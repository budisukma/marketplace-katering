@extends('layouts.app')
@section('title','Invoice '.$invoice->invoice_number)
@section('content')
<div class="row" style="justify-content:space-between;margin-bottom:18px"><a href="{{ url()->previous() }}">← Kembali</a><button class="btn" onclick="window.print()">Cetak invoice</button></div>
<div class="card" style="max-width:820px;margin:auto;padding:30px" id="invoice">
    <div class="row" style="justify-content:space-between;align-items:flex-start">
        <div><span class="pill">INVOICE PEMESANAN</span>
            <h1 style="margin-top:12px">KateringKita</h1>
            <p>Marketplace Katering</p>
        </div>
        <div style="text-align:right">
            <h2>{{ $invoice->invoice_number }}</h2>
            <p>Tanggal: {{ \Carbon\Carbon::parse($invoice->issued_at)->format('d/m/Y H:i') }}</p><span class="pill">{{ ucfirst($order->status) }}</span>
        </div>
    </div>
    <hr style="border:0;border-top:1px solid #e4e7ec;margin:24px 0">
    <div class="grid">
        <div><label>Ditagihkan kepada</label><strong>{{ $order->customer->name }}</strong>
            <p>{{ $order->delivery_address }}</p>
        </div>
        <div><label>Penyedia katering</label><strong>{{ $order->merchant->company_name }}</strong>
            <p>{{ $order->merchant->address }}</p>
        </div>
    </div>
    <table class="table" style="margin-top:25px">
        <thead>
            <tr>
                <th>Menu</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>@foreach($order->items as $item)<tr>
                <td>{{ $item->menu_name }}</td>
                <td>Rp {{ number_format($item->unit_price,0,',','.') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
            </tr>@endforeach</tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right">TOTAL</th>
                <th>Rp {{ number_format($order->total,0,',','.') }}</th>
            </tr>
        </tfoot>
    </table>
    <div style="margin-top:22px"><label>Tanggal pengiriman</label>
        <p>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p><label>Catatan</label>
        <p>{{ $order->notes ?: 'Tidak ada catatan.' }}</p>
    </div>
    <hr style="border:0;border-top:1px solid #e4e7ec">
    <p style="text-align:center">Terima kasih. Invoice ini dibuat secara otomatis oleh Marketplace Katering.</p>
</div>
<style>
    @media print {

        header,
        footer,
        .wrap>.row,
        .btn {
            display: none !important
        }

        body {
            background: #fff
        }

        .wrap {
            margin: 0;
            padding: 0
        }

        .card {
            box-shadow: none;
            border: 0
        }
    }
</style>
@endsection