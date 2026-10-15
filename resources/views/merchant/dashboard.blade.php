@extends('layouts.app')

@section('title', 'Dashboard Merchant')

@section('content')

@if ($errors->any())
    <div class="card" style="margin-bottom:16px; color:red">
        <strong>Menu gagal diproses:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row" style="justify-content:space-between">
    <div>
        <h1>Dashboard Merchant</h1>
        <p>Kelola profil, menu, dan pesanan perusahaan Anda.</p>
    </div>
    <a class="btn secondary" href="{{ route('merchant.profile.edit') }}">Edit profil</a>
</div>

<div class="grid" style="margin:22px 0">
    <div class="card">
        <p>Total menu</p>
        <h1>{{ $merchant->menus->count() }}</h1>
    </div>
    <div class="card">
        <p>Total pesanan</p>
        <h1>{{ $orders->count() }}</h1>
    </div>
    <div class="card">
        <p>Menunggu konfirmasi</p>
        <h1>{{ $orders->where('status', 'pending')->count() }}</h1>
    </div>
</div>

<div class="card" style="margin-bottom:22px">
    <h2>Tambah menu baru</h2>

    <form method="POST" enctype="multipart/form-data" action="{{ route('merchant.menus.store') }}">
        @csrf

        <div class="grid">
            <div class="field">
                <label>Nama menu</label>
                <input name="name" required>
            </div>
            <div class="field">
                <label>Harga (Rp)</label>
                <input type="number" min="0" step="500" name="price" required>
            </div>
        </div>

        <div class="field">
            <label>Deskripsi</label>
            <textarea name="description" required></textarea>
        </div>

        <div class="field">
            <label>Foto menu (maks. 2 MB)</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <button type="submit" class="btn">Tambah menu</button>
    </form>
</div>

<h2>Daftar menu</h2>

<div class="grid" style="margin-bottom:28px">
    @forelse($merchant->menus as $menu)
        <div class="card">

            @if($menu->photo)
                <img class="menu-img"
                     src="{{ asset('storage/'.$menu->photo) }}"
                     alt="{{ $menu->name }}">
            @endif

            <form method="POST" action="{{ route('merchant.menus.update', $menu) }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label>Nama menu</label>
                    <input name="name" value="{{ $menu->name }}" required>
                </div>

                <div class="field">
                    <label>Deskripsi</label>
                    <textarea name="description" required>{{ $menu->description }}</textarea>
                </div>

                <div class="field">
                    <label>Harga</label>
                    <input type="number" min="0" name="price" value="{{ $menu->price }}" required>
                </div>

                <div class="field">
                    <label>Ganti foto (opsional)</label>
                    <input type="file" name="photo" accept="image/*">
                </div>

                <label>
                    <input style="width:auto"
                           type="checkbox"
                           name="is_available"
                           value="1"
                           {{ $menu->is_available ? 'checked' : '' }}>
                    Tersedia
                </label>

                <button type="submit" class="btn" style="margin-top:12px">
                    Simpan perubahan
                </button>
            </form>

            <form method="POST"
                  action="{{ route('merchant.menus.delete', $menu) }}"
                  onsubmit="return confirm('Hapus menu ini?')"
                  style="margin-top:10px">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn danger">Hapus menu</button>
            </form>
        </div>
    @empty
        <p>Belum ada menu. Tambahkan menu pertama Anda di atas.</p>
    @endforelse
</div>

<h2>Pesanan masuk</h2>

@forelse($orders as $order)
    <div class="card" style="margin:12px 0">

        <div class="row" style="justify-content:space-between">
            <div>
                <strong>Pesanan #{{ $order->id }}</strong>
                <p>
                    {{ $order->customer->name }}
                    · {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                </p>
            </div>

            <span class="pill">{{ ucfirst($order->status) }}</span>
        </div>

        <ul>
            @foreach($order->items as $item)
                <li>
                    {{ $item->menu_name }} × {{ $item->quantity }}
                    — Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </li>
            @endforeach
        </ul>

        <strong>Total: Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
        · <a href="{{ route('invoices.show', $order->invoice) }}">Lihat invoice</a>

        <form method="POST"
              action="{{ route('merchant.orders.status', $order) }}"
              class="row"
              style="margin-top:14px">
            @csrf
            @method('PATCH')

            <select name="status" style="max-width:230px">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>
                    Pending
                </option>
                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>
                    Confirmed
                </option>
                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>
                    Preparing
                </option>
                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>
                    Delivered
                </option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                    Cancelled
                </option>
            </select>

            <button type="submit" class="btn">Perbarui status</button>
        </form>

    </div>
@empty
    <div class="card">
        <p>Belum ada pesanan masuk.</p>
    </div>
@endforelse

@endsection