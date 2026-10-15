<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Merchant::with('menus')
            ->whereHas('menus', function ($q) {
                $q->where('is_available', true);
            });

        if ($request->filled('location')) {
            $query->where(
                'location',
                'like',
                '%' . $request->input('location') . '%'
            );
        }

        if ($request->filled('food_type')) {
            $query->where(
                'food_type',
                'like',
                '%' . $request->input('food_type') . '%'
            );
        }

        $merchants = $query->latest()
            ->paginate(9)
            ->withQueryString();

        return view('marketplace.index', compact('merchants'));
    }

    public function show(Merchant $merchant)
    {
        $merchant->load([
            'menus' => function ($q) {
                $q->where('is_available', true);
            }
        ]);

        return view('marketplace.show', compact('merchant'));
    }

    public function customerDashboard()
    {
        $orders = \App\Models\Order::with([
            'merchant',
            'invoice',
            'items'
        ])
            ->where('customer_id', auth()->id())
            ->latest()
            ->get();

        return view('customer.dashboard', compact('orders'));
    }
}
