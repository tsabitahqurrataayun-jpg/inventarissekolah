@extends('layouts.app')

@section('content')
<div class="welcome-row">
    <div>
        <p class="eyebrow">RIWAYAT SIRKULASI</p>
        <h2>Peminjaman</h2>
        <p class="muted">Pantau status barang yang dipinjam dan diajukan.</p>
    </div>
    @if(auth()->user()->role !== 'admin')
        <a class="button primary" href="{{ route('loans.create') }}">Ajukan baru <span>↗</span></a>
    @endif
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Barang</th>
                @if(auth()->user()->role === 'admin')
                    <th>Peminjam</th>
                @endif
                <th>Jumlah</th>
                <th>Tanggal pinjam</th>
                <th>Jatuh tempo</th>
                <th>Status</th>
                <th>Bukti</th>
                <th>{{ auth()->user()->role === 'admin' ? 'Aksi admin' : 'Pengembalian' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
                <tr>
                    <td>
                        <strong>{{ $loan->inventory?->name ?? 'Inventaris dihapus' }}</strong>
                        <small>{{ $loan->inventory?->code ?? '-' }}</small>
                    </td>
                    @if(auth()->user()->role === 'admin')
                        <td>{{ $loan->user?->name ?? '-' }}</td>
                    @endif
                    <td>{{ $loan->quantity }} unit</td>
                    <td>{{ $loan->borrowed_at?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $loan->due_at->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $loan->status }}">{{ $loan->status_label }}</span>
                        @if(in_array($loan->status, ['approved', 'borrowed'], true) && $loan->due_at->isBefore(today()))
                            <small class="overdue-label">Belum dikembalikan · terlambat</small>
                        @endif
                        @if($loan->status === 'rejected' && $loan->rejection_reason)
                            <small class="rejection-reason">Alasan penolakan: {{ $loan->rejection_reason }}</small>
                        @endif
                    </td>
                    <td>
                        @include('components.loan-evidence-links', ['loan' => $loan])
                    </td>
                    <td>
                        @if(auth()->user()->role === 'admin')
                            @if($loan->status === 'pending')
                                <div class="inline-actions">
                                    <form method="POST" action="{{ route('loans.status', $loan) }}" onsubmit="return requestApprovalConfirmation(event)">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="mini-button approve">Setujui pinjaman</button>
                                    </form>
                                    <form method="POST" action="{{ route('loans.status', $loan) }}" onsubmit="return requestRejectionReason(event)">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="mini-button reject">Tolak</button>
                                    </form>
                                </div>
                            @elseif($loan->status === 'return_pending')
                                <form method="POST" action="{{ route('loans.status', $loan) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="returned">
                                    <button class="mini-button approve">Konfirmasi barang diterima</button>
                                </form>
                            @else
                                <span>-</span>
                            @endif
                        @elseif(in_array($loan->status, ['approved', 'borrowed'], true))
                                <form method="POST" action="{{ route('loans.return', $loan) }}" enctype="multipart/form-data" class="return-evidence-form">
                                    @csrf
                                    <label>
                                        Bukti foto barang yang dikembalikan
                                        <input type="file" name="return_evidence" accept="image/jpeg,image/png,image/webp" required>
                                    </label>
                                    @error('return_evidence')
                                        <small class="evidence-error">{{ $message }}</small>
                                    @enderror
                                    <button class="mini-button approve" type="submit">Kirim bukti pengembalian</button>
                                </form>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()->role === 'admin' ? 8 : 7 }}" class="empty">Belum ada data peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
