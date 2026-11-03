<section class="requests-panel">
    <div class="admin-panel-heading">
        <div>
            <h3>Pengajuan Terkini</h3>
            <p>Pembaruan transaksi terbaru</p>
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
                </div>
                <div class="request-actions">
                    @include('components.loan-evidence-links', ['loan' => $loan])
                    @if($loan->status === 'pending')
                        <form method="POST" action="{{ route('loans.status', $loan) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button class="request-button approve">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('loans.status', $loan) }}">
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
