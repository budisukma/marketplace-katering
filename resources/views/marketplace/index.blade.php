@extends('layouts.app')
@section('title','Cari Katering')
@section('content')
<section style="padding:34px 0 26px"><span class="pill">MARKETPLACE KATERING</span><h1 style="margin-top:14px">Makanan kantor, lebih mudah.</h1><p style="max-width:650px">Temukan penyedia katering, bandingkan menu, lalu pesan sesuai kebutuhan kantor Anda.</p>
<form class="card row" method="GET" action="{{ route('home') }}" style="margin-top:22px">
<div style="flex:1;min-width:180px"><label>Lokasi</label><input name="location" placeholder="Contoh: Jambi" value="{{ request('location') }}"></div>
<div style="flex:1;min-width:180px"><label>Jenis makanan</label><input name="food_type" placeholder="Contoh: Nusantara" value="{{ request('food_type') }}"></div>
<button class="btn" style="align-self:end">Cari katering</button>
</form></section>
<div class="row" style="justify-content:space-between;margin:25px 0"><h2>Penyedia katering</h2><span class="muted">{{ $merchants->total() }} penyedia ditemukan</span></div>
<div class="grid">
@forelse($merchants as $merchant)
<div class="card"><div class="row" style="justify-content:space-between"><span class="pill">{{ $merchant->food_type ?: 'Beragam menu' }}</span><span class="muted">{{ $merchant->location ?: 'Lokasi belum diisi' }}</span></div>
<h2 style="margin-top:14px">{{ $merchant->company_name }}</h2><p>{{ \Illuminate\Support\Str::limit($merchant->description ?: 'Penyedia katering untuk kebutuhan kantor.', 115) }}</p>
<p class="muted">{{ $merchant->menus->count() }} menu tersedia</p><a class="btn" href="{{ route('marketplace.show',$merchant) }}">Lihat menu</a></div>
@empty<div class="card"><h2>Belum ada penyedia katering</h2><p>Merchant yang mendaftar dan menambahkan menu akan muncul di sini.</p><a href="{{ route('register') }}" class="btn">Daftar sebagai merchant</a></div>@endforelse
</div><div style="margin-top:22px">{{ $merchants->links() }}</div>
@endsection
