@extends('layouts.app')
@section('title','Profil Merchant')
@section('content')
<div class="card" style="max-width:760px"><h1>Profil perusahaan</h1><p>Lengkapi informasi agar kantor mudah menemukan katering Anda.</p>
<form method="POST" action="{{ route('merchant.profile.update') }}">@csrf @method('PUT')
<div class="field"><label>Nama perusahaan</label><input name="company_name" value="{{ old('company_name',$merchant->company_name) }}" required></div>
<div class="field"><label>Alamat</label><input name="address" value="{{ old('address',$merchant->address) }}"></div>
<div class="grid"><div class="field"><label>Nomor telepon</label><input name="phone" value="{{ old('phone',$merchant->phone) }}"></div><div class="field"><label>Kota/lokasi</label><input name="location" value="{{ old('location',$merchant->location) }}"></div></div>
<div class="field"><label>Jenis makanan</label><input name="food_type" value="{{ old('food_type',$merchant->food_type) }}" placeholder="Nusantara, Padang, Vegetarian..."></div>
<div class="field"><label>Deskripsi</label><textarea name="description">{{ old('description',$merchant->description) }}</textarea></div><button class="btn">Simpan profil</button>
</form></div>
@endsection
