<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // CREATE: membuat pesanan baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'delivery_date' => 'required|date|after_or_equal:today',
            'delivery_address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:0|max:10000',
        ]);

        $selected = collect($data['quantities'])
            ->filter(function ($qty) {
                return (int) $qty > 0;
            });

        if ($selected->isEmpty()) {
            return back()->withInput()->withErrors([
                'quantities' => 'Pilih minimal satu menu dengan jumlah lebih dari 0.',
            ]);
        }

        $order = DB::transaction(function () use ($data, $selected) {
            $merchant = Merchant::findOrFail($data['merchant_id']);

            $menus = Menu::where('merchant_id', $merchant->id)
                ->where('is_available', true)
                ->whereIn('id', $selected->keys())
                ->get()
                ->keyBy('id');

            if ($menus->count() !== $selected->count()) {
                abort(422, 'Ada menu yang tidak valid atau tidak tersedia.');
            }

            $total = 0;

            foreach ($selected as $id => $qty) {
                $total += $menus[$id]->price * (int) $qty;
            }

            $order = Order::create([
                'customer_id' => auth()->id(),
                'merchant_id' => $merchant->id,
                'delivery_date' => $data['delivery_date'],
                'delivery_address' => $data['delivery_address'],
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
                'total' => $total,
            ]);

            foreach ($selected as $id => $qty) {
                $menu = $menus[$id];
                $quantity = (int) $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'quantity' => $quantity,
                    'unit_price' => $menu->price,
                    'subtotal' => $menu->price * $quantity,
                ]);
            }

            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV-' . now()->format('Ymd')
                    . '-' . strtoupper(Str::random(6)),
                'issued_at' => now(),
            ]);

            return $order;
        });

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    // READ: melihat detail pesanan
    public function show(Order $order)
    {
        $this->authorizeCustomerOrder($order);

        $order->load(['merchant', 'items', 'invoice']);

        return view('customer.orders.show', compact('order'));
    }

    // Menampilkan form edit beserta pilihan menu katering
    // Menampilkan form edit beserta data pesanan sebelumnya
    public function edit(Order $order)
    {
        $this->authorizeCustomerOrder($order);

        abort_unless(
            $order->status === 'pending',
            403,
            'Pesanan yang sudah diproses tidak bisa diedit.'
        );

        // Muat relasi pesanan, katering, dan item lama
        $order->load(['merchant', 'items']);

        // Ambil ID menu yang pernah dipesan
        $existingMenuIds = $order->items
            ->pluck('menu_id')
            ->filter()
            ->map(function ($id) {
                return (int) $id;
            })
            ->values()
            ->all();

        // Tampilkan menu yang tersedia dan menu lama,
        // meskipun menu lama sekarang sudah tidak tersedia.
        $menus = Menu::where('merchant_id', $order->merchant_id)
            ->where(function ($query) use ($existingMenuIds) {
                $query->where('is_available', true);

                if (!empty($existingMenuIds)) {
                    $query->orWhereIn('id', $existingMenuIds);
                }
            })
            ->orderBy('name')
            ->get();

        // Isi jumlah berdasarkan pesanan yang sudah tersimpan
        $quantities = [];

        foreach ($order->items as $item) {
            if ($item->menu_id !== null) {
                $quantities[(int) $item->menu_id] = (int) $item->quantity;
            }
        }

        return view('customer.orders.edit', compact(
            'order',
            'menus',
            'quantities'
        ));
    }

    // UPDATE: menambah, mengubah, dan menghapus pilihan menu
    public function update(Request $request, Order $order)
    {
        $this->authorizeCustomerOrder($order);

        abort_unless(
            $order->status === 'pending',
            403,
            'Pesanan yang sudah diproses tidak bisa diedit.'
        );

        $data = $request->validate([
            'delivery_date' => 'required|date|after_or_equal:today',
            'delivery_address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:0|max:10000',
        ]);

        $items = $order->items()->get()->keyBy('menu_id');

        $existingMenuIds = $items->keys()
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();

        // Menu yang boleh dipilih: tersedia atau sudah ada di pesanan.
        $allowedMenus = Menu::where('merchant_id', $order->merchant_id)
            ->where(function ($query) use ($existingMenuIds) {
                $query->where('is_available', true);

                if (!empty($existingMenuIds)) {
                    $query->orWhereIn('id', $existingMenuIds);
                }
            })
            ->get()
            ->keyBy('id');

        $submittedQuantities = collect($data['quantities']);

        // Tolak ID menu yang bukan milik katering pesanan ini.
        foreach ($submittedQuantities as $menuId => $qty) {
            if (!$allowedMenus->has((int) $menuId)) {
                return back()->withInput()->withErrors([
                    'quantities' => 'Ada menu yang tidak valid untuk katering ini.',
                ]);
            }
        }

        $selected = $submittedQuantities->filter(function ($qty) {
            return (int) $qty > 0;
        });

        if ($selected->isEmpty()) {
            return back()->withInput()->withErrors([
                'quantities' => 'Pilih minimal satu menu dengan jumlah lebih dari 0.',
            ]);
        }

        DB::transaction(function () use (
            $order,
            $data,
            $items,
            $allowedMenus,
            $submittedQuantities
        ) {
            $total = 0;

            // Ubah atau hapus menu yang sudah ada di pesanan.
            foreach ($items as $menuId => $item) {
                $qty = (int) ($submittedQuantities[$menuId] ?? 0);

                if ($qty <= 0) {
                    $item->delete();
                    continue;
                }

                $item->quantity = $qty;
                $item->subtotal = $item->unit_price * $qty;
                $item->save();

                $total += $item->subtotal;
            }

            // Tambahkan menu baru yang dipilih customer.
            foreach ($submittedQuantities as $menuId => $qty) {
                $menuId = (int) $menuId;
                $qty = (int) $qty;

                if ($qty <= 0 || $items->has($menuId)) {
                    continue;
                }

                $menu = $allowedMenus->get($menuId);

                if (!$menu || !$menu->is_available) {
                    continue;
                }

                $subtotal = $menu->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'quantity' => $qty,
                    'unit_price' => $menu->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update([
                'delivery_date' => $data['delivery_date'],
                'delivery_address' => $data['delivery_address'],
                'notes' => $data['notes'] ?? null,
                'total' => $total,
            ]);
        });

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Pesanan berhasil diperbarui.');
    }

    // DELETE: membatalkan pesanan, bukan menghapus riwayat
    public function cancel(Order $order)
    {
        $this->authorizeCustomerOrder($order);

        abort_unless(
            $order->status === 'pending',
            403,
            'Hanya pesanan pending yang bisa dibatalkan.'
        );

        $order->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    // Memastikan pesanan milik customer yang login
    private function authorizeCustomerOrder(Order $order)
    {
        abort_unless(
            (int) $order->customer_id === (int) auth()->id(),
            403,
            'Anda tidak memiliki akses ke pesanan ini.'
        );
    }
}
