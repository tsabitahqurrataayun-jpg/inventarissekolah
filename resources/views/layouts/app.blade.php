<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Inventaris' }} | Inventaris</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite('resources/js/app.js')
    @endif
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    @if(auth()->user()->role !== 'admin')
        <link rel="stylesheet" href="{{ asset('user.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('return-schedule.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-actions.css') }}">
    <link rel="stylesheet" href="{{ asset('loan-request.css') }}">
    <link rel="stylesheet" href="{{ asset('return-date-dialog.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('inventory-page.css') }}">
    <link rel="stylesheet" href="{{ asset('loan-evidence.css') }}">
    <link rel="stylesheet" href="{{ asset('stock-toast.css') }}">
    <link rel="stylesheet" href="{{ asset('loan-confirmation.css') }}">
    <link rel="stylesheet" href="{{ asset('rejection-dialog.css') }}">
    <link rel="stylesheet" href="{{ asset('responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('sidebar-nav.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard-metrics.css') }}">
    <link rel="stylesheet" href="{{ asset('report-charts.css') }}">
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
        .table-wrap .rejection-reason {
            margin-top: 8px;
            line-height: 1.5;
            white-space: normal;
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
        .user-shell .sidebar-user {
            margin-top: auto;
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
                <a class="nav-link {{ request()->routeIs('inventories.catalog') ? 'active' : '' }}" href="{{ route('inventories.catalog') }}"><span>▤</span>Daftar Inventaris</a>
                <a class="nav-link {{ request()->routeIs('loans.create') ? 'active' : '' }}" href="{{ route('loans.create') }}"><span>♧</span>Ajukan Peminjaman</a>
                <a class="nav-link {{ request()->routeIs('loans.index') ? 'active' : '' }}" href="{{ route('loans.index') }}"><span>↻</span>Pengajuan</a>
            @endif
        </nav>
        <div class="sidebar-user"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'admin' ? 'Petugas' : ucfirst(auth()->user()->role) }}</small></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button" title="Keluar">Keluar</button></form></div>
    </aside>
    <button type="button" class="mobile-nav-backdrop" aria-label="Tutup menu navigasi" hidden></button>
    <main class="content">
        <header class="topbar"><div><button type="button" class="mobile-menu-toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="primary-navigation"><span></span><span></span><span></span></button><div class="topbar-page-title"><span class="breadcrumb">⌂ &nbsp; / &nbsp; Dashboard</span><h1>Dashboard</h1></div></div><div class="top-actions"><span class="role-pill">{{ ucfirst(auth()->user()->role) }}</span><span class="notification">♧</span><div class="user-menu-trigger"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><strong>{{ auth()->user()->name }}</strong><span class="role-label">⌄</span></div></div></header>
        @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="flash error">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="flash error">{{ $errors->first() }}</div>@endif
        {{ $slot ?? '' }}
        @yield('content')
    </main>
    @if(auth()->user()->role === 'admin')
        <div class="rejection-dialog" data-rejection-dialog hidden>
            <section class="rejection-dialog-panel" role="dialog" aria-modal="true" aria-labelledby="rejection-dialog-title" aria-describedby="rejection-dialog-description">
                <span class="rejection-dialog-icon" aria-hidden="true">!</span>
                <h2 id="rejection-dialog-title">Tolak pengajuan peminjaman?</h2>
                <p id="rejection-dialog-description">Tuliskan alasan agar siswa atau guru mengetahui mengapa peminjaman tidak diterima.</p>
                <label for="rejection-reason-input">Alasan penolakan</label>
                <textarea id="rejection-reason-input" data-rejection-reason maxlength="1000" rows="4" placeholder="Contoh: stok barang sedang digunakan untuk kegiatan lain"></textarea>
                <small class="rejection-dialog-error" data-rejection-error hidden>Alasan penolakan wajib diisi.</small>
                <div class="rejection-dialog-actions">
                    <button type="button" class="rejection-dialog-cancel" data-rejection-cancel>Batal</button>
                    <button type="button" class="rejection-dialog-confirm" data-rejection-confirm>Tolak peminjaman</button>
                </div>
            </section>
        </div>
        <div class="loan-confirmation" data-approval-confirmation hidden>
            <section class="loan-confirmation-dialog" role="dialog" aria-modal="true" aria-labelledby="approval-confirmation-title" aria-describedby="approval-confirmation-description">
                <span class="loan-confirmation-icon" aria-hidden="true">?</span>
                <h3 id="approval-confirmation-title">Konfirmasi penerimaan</h3>
                <p id="approval-confirmation-description">Apakah kamu benar ingin menerima peminjaman tersebut?</p>
                <div class="loan-confirmation-actions">
                    <button type="button" class="loan-confirmation-no" data-approval-confirmation-no>Tidak</button>
                    <button type="button" class="loan-confirmation-yes" data-approval-confirmation-yes>Ya, terima</button>
                </div>
            </section>
        </div>
    @endif
</div>

<script>
    function requestApprovalConfirmation(event) {
        const form = event.currentTarget;

        if (form.dataset.approvalConfirmed === 'true') {
            delete form.dataset.approvalConfirmed;
            return true;
        }

        event.preventDefault();
        window.openApprovalConfirmation(form, event.submitter);

        return false;
    }

    function requestRejectionReason(event) {
        const form = event.currentTarget;

        if (form.dataset.rejectionConfirmed === 'true') {
            delete form.dataset.rejectionConfirmed;
            return true;
        }

        event.preventDefault();
        window.openRejectionDialog(form, event.submitter);

        return false;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const rejectionDialog = document.querySelector('[data-rejection-dialog]');
        if (rejectionDialog) {
            const rejectionInput = rejectionDialog.querySelector('[data-rejection-reason]');
            const rejectionError = rejectionDialog.querySelector('[data-rejection-error]');
            const confirmButton = rejectionDialog.querySelector('[data-rejection-confirm]');
            let rejectionForm = null;
            let rejectionTrigger = null;

            window.openRejectionDialog = function (form, trigger) {
                rejectionForm = form;
                rejectionTrigger = trigger;
                rejectionInput.value = '';
                rejectionError.hidden = true;
                rejectionDialog.hidden = false;
                rejectionInput.focus();
            };

            const closeRejectionDialog = function () {
                rejectionDialog.hidden = true;
                rejectionForm = null;
                if (rejectionTrigger) rejectionTrigger.focus();
                rejectionTrigger = null;
            };

            rejectionDialog.querySelector('[data-rejection-cancel]').addEventListener('click', closeRejectionDialog);
            rejectionDialog.addEventListener('click', function (event) {
                if (event.target === rejectionDialog) closeRejectionDialog();
            });
            confirmButton.addEventListener('click', function () {
                const reason = rejectionInput.value.trim();
                if (!reason) {
                    rejectionError.hidden = false;
                    rejectionInput.focus();
                    return;
                }

                let reasonField = rejectionForm.querySelector('[name="rejection_reason"]');
                if (!reasonField) {
                    reasonField = document.createElement('input');
                    reasonField.type = 'hidden';
                    reasonField.name = 'rejection_reason';
                    rejectionForm.appendChild(reasonField);
                }

                reasonField.value = reason;
                const formToSubmit = rejectionForm;
                rejectionDialog.hidden = true;
                rejectionForm = null;
                rejectionTrigger = null;
                formToSubmit.dataset.rejectionConfirmed = 'true';
                formToSubmit.requestSubmit();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !rejectionDialog.hidden) closeRejectionDialog();
            });
        }

        const approvalConfirmation = document.querySelector('[data-approval-confirmation]');
        if (approvalConfirmation) {
            let approvalForm = null;
            let approvalTrigger = null;

            window.openApprovalConfirmation = function (form, trigger) {
                approvalForm = form;
                approvalTrigger = trigger;
                approvalConfirmation.hidden = false;
                approvalConfirmation.querySelector('[data-approval-confirmation-no]').focus();
            };

            const closeApprovalConfirmation = function () {
                approvalConfirmation.hidden = true;
                approvalForm = null;
                if (approvalTrigger) approvalTrigger.focus();
                approvalTrigger = null;
            };

            approvalConfirmation.querySelector('[data-approval-confirmation-no]').addEventListener('click', closeApprovalConfirmation);
            approvalConfirmation.addEventListener('click', function (event) {
                if (event.target === approvalConfirmation) closeApprovalConfirmation();
            });
            approvalConfirmation.querySelector('[data-approval-confirmation-yes]').addEventListener('click', function () {
                const formToSubmit = approvalForm;
                approvalConfirmation.hidden = true;
                approvalForm = null;
                approvalTrigger = null;
                formToSubmit.dataset.approvalConfirmed = 'true';
                formToSubmit.requestSubmit();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !approvalConfirmation.hidden) closeApprovalConfirmation();
            });
        }

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
