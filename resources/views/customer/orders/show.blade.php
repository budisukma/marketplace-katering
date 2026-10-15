@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')

<style>
    .order-page {
        max-width: 1000px;
        margin: 25px auto;
        padding: 0 16px 40px;
        color: #253047;
    }

    .order-hero {
        background: linear-gradient(135deg, #123c35, #23745d);
        color: white;
        border-radius: 18px;
        padding: 28px;
        margin-bottom: 22px;
    }

    .order-hero h1 {
        margin: 10px 0;
        font-size: 27px;
    }

    .order-hero p {
        margin: 6px 0;
        color: #e0f2e9;
    }

    .order-layout {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 20px;
        align-items: start;
    }

    .order-card {
        background: #fff;
        border: 1px solid #e8edf0;
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 18px;
        box-shadow: 0 5px 20px rgba(20, 40, 35, 0.04);
    }

    .order-card h2 {
        font-size: 18px;
        margin: 0 0 18px;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #edf0f2;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .item-name {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .muted {
        color: #7b8794;
        font-size: 13px;
    }

    .order-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-top: 18px;
        margin-top: 8px;
        border-top: 1px dashed #d7dfe2;
    }

    .total-price {
        color: #16734e;
        font-size: 23px;
        font-weight: 750;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding: 12px 0;
        border-bottom: 1px solid #edf0f2;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .status-label {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 20px;
        background: #fff2d7;
        color: #8a5b00;
        font-size: 13px;
        font-weight: 700;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-complete {
        background: #dcfce7;
        color: #166534;
    }

    .order-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .order-actions .btn {
        text-decoration: none;
        cursor: pointer;
    }

    .btn-secondary {
        display: inline-block;
        padding: 10px 16px;
        border: 1px solid #d7dfe2;
        border-radius: 8px;
        background: white;
        color: #253047;
        text-decoration: none;
    }

    @media (max-width: 700px) {
        .order-layout {
            grid-template-columns: 1fr;
        }

        .order-hero {
            padding: 22px;
        }

        .order-hero h1 {
            font-size: 23px;
        }

        .order-card {
            padding: 17px;
        }

        .total-price {
            font-size: 19px;
        }
    }
</style>

<div class="order-page">

    {{-- Header konfirmasi --}}
    <div class="order-hero">
        <div style="font-size:32px">✓</div>

        <p>DETAIL PEMESANAN</p>

        <h1>Pesanan berhasil dibuat!</h1>

        <p>
            Terima kasih telah memesan. Berikut ringkasan pesanan katering Anda.
        </p>

        <div style="margin-top:18px">
            <span style="opacity:.85">Nomor pesanan</span>
            <div style="font-size:20px;font-weight:700">
                #{{ $order->id }}
            </div>
        </div>
    </div>

    <div class="order-layout">

        {{-- Daftar menu --}}
        <div>
            <div class="order-card">
                <h2>🛍️ Menu yang Dipesan</h2>

                @forelse($order->items as $item)
                <div class="order-item">
                    <div>
                        <div class="item-name">
                            {{ $item->menu_name }}
                        </div>

                        <div class="muted">
                            {{ $item->quantity }} porsi
                            × Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                        </div>
                    </div>

                    <div style="font-weight:700;white-space:nowrap">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <p class="muted">Belum ada detail menu pada pesanan ini.</p>
                @endforelse

                <div class="order-total">
                    <div>
                        <div class="muted">Total pembayaran</div>
                        <strong>Total pesanan</strong>
                    </div>

                    <div class="total-price">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Informasi katering --}}
            <div class="order-card">
                <h2>🏪 Informasi Katering</h2>

                <div class="detail-row">
                    <span class="muted">Nama katering</span>
                    <strong>
                        {{ $order->merchant->company_name ?? 'Katering' }}
                    </strong>
                </div>
            </div>
        </div>

        {{-- Detail pengiriman dan status --}}
        <div>
            <div class="order-card">
                <h2>📦 Detail Pengiriman</h2>

                <div class="detail-row">
                    <span class="muted">Tanggal pengiriman</span>
                    <strong>
                        {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}
                    </strong>
                </div>

                <div class="detail-row">
                    <span class="muted">Alamat</span>
                    <strong style="text-align:right;max-width:60%">
                        {{ $order->delivery_address }}
                    </strong>
                </div>

                <div class="detail-row">
                    <span class="muted">Catatan</span>
                    <span style="text-align:right;max-width:60%">
                        {{ $order->notes ?: 'Tidak ada catatan' }}
                    </span>
                </div>

                <div style="margin-top:20px">
                    <div class="muted" style="margin-bottom:8px">
                        Status pesanan
                    </div>

                    @if($order->status === 'pending')
                    <span class="status-label">Menunggu konfirmasi</span>
                    @elseif($order->status === 'cancelled')
                    <span class="status-label status-cancelled">Dibatalkan</span>
                    @elseif(in_array($order->status, ['completed', 'delivered']))
                    <span class="status-label status-complete">
                        {{ ucfirst($order->status) }}
                    </span>
                    @else
                    <span class="status-label">
                        {{ ucfirst($order->status) }}
                    </span>
                    @endif
                </div>

                <div class="order-actions">
                    <a href="{{ route('customer.dashboard') }}" class="btn">
                        Pesanan Saya
                    </a>

                    <a href="{{ route('home') }}" class="btn-secondary">
                        Kembali ke Marketplace
                    </a>
                </div>
            </div>

            {{-- Aksi pesanan --}}
            @if($order->status === 'pending')
            <div class="order-card">
                <h2>⚙️ Kelola Pesanan</h2>

                <p class="muted">
                    Pesanan masih menunggu konfirmasi. Anda masih bisa
                    mengubah detail atau membatalkannya.
                </p>

                <div class="order-actions">
                    <a href="{{ route('customer.orders.edit', $order) }}"
                        class="btn">
                        Edit Pesanan
                    </a>

                    <form
                        action="{{ route('customer.orders.cancel', $order) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">

                        @csrf
                        @method('PATCH')

                        <button type="submit"
                            class="btn"
                            style="background:#b91c1c;border-color:#b91c1c">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Invoice --}}
            @if($order->invoice)
            <div class="order-card">
                <h2>🧾 Invoice</h2>

                <p class="muted">
                    Invoice untuk pesanan ini sudah tersedia.
                </p>

                <a href="{{ route('invoices.show', $order->invoice) }}"
                    class="btn">
                    Lihat Invoice
                </a>
            </div>
            @endif
        </div>

    </div>
</div>

@endsection