@extends('layouts.app')
@section('title','Daftar')
@section('content')
<div class="card" style="max-width:620px;margin:auto"><h1>Buat akun</h1><p>Pilih jenis akun sesuai kebutuhan Anda.</p>
<form method="POST" action="{{ route('register.store') }}">@csrf
<div class="field"><label>Nama lengkap</label><input name="name" value="{{ old('name') }}" required></div>
<div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
<div class="field"><label>Jenis akun</label><select name="role" id="role" onchange="document.getElementById('company').style.display=this.value==='merchant'?'block':'none'"><option value="customer">Customer — Kantor</option><option value="merchant" @selected(old('role')==='merchant')>Merchant — Katering</option></select></div>
<div class="field" id="company" style="display:{{ old('role')==='merchant'?'block':'none' }}"><label>Nama perusahaan katering</label><input name="company_name" value="{{ old('company_name') }}"></div>
<div class="field"><label>Password (minimal 8 karakter)</label><input type="password" name="password" required></div>
<div class="field"><label>Konfirmasi password</label><input type="password" name="password_confirmation" required></div>
<button class="btn">Daftar sekarang</button> <a href="{{ route('login') }}">Sudah punya akun?</a>
</form></div>
@endsection
