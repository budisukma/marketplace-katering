<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MerchantController extends Controller
{
    private function profile()
    {
        return auth()->user()->merchantProfile()->firstOrFail();
    }

    public function dashboard()
    {
        $merchant = $this->profile();
        $merchant->load('menus');
        $orders = Order::with(['customer', 'items', 'invoice'])->where('merchant_id', $merchant->id)->latest()->get();
        return view('merchant.dashboard', compact('merchant', 'orders'));
    }

    public function editProfile()
    {
        return view('merchant.profile', ['merchant' => $this->profile()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:160',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'description' => 'nullable|string|max:2000',
            'location' => 'nullable|string|max:120',
            'food_type' => 'nullable|string|max:120',
        ]);
        $this->profile()->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function storeMenu(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:160',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) $data['photo'] = $request->file('photo')->store('menus', 'public');
        $this->profile()->menus()->create($data);
        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function updateMenu(Request $request, Menu $menu)
    {
        abort_unless($menu->merchant_id === $this->profile()->id, 403);
        $data = $request->validate([
            'name' => 'required|string|max:160',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
        ]);
        if ($request->hasFile('photo')) {
            if ($menu->photo) Storage::disk('public')->delete($menu->photo);
            $data['photo'] = $request->file('photo')->store('menus', 'public');
        }
        $data['is_available'] = $request->boolean('is_available');
        $menu->update($data);
        return back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function deleteMenu(Menu $menu)
    {
        abort_unless($menu->merchant_id === $this->profile()->id, 403);
        $menu->delete();
        return back()->with('success', 'Menu berhasil dihapus.');
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        abort_unless($order->merchant_id === $this->profile()->id, 403);
        $data = $request->validate(['status' => 'required|in:pending,confirmed,preparing,delivered,cancelled']);
        $order->update($data);
        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
