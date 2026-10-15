@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container">

    <h1>Pesanan Saya</h1>
    <p>Lihat, edit, dan kelola pesanan katering Anda.</p>

    {{-- Pesan berhasil --}}
   
    <div class="card" style="margin-bottom:15px;color:green">
        {{ session('success') }}
    </div>
   

    {{-- Pesan kesalahan --}}
    @if($errors->any())
    <div class="card" style="margin-bottom:15px;color:#b91c1c">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Tombol membuat pesanan baru --}}
    <div style="margin:20px 0">
        <a href="{{ route('home') }}" class="btn">
            + Cari Katering / Pesan Menu
        </a>
    </div>

    <hr>

    <h2>Riwayat Pesanan</h2>

    @forelse($orders as $order)

    <div class="card" style="margin-bottom:20px;padding:20px">

        {{-- Informasi pesanan --}}
        <div style="display:flex;justify-content:space-between;
                        align-items:center;flex-wrap:wrap;gap:10px">

            <div>
                <h3>
                    {{ $order->merchant->company_name ?? 'Katering' }}
                </h3>

                <p>
                    Nomor pesanan: #{{ $order->id }}
                </p>

                <p>
                    Tanggal pengiriman:
                    {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                </p>
            </div>

            <div>
                <strong>Status:</strong>

                @if($order->status === 'pending')
                <span class="pill">Menunggu konfirmasi</span>
                @elseif($order->status === 'cancelled')
                <span class="pill">Dibatalkan</span>
                @else
                <span class="pill">
                    {{ ucfirst($order->status) }}
                </span>
                @endif
            </div>

        </div>

        <hr>

        {{-- Daftar menu pesanan --}}
        <h4>Menu yang Dipesan</h4>

        @forelse($order->items as $item)
        <div style="display:flex;justify-content:space-between;
                            gap:10px;flex-wrap:wrap;margin-bottom:8px">

            <span>
                {{ $item->menu_name }}
                × {{ $item->quantity }}
            </span>

            <span>
                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
            </span>

        </div>
        @empty
        <p>Belum ada detail menu pada pesanan ini.</p>
        @endforelse

        <hr>

        {{-- Total harga --}}
        <h3>
            Total:
            Rp {{ number_format($order->total, 0, ',', '.') }}
        </h3>

        {{-- Tombol CRUD --}}
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:18px">

            {{-- READ: melihat detail --}}
            <a href="{{ route('customer.orders.show', $order) }}"
                class="btn">
                Lihat Pesanan
            </a>

            {{-- Invoice --}}
            @if($order->invoice)
            <a href="{{ route('invoices.show', $order->invoice) }}"
                class="btn">
                Lihat Invoice
            </a>
            @endif

            {{-- UPDATE dan DELETE hanya untuk pending --}}
            @if($order->status === 'pending')

            <a href="{{ route('customer.orders.edit', $order) }}"
                class="btn">
                Edit Pesanan
            </a>

            {{-- CANCEL --}}
            <form
                action="{{ route('customer.orders.cancel', $order) }}"
                method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">

                @csrf
                @method('PATCH')

                <button type="submit" class="btn">
                    Batalkan Pesanan
                </button>

            </form>

            @endif

        </div>

    </div>

    @empty

    <div class="card" style="padding:25px">
        <h3>Belum Ada Pesanan</h3>

        <p>
            Anda belum memiliki pesanan katering.
            Silakan cari katering dan pilih menu yang diinginkan.
        </p>

        <a href="{{ route('home') }}" class="btn">
            Mulai Pesan Katering
        </a>
    </div>

    @endforelse

</div>
@endsection