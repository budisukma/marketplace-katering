<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KateringKita') — Marketplace Katering</title>
    <style>
        :root {
            --ink: #182230;
            --muted: #667085;
            --green: #16794b;
            --pale: #f3f7f4;
            --line: #e4e7ec
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font: 15px/1.55 Arial, sans-serif;
            color: var(--ink);
            background: #f8faf9
        }

        a {
            color: var(--green);
            text-decoration: none
        }

        header {
            background: #fff;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 2
        }

        .nav {
            max-width: 1100px;
            margin: auto;
            padding: 15px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px
        }

        .brand {
            font-weight: 800;
            font-size: 21px;
            color: var(--green)
        }

        .navlinks {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap
        }

        .wrap {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 22px
        }

        h1 {
            font-size: 30px;
            line-height: 1.2;
            margin: 0 0 10px
        }

        h2 {
            font-size: 21px;
            margin: 0 0 14px
        }

        p {
            color: var(--muted)
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 3px 12px #10182808
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(245px, 1fr));
            gap: 18px
        }

        .stack {
            display: grid;
            gap: 14px
        }

        .row {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap
        }

        label {
            font-size: 13px;
            font-weight: 700;
            display: block;
            margin-bottom: 5px
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d0d5dd;
            border-radius: 8px;
            font: inherit;
            background: #fff
        }

        textarea {
            min-height: 85px
        }

        .field {
            margin-bottom: 13px
        }

        .btn {
            display: inline-block;
            border: 0;
            border-radius: 8px;
            background: var(--green);
            color: #fff;
            padding: 10px 15px;
            font-weight: 700;
            cursor: pointer;
            font: inherit
        }

        .btn.secondary {
            background: #e8f4ed;
            color: var(--green)
        }

        .btn.danger {
            background: #b42318
        }

        .muted {
            color: var(--muted)
        }

        .price {
            font-size: 18px;
            font-weight: 800;
            color: var(--green)
        }

        .pill {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 20px;
            background: #e8f4ed;
            color: #166534;
            font-size: 12px
        }

        .alert {
            padding: 12px 15px;
            border-radius: 9px;
            background: #e8f4ed;
            margin-bottom: 15px
        }

        .error {
            background: #fef3f2;
            color: #b42318;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px
        }

        .menu-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 9px;
            background: #edf2ef
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: #fff
        }

        .table th,
        .table td {
            text-align: left;
            padding: 11px;
            border-bottom: 1px solid var(--line);
            vertical-align: top
        }

        footer {
            text-align: center;
            padding: 30px;
            color: var(--muted)
        }

        @media(max-width:600px) {
            .nav {
                align-items: flex-start;
                flex-direction: column
            }

            .wrap {
                margin: 22px auto;
                padding: 0 14px
            }

            h1 {
                font-size: 25px
            }

            .table {
                font-size: 13px
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="nav">
            <a class="brand" href="{{ route('home') }}">
                <span>🍱</span>
                <span>KateringKita</span>
            </a>

            <div class="navlinks">
                <a href="{{ route('home') }}">Cari Katering</a>

                @if(auth()->check())
                @if(auth()->user()->role === 'merchant')
                <a href="{{ route('merchant.dashboard') }}">
                    Dashboard Merchant
                </a>
                @else
                <a href="{{ route('customer.dashboard') }}">
                    Pesanan Saya
                </a>
                @endif

                <span class="muted">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn secondary">
                        Keluar
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}">Masuk</a>

                <a class="btn" href="{{ route('register') }}">
                    Daftar
                </a>
                @endif
            </div>
        </div>
    </header>

    <main class="wrap">
        @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="error"><strong>Mohon periksa kembali:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>@endif
        @yield('content')
    </main>
    <footer>Marketplace Katering · Menghubungkan kantor dengan penyedia katering</footer>
</body>

</html>