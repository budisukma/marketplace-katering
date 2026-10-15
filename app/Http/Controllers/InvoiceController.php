<?php
namespace App\Http\Controllers;

use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'order.customer',
            'order.merchant.user',
            'order.items'
        ]);

        $order = $invoice->order;
        $user = auth()->user();

        abort_unless(
            $user->id === $order->customer_id ||
            (
                $user->role === 'merchant' &&
                $user->merchantProfile &&
                $user->merchantProfile->id === $order->merchant_id
            ),
            403
        );

        return view('invoices.show', compact('invoice', 'order'));
    }
}
