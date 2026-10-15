@extends('layouts.app')

@section('title', 'Edit Pesanan')

@section('content')

<style>
    .edit-page {
        max-width: 1000px;
        margin: 28px auto;
        padding: 0 16px 40px;
        color: #253047;
    }

    .edit-header {
        background: linear-gradient(135deg, #123c35, #23745d);
        color: white;
        padding: 28px;
        border-radius: 18px;
        margin-bottom: 24px;
    }

    .edit-header h1 {
        font-size: 27px;
        margin: 10px 0;
    }

    .edit-header p {
        color: #e0f2e9;
        margin: 5px 0;
    }

    .edit-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 20px;
        align-items: start;
    }

    .edit-card {
        background: #fff;
        border: 1px solid #e8edf0;
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 18px;
        box-shadow: 0 5px 20px rgba(20, 40, 35, .04);
    }

    .edit-card h2 {
        font-size: 18px;
        margin: 0 0 8px;
    }

    .edit-muted {
        color: #7b8794;
        font-size: 13px;
        line-height: 1.6;
    }

    .menu-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 17px 0;
        border-bottom: 1px solid #edf0f2;
    }

    .menu-option:last-child {
        border-bottom: none;
    }

    .menu-name {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .menu-price {
        color: #16734e;
        font-weight: 700;
        margin-top: 6px;
    }

    .quantity-input {
        width: 80px;
        padding: 10px;
        border: 1px solid #d7dfe2;
        border-radius: 9px;
        font-size: 15px;
        text-align: center;
    }

    .edit-field {
        margin-top: 18px;
    }

    .edit-field label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .edit-field input,
    .edit-field textarea {
        width: 100%;
        padding: 12px 13px;
        border: 1px solid #d7dfe2;
        border-radius: 9px;
        box-sizing: border-box;
        font: inherit;
        background: #fff;
    }

    .edit-field input:focus,
    .edit-field textarea:focus {
        outline: none;
        border-color: #23745d;
        box-shadow: 0 0 0 3px rgba(35, 116, 93, .10);
    }

    .edit-notice {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 13px 15px;
        border-radius: 10px;
        font-size: 13px;
        margin-top: 18px;
    }

    .edit-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .edit-secondary {
        display: inline-block;
        padding: 11px 16px;
        border: 1px solid #d7dfe2;
        border-radius: 9px;
        background: white;
        color: #253047;
        text-decoration: none;
    }

    .edit-error {
        color: #b91c1c;
        font-size: 13px;
        margin-top: 6px;
    }

    @media (max-width: 700px) {
        .edit-grid {
            grid-template-columns: 1fr;
        }

        .edit-header {
            padding: 22px;
        }

        .edit-header h1 {
            font-size: 23px;
        }

        .edit-card {
            padding: 17px;
        }
    }
</style>

<div class="edit-page">

    <div class="edit-header">
        <div style="font-size:30px">✏️</div>
        <p>KELOLA PESANAN</p>
        <h1>Edit Pesanan #{{ $order->id }}</h1>
        <p>
            Ubah jumlah menu, tanggal pengiriman, atau alamat sesuai kebutuhan.
        </p>
    </div>

    @if(session('success'))
    <div class="edit-notice">
        {{ session('success') }}
    </div>
    @endif


    <form action="{{ route('customer.orders.update', $order) }}"
        method="POST">

        @csrf
        @method('PUT')

        <div class="edit-grid">

            {{-- Pilihan menu --}}
            <div class="edit-card">
                <h2>🍱 Pilih Menu</h2>
                <p class="edit-muted">
                    Atur jumlah porsi yang ingin dipesan. Isi 0 jika tidak ingin
                    memesan menu tersebut.
                </p>

                @forelse($menus as $menu)
                <div class="menu-option">
                    <div style="flex:1">
                        <div class="menu-name">
                            {{ $menu->name }}
                        </div>

                        <div class="edit-muted">
                            Menu dari
                            {{ $order->merchant->company_name ?? 'katering pilihan Anda' }}
                        </div>

                        <div class="menu-price">
                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                            <span class="edit-muted">/ porsi</span>
                        </div>
                    </div>

                    <div style="text-align:center">
                        <label for="quantity-{{ $menu->id }}"
                            class="edit-muted"
                            style="display:block;margin-bottom:7px">
                            Jumlah
                        </label>

                        <input
                            id="quantity-{{ $menu->id }}"
                            class="quantity-input"
                            type="number"
                            name="quantities[{{ $menu->id }}]"
                            min="0"
                            value="{{ old('quantities.' . $menu->id, isset($quantities[$menu->id]) ? $quantities[$menu->id] : 0) }}">
                    </div>
                </div>
                @empty
                <p class="edit-muted">
                    Belum ada menu tersedia dari katering ini.
                </p>
                @endforelse

                <div class="edit-notice">
                    Pastikan jumlah porsi sudah benar sebelum menyimpan perubahan.
                </div>
            </div>

            {{-- Informasi pengiriman --}}
            <div>
                <div class="edit-card">
                    <h2>📦 Pengiriman</h2>
                    <p class="edit-muted">
                        Pastikan tanggal dan alamat pengiriman benar.
                    </p>

                    <div class="edit-field">
                        <label for="delivery_date">Tanggal Pengiriman</label>

                        <input
                            id="delivery_date"
                            type="date"
                            name="delivery_date"
                            min="{{ date('Y-m-d') }}"
                            value="{{ old('delivery_date', \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d')) }}"
                            required>

                        @error('delivery_date')
                        <div class="edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="edit-field">
                        <label for="delivery_address">Alamat Pengiriman</label>

                        <textarea
                            id="delivery_address"
                            name="delivery_address"
                            rows="4"
                            placeholder="Masukkan alamat lengkap pengiriman"
                            required>{{ old('delivery_address', $order->delivery_address) }}</textarea>

                        @error('delivery_address')
                        <div class="edit-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="edit-field">
                        <label for="notes">Catatan Pesanan</label>

                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            placeholder="Contoh: makanan dikirim sebelum jam 12 siang">{{ old('notes', $order->notes) }}</textarea>

                        @error('notes')
                        <div class="edit-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="edit-card">
                    <h2>Ringkasan Pesanan</h2>

                    <div class="edit-muted">
                        Nomor pesanan
                    </div>

                    <div style="font-size:22px;font-weight:750;margin:5px 0 15px">
                        #{{ $order->id }}
                    </div>

                    <div class="edit-muted">
                        Katering
                    </div>

                    <div style="font-weight:600;margin-top:5px">
                        {{ $order->merchant->company_name ?? 'Katering' }}
                    </div>

                    <div class="edit-actions">
                        <button type="submit" class="btn">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('customer.orders.show', $order) }}"
                            class="edit-secondary">
                            Kembali
                        </a>
                    </div>

                    <p class="edit-muted" style="margin-top:15px">
                        Total pesanan akan dihitung ulang setelah perubahan disimpan.
                    </p>
                </div>
            </div>

        </div>
    </form>

</div>

@endsection