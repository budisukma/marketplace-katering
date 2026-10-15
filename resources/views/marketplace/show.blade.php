@extends('layouts.app')
@section('title',$merchant->company_name)
@section('content')
<a href="{{ route('home') }}">← Kembali ke marketplace</a>
<div class="card" style="margin:18px 0 24px"><span class="pill">{{ $merchant->food_type ?: 'Katering' }}</span><h1 style="margin-top:12px">{{ $merchant->company_name }}</h1><p>{{ $merchant->description }}</p><p>📍 {{ $merchant->address ?: $merchant->location ?: 'Alamat belum tersedia' }} · ☎ {{ $merchant->phone ?: 'Kontak belum tersedia' }}</p></div>
<h2>Pilih menu</h2>
@if(auth()->check() && auth()->user()->role==='customer')
<form method="POST" action="{{ route('customer.orders.store') }}">@csrf<input type="hidden" name="merchant_id" value="{{ $merchant->id }}">
<div class="grid">
@foreach($merchant->menus as $menu)
<div class="card">
@if($menu->photo)<img class="menu-img" src="{{ asset('storage/'.$menu->photo) }}" alt="{{ $menu->name }}">@else<div class="menu-img" style="display:grid;place-items:center;font-size:44px">🍱</div>@endif
<h2 style="margin-top:12px">{{ $menu->name }}</h2><p>{{ $menu->description }}</p><div class="row" style="justify-content:space-between"><span class="price">Rp {{ number_format($menu->price,0,',','.') }}</span><div style="width:85px"><label>Jumlah</label><input type="number" min="0" max="10000" name="quantities[{{ $menu->id }}]" value="0"></div></div>
</div>
@endforeach
</div>
@if($merchant->menus->count())
<div class="card" style="margin-top:20px"><h2>Detail pengiriman</h2><div class="grid"><div class="field"><label>Tanggal pengiriman</label><input type="date" name="delivery_date" min="{{ now()->toDateString() }}" required></div><div class="field"><label>Alamat pengiriman</label><input name="delivery_address" required placeholder="Alamat kantor"></div></div><div class="field"><label>Catatan (opsional)</label><textarea name="notes" placeholder="Catatan alergi, jam pengiriman, dan sebagainya"></textarea></div><button class="btn">Buat pesanan & invoice</button></div>
@endif</form>
@elseif(!auth()->check())
<p><a class="btn" href="{{ route('login') }}">Masuk sebagai Customer untuk memesan</a></p>
@else<p class="muted">Akun Merchant tidak dapat melakukan pemesanan. Gunakan akun Customer.</p>@endif
@if(!auth()->check() || auth()->user()->role!=='customer')
<div class="grid">@foreach($merchant->menus as $menu)<div class="card">@if($menu->photo)<img class="menu-img" src="{{ asset('storage/'.$menu->photo) }}" alt="{{ $menu->name }}">@endif<h2>{{ $menu->name }}</h2><p>{{ $menu->description }}</p><span class="price">Rp {{ number_format($menu->price,0,',','.') }}</span></div>@endforeach</div>
@endif
@endsection
