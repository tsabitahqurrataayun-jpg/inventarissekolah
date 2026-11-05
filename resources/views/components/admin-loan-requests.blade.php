<section class="requests-panel">
    <div class="admin-panel-heading">
        <div>
            <h3>Status Peminjaman Terbaru</h3>
            <p>Barang yang belum dikembalikan ditampilkan terlebih dahulu</p>
        </div>
        <a href="{{ route('loans.index') }}">Lihat semua ›</a>
    </div>

    <div class="request-list">
        @forelse($loans as $loan)
            <div class="request-row">
                <span class="request-icon">▣</span>
                <div class="request-copy">
                    <strong>{{ $loan->inventory?->name ?? 'Inventaris dihapus' }}</strong>
                    <small>{{ $loan->user?->name ?? '-' }} · {{ $loan->quantity }} unit · {{ $loan->notes ?: 'Peminjaman alat' }}</small>
                    @if(in_array($loan->status, ['approved', 'borrowed'], true))
                        <small>Belum dikembalikan · jatuh tempo {{ $loan->due_at?->translatedFormat('d F Y') ?? '-' }}</small>
                    @elseif($loan->status === 'return_pending')
                        <small>Bukti pengembalian terkirim · menunggu konfirmasi admin</small>
                    @endif
                    @if($loan->status === 'rejected' && $loan->rejection_reason)
                        <small>Alasan penolakan: {{ $loan->rejection_reason }}</small>
                    @endif
                </div>
                <div class="request-actions">
                    @include('components.loan-evidence-links', ['loan' => $loan])
                    @if($loan->status === 'pending')
                        <form method="POST" action="{{ route('loans.status', $loan) }}" onsubmit="return requestApprovalConfirmation(event)">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button class="request-button approve">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('loans.status', $loan) }}" onsubmit="return requestRejectionReason(event)">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button class="request-button reject">Tolak</button>
                        </form>
                    @elseif($loan->status === 'return_pending')
                        <span class="badge return_pending">{{ $loan->status_label }}</span>
                        <form method="POST" action="{{ route('loans.status', $loan) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="returned">
                            <button class="request-button approve">Konfirmasi diterima</button>
                        </form>
                    @else
                        <span class="badge {{ $loan->status }}">{{ $loan->status_label }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty">Belum ada pengajuan peminjaman.</div>
        @endforelse
    </div>
</section>
