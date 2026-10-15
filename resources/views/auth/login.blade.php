@extends('layouts.app')
@section('title','Masuk')
@section('content')
<div class="card" style="max-width:520px;margin:auto"><h1>Selamat datang kembali</h1><p>Masuk ke portal Marketplace Katering.</p>
<form method="POST" action="{{ route('login.store') }}">@csrf
<div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus></div>
<div class="field"><label>Password</label><input type="password" name="password" required></div>
<div class="field"><label><input type="checkbox" name="remember" value="1" style="width:auto"> Ingat saya</label></div>
<button class="btn">Masuk</button> <a href="{{ route('register') }}">Buat akun</a>
</form></div>
@endsection
