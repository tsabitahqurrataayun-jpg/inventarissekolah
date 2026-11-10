@extends('layouts.app')

@section('content')
<div class="report-page">
    <div class="report-header-row">
        <div>
            <p class="eyebrow">REKAPITULASI</p>
            <h2>Laporan Peminjaman</h2>
            <p class="muted">Lihat diagram dan ringkasan data peminjaman, lalu cetak sebagai PDF atau buka rekap di Excel.</p>
        </div>

        <div class="report-actions">
            <a href="{{ route('reports.print', ['status' => $currentStatus]) }}" class="button export secondary">Pratinjau Cetak / PDF</a>
            <a href="{{ route('reports.export', ['status' => $currentStatus]) }}" class="button export primary">Unduh untuk Excel (.csv)</a>
        </div>
    </div>

    <div class="report-summary-grid">
        <div class="report-stat-card">
            <small>Total Transaksi</small>
            <strong>{{ $stats['total'] }}</strong>
        </div>
        <div class="report-stat-card">
            <small>Unit Dipinjam</small>
            <strong>{{ $stats['borrowed'] }}</strong>
        </div>
        <div class="report-stat-card">
            <small>Jumlah Peminjam</small>
            <strong>{{ $stats['borrowers'] }}</strong>
        </div>
        <div class="report-stat-card danger">
            <small>Terlambat</small>
            <strong>{{ $stats['late'] }}</strong>
        </div>
    </div>

    @include('reports.analytics')

    <div class="report-toolbar">
        <div class="status-filters">
            <a href="{{ route('reports.index', ['status' => 'all']) }}" class="filter-chip {{ $currentStatus === 'all' ? 'active' : '' }}">Semua</a>
            <a href="{{ route('reports.index', ['status' => 'borrowed']) }}" class="filter-chip {{ $currentStatus === 'borrowed' ? 'active' : '' }}">Dipinjam</a>
            <a href="{{ route('reports.index', ['status' => 'returned']) }}" class="filter-chip {{ $currentStatus === 'returned' ? 'active' : '' }}">Dikembalikan</a>
            <a href="{{ route('reports.index', ['status' => 'late']) }}" class="filter-chip {{ $currentStatus === 'late' ? 'active' : '' }}">Terlambat</a>
            <a href="{{ route('reports.index', ['status' => 'pending']) }}" class="filter-chip {{ $currentStatus === 'pending' ? 'active' : '' }}">Menunggu Verifikasi</a>
        </div>
    </div>

    <div class="report-table-card">
        <div class="report-table-header">
            <h3>SMK NEGERI 5 SURAKARTA</h3>
            <p>Laporan Peminjaman Inventaris — SIPARIS · {{ $statusLabel }}</p>
            <small>Dicetak pada {{ now()->translatedFormat('d F Y') }} · {{ $loans->count() }} transaksi</small>
        </div>

        <div class="table-wrap report-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Peminjam</th>
                        <th>Peran</th>
                        <th>Nomor Telepon</th>
                        <th>Alat</th>
                        <th>Jumlah</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Jatuh Tempo</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $index => $loan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $loan->inventory?->code ?? '-' }}</td>
                            <td>{{ $loan->user?->name ?? '-' }}<br><small>{{ $loan->user?->role ?? 'Siswa' }}@if($loan->user?->role === 'siswa') · Kelas: {{ $loan->user?->class ?? 'Belum diisi' }}@endif</small></td>
                            <td>{{ $loan->user?->role ?? 'Siswa' }}</td>
                            <td>{{ $loan->borrower_phone ?? '-' }}</td>
                            <td>{{ $loan->inventory?->name ?? '-' }}</td>
                            <td>{{ $loan->quantity }} unit</td>
                            <td>{{ $loan->borrowed_at?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $loan->due_at?->format('d M Y') ?? '-' }}</td>
                            <td>{{ $loan->returned_at?->format('d M Y') ?? '-' }}</td>
                            <td>
                                @php
                                    $statusLabel = match($loan->status){
                                        'pending' => 'Menunggu Verifikasi',
                                        'return_pending' => 'Menunggu Verifikasi Pengembalian',
                                        'returned' => 'Dikembalikan',
                                        'approved', 'borrowed' => 'Dipinjam',
                                        'rejected' => 'Ditolak',
                                        default => ucfirst($loan->status),
                                    };
                                @endphp
                                <span class="report-status {{ $loan->status }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="empty">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
