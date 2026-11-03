<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Inventaris' }} | Inventaris</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('app.css') }}">
        @if(auth()->user()->role !== 'admin')
            <link rel="stylesheet" href="{{ asset('user.css') }}">
        @endif
    <link rel="stylesheet" href="{{ asset('return-schedule.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-actions.css') }}">
    <link rel="stylesheet" href="{{ asset('loan-request.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('inventory-page.css') }}">
    <link rel="stylesheet" href="{{ asset('loan-evidence.css') }}">
    <link rel="stylesheet" href="{{ asset('responsive.css') }}">
    <style>
        .admin-shell .content {
            background: #efefee;
            padding: 18px 24px 30px;
        }
        .admin-shell .topbar {
            margin-bottom: 18px;
            border-bottom: 1px solid #dfe2e6;
            padding-bottom: 8px;
        }
        .report-page {
            padding: 8px 0 0;
        }
        .report-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 24px;
            margin-bottom: 18px;
        }
        .report-header-row .eyebrow {
            letter-spacing: .09em;
            color: #818b95;
            font-size: 11px;
            margin: 0 0 10px;
        }
        .report-header-row h2 {
            margin: 0;
            font-size: 24px;
            color: #1e2d40;
            font-weight: 800;
            line-height: 1.2;
        }
        .report-header-row .muted {
            margin: 8px 0 0;
            color: #6d7782;
            font-size: 13px;
        }
        .report-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .report-actions .button {
            border-radius: 9px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #d6dfe8;
            min-width: 88px;
        }
        .report-actions .button.secondary {
            background: #0d2a4a;
            color: #fff;
            border-color: #0d2a4a;
        }
        .report-actions .button.primary {
            background: #f2b841;
            color: #1d3555;
            border-color: #f2b841;
        }
        .report-actions .button.gold {
            background: #f5d894;
            color: #1d3555;
            border-color: #f5d894;
        }
        .report-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(120px, 1fr));
            gap: 16px;
            margin: 0 0 18px;
        }
        .report-stat-card {
            background: transparent;
            padding: 0;
            border: 0;
            min-height: 0;
        }
        .report-stat-card small {
            display: block;
            font-size: 12px;
            color: #4c5a68;
            margin-bottom: 6px;
        }
        .report-stat-card strong {
            display: block;
            font-size: 24px;
            line-height: 1.1;
            color: #1b3351;
            font-weight: 800;
        }
        .report-stat-card.danger strong {
            color: #d65b5c;
        }
        .report-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin: 0 0 18px;
        }
        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 220px;
            max-width: 420px;
            padding: 10px 12px;
            border: 1px solid #dfe5eb;
            background: #f8f9fb;
            border-radius: 8px;
            color: #8894a2;
            font-size: 13px;
        }
        .search-box span {
            font-size: 15px;
        }
        .search-box input {
            border: 0;
            background: transparent;
            outline: none;
            width: 100%;
            color: #75839a;
            font-size: 13px;
        }
        .status-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .filter-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 8px;
            background: #f2f4f6;
            color: #4d5968;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #dde3ea;
        }
        .filter-chip.active {
            background: #0d2a4a;
            color: #fff;
            border-color: #0d2a4a;
        }
        .report-table-card {
            background: #f7f7f6;
            border: 1px solid #dfe4ea;
            border-radius: 10px;
            overflow: hidden;
        }
        .report-table-header {
            padding: 18px 18px 8px;
            text-align: center;
        }
        .report-table-header h3 {
            margin: 0;
            font-size: 19px;
            color: #1d3555;
            font-weight: 900;
            letter-spacing: 0.02em;
        }
        .report-table-header p {
            margin: 6px 0 4px;
            color: #53657a;
            font-size: 13px;
        }
        .report-table-header small {
            color: #7a8592;
            font-size: 11px;
        }
        .report-table-wrap {
            padding: 0 18px 18px;
        }
        .report-table-wrap table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid #e4e7eb;
        }
        .report-table-wrap thead th {
            background: #f2f5f7;
            color: #324457;
            font-size: 11px;
            padding: 10px 8px;
            text-align: left;
            border-bottom: 1px solid #e4e7eb;
            font-weight: 800;
        }
        .report-table-wrap tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #edf0f3;
            color: #2d3e52;
            font-size: 12px;
            vertical-align: top;
        }
        .report-table-wrap tbody tr:last-child td {
            border-bottom: 0;
        }
        .report-table-wrap td small {
            display: block;
            color: #76859a;
            font-size: 11px;
            margin-top: 2px;
        }
        .report-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }
        .report-status.pending {
            background: #ebf3ff;
            color: #2a6ac9;
        }
        .report-status.approved,
        .report-status.borrowed {
            background: #eaf9ef;
            color: #29885a;
        }
        .report-status.returned {
            background: #e9f7ee;
            color: #2e7b59;
        }
        .report-status.rejected {
            background: #fbe9e9;
            color: #cf5656;
        }
        .report-table-wrap .empty {
            text-align: center;
            color: #667387;
            padding: 18px;
        }
        @media (max-width: 900px) {
            .report-header-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .report-summary-grid {
                grid-template-columns: repeat(2, minmax(120px, 1fr));
            }
        }
    </style>
</head>
<body class="{{ auth()->user()->role === 'admin' ? 'admin-shell' : 'user-shell' }}">
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">S</span><span><strong>SIPARIS</strong><small>SMKN 5 SURAKARTA</small></span></a>
        <nav class="nav-list" id="primary-navigation">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>⌂</span>Dashboard</a>
            @if(auth()->user()->role === 'admin')
                <a class="nav-link {{ request()->routeIs('inventories.*') ? 'active' : '' }}" href="{{ route('inventories.index') }}"><span>▤</span>Data Inventaris</a>
                <a class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}" href="{{ route('loans.index') }}"><span>▣</span>Peminjaman</a>
                <a class="nav-link {{ request()->routeIs('loans.returned') ? 'active' : '' }}" href="{{ route('loans.returned') }}"><span>↩</span>Pengembalian</a>
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><span>▥</span>Laporan</a>
            @else
                <a class="nav-link {{ request()->routeIs('loans.create') ? 'active' : '' }}" href="{{ route('loans.create') }}"><span>♧</span>Katalog Alat</a>
                <a class="nav-link {{ request()->routeIs('loans.index') ? 'active' : '' }}" href="{{ route('loans.index') }}"><span>↻</span>Pengajuan</a>
            @endif
        </nav>
        @if(auth()->user()->role !== 'admin')
            <div class="user-export"><small>EKSPOR DATA</small><div><button type="button">▣ PDF</button><button type="button">▤ Excel</button></div></div>
        @endif
        <div class="sidebar-user"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'admin' ? 'Petugas' : ucfirst(auth()->user()->role) }}</small></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button" title="Keluar">Keluar</button></form></div>
    </aside>
    <button type="button" class="mobile-nav-backdrop" aria-label="Tutup menu navigasi" hidden></button>
    <main class="content">
        <header class="topbar"><div><button type="button" class="mobile-menu-toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="primary-navigation"><span></span><span></span><span></span></button><div class="topbar-page-title"><span class="breadcrumb">⌂ &nbsp; / &nbsp; Dashboard</span><h1>Dashboard</h1></div></div><div class="top-actions"><span class="role-pill">{{ auth()->user()->role === 'admin' ? 'Petugas Lab' : 'Siswa/Guru' }}</span><span class="notification">♧</span><div class="user-menu-trigger"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><strong>{{ auth()->user()->name }}</strong><span class="role-label">⌄</span></div></div></header>
        @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="flash error">{{ $errors->first() }}</div>@endif
        {{ $slot ?? '' }}
        @yield('content')
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const backdrop = document.querySelector('.mobile-nav-backdrop');

        if (!menuToggle || !backdrop) return;

        const closeMenu = function () {
            document.body.classList.remove('mobile-nav-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Buka menu navigasi');
            backdrop.hidden = true;
        };

        menuToggle.addEventListener('click', function () {
            const isOpen = document.body.classList.toggle('mobile-nav-open');
            menuToggle.setAttribute('aria-expanded', String(isOpen));
            menuToggle.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
            backdrop.hidden = !isOpen;
        });

        backdrop.addEventListener('click', closeMenu);
        document.querySelectorAll('.sidebar .nav-link').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeMenu();
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 760) closeMenu();
        });
    });
</script>
</body>
</html>
