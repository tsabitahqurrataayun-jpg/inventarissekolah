@extends('layouts.app')
@section('content')
@if(auth()->user()->role !== 'admin')
    <section class="student-welcome"><div><p class="student-kicker">DASHBOARD {{ strtoupper(auth()->user()->role) }}</p><h2>Selamat pagi, {{ auth()->user()->name }}</h2><p>Pantau inventaris dan seluruh aktivitas peminjaman hari ini.</p></div><div class="student-actions"><a href="{{ route('loans.create') }}" class="student-catalog">Lihat Katalog</a><a href="{{ route('loans.create') }}" class="button student-loan-button">Ajukan Pinjaman</a></div></section>
    <section class="student-metrics">
        <a class="student-metric metric-link" href="{{ route('loans.create') }}"><span class="student-metric-icon navy">▣</span><div><small>Total Alat</small><strong>{{ $inventoryCount }}</strong><em>{{ $inventoryCount > 0 ? $inventoryCount : 0 }} kategori</em></div></a>
        <a class="student-metric metric-link" href="{{ route('loans.index') }}"><span class="student-metric-icon cyan">▣</span><div><small>Sedang Dipinjam</small><strong>{{ $activeLoans }}</strong><em>Unit aktif</em></div></a>
        <a class="student-metric metric-link" href="{{ route('loans.index') }}"><span class="student-metric-icon amber">◷</span><div><small>Menunggu Verifikasi</small><strong>{{ $pendingLoans }}</strong><em>Perlu aksi</em></div></a>
        <a class="student-metric metric-link" href="{{ route('loans.index') }}"><span class="student-metric-icon danger">↩</span><div><small>Terlambat Kembali</small><strong>{{ $lateLoans }}</strong><em>Perlu dihubungi</em></div></a>
    </section>
    <div class="student-dashboard-grid"><section class="student-requests-panel"><div class="student-panel-heading"><div><h3>Pengajuan Terkini</h3><p>Pembaruan transaksi terbaru</p></div><a href="{{ route('loans.index') }}">Lihat semua ›</a></div><div class="student-request-list">@forelse($loans as $loan)<div class="student-request-row"><span class="student-request-icon">▣</span><div class="student-request-copy"><strong>{{ $loan->inventory->name }}</strong><small>{{ auth()->user()->name }} · {{ $loan->quantity }} unit · {{ $loan->notes ?: 'Peminjaman alat' }}</small></div><span class="badge {{ $loan->status }}">{{ $loan->status_label }}</span></div>@empty<div class="empty">Belum ada pengajuan peminjaman.</div>@endforelse</div></section>@include('components.return-schedule')</div>
@else
    <section class="admin-welcome"><div><p class="admin-kicker">DASHBOARD ADMIN</p><h2>Selamat pagi, Admin SIPARIS</h2><p>Pantau inventaris dan seluruh aktivitas peminjaman hari ini.</p></div><div class="admin-quick-actions"><a href="{{ route('inventories.index') }}" class="catalog-link">Lihat Katalog</a><a href="{{ route('loans.index') }}" class="button verify-button">Verifikasi Sekarang</a></div></section>
    <section class="admin-metrics">
        <a class="admin-metric metric-link" href="{{ route('inventories.index') }}"><span class="metric-icon navy">▣</span><div><small>Total Alat</small><strong>{{ $inventoryCount }}</strong><em>{{ $inventoryCount > 0 ? $inventoryCount : 0 }} kategori</em></div></a>
        <a class="admin-metric metric-link" href="{{ route('loans.index') }}"><span class="metric-icon cyan">▣</span><div><small>Sedang Dipinjam</small><strong>{{ $activeLoans }}</strong><em>Unit aktif</em></div></a>
        <a class="admin-metric metric-link" href="{{ route('loans.index') }}"><span class="metric-icon amber">◷</span><div><small>Menunggu Verifikasi</small><strong>{{ $pendingLoans }}</strong><em>Perlu aksi</em></div></a>
        <a class="admin-metric metric-link" href="{{ route('loans.index') }}"><span class="metric-icon danger">↩</span><div><small>Terlambat Kembali</small><strong>{{ $lateLoans }}</strong><em>Perlu dihubungi</em></div></a>
    </section>
    @if($overdueLoans->isNotEmpty())
        <section class="overdue-notice" role="alert">
            <div class="overdue-notice-heading">
                <div><strong>Pengingat pengembalian terlambat</strong><p>{{ $overdueLoans->count() }} peminjaman melewati tanggal pengembalian dan belum dikembalikan.</p></div>
                <a href="{{ route('loans.index') }}">Lihat peminjaman</a>
            </div>
            <ul>
                @foreach($overdueLoans as $loan)
                    <li><strong>{{ $loan->user?->name ?? 'Peminjam' }}</strong> belum mengembalikan {{ $loan->inventory?->name ?? 'inventaris' }} (jatuh tempo {{ $loan->due_at->translatedFormat('d F Y') }}).</li>
                @endforeach
            </ul>
        </section>
    @endif
    <div class="admin-dashboard-grid">
        @include('components.admin-loan-requests', ['loans' => $loans])
        @include('components.return-schedule')
    </div>
@endif
@endsection
