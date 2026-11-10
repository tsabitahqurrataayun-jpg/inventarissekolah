@extends('layouts.app')

@section('content')
<div class="welcome-row">
    <div>
        <p class="eyebrow">RIWAYAT INVENTARIS</p>
        <h2>Pengembalian Barang</h2>
        <p class="muted">Riwayat barang yang sudah dikembalikan dan dikonfirmasi admin.</p>
    </div>
    <a class="button secondary" href="{{ route('loans.index') }}">Lihat semua peminjaman</a>
</div>

<section class="report-summary-grid">
    <div class="report-stat-card">
        <small>Transaksi dikembalikan</small>
        <strong>{{ $returnedCount }}</strong>
    </div>
    <div class="report-stat-card">
        <small>Total unit kembali</small>
        <strong>{{ $returnedUnits }}</strong>
    </div>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Kode inventaris</th>
                <th>Peminjam</th>
                <th>Jumlah</th>
                <th>Tanggal pinjam</th>
                <th>Tanggal dikembalikan</th>
                <th>Bukti</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
                <tr>
                    <td><strong>{{ $loan->inventory?->name ?? 'Inventaris dihapus' }}</strong></td>
                    <td>{{ $loan->inventory?->code ?? '-' }}</td>
                    <td>
                        <strong>{{ $loan->user?->name ?? '-' }}</strong>
                        <small>{{ ucfirst($loan->user?->role ?? 'siswa') }}</small>
                        @if($loan->borrower_phone)
                            <small>Telp: <a href="tel:{{ preg_replace('/[^0-9+]/', '', $loan->borrower_phone) }}">{{ $loan->borrower_phone }}</a></small>
                        @else
                            <small>Telp: -</small>
                        @endif
                        @if($loan->user?->role === 'siswa')
                            <small>Kelas: {{ $loan->user?->class ?? 'Belum diisi' }}</small>
                        @endif
                    </td>
                    <td>{{ $loan->quantity }} unit</td>
                    <td>{{ $loan->borrowed_at?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $loan->returned_at?->format('d M Y') ?? '-' }}</td>
                    <td>
                        @include('components.loan-evidence-links', ['loan' => $loan])
                    </td>
                    <td><span class="badge returned">Dikembalikan</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">Belum ada barang yang dikembalikan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
