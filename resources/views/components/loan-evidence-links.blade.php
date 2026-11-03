<div class="loan-evidence-links">
    @if($loan->borrowing_evidence_path)
        <a class="loan-evidence-link borrowing" href="{{ route('loans.evidence', [$loan, 'borrowing']) }}" target="_blank" rel="noopener">
            <span class="loan-evidence-icon" aria-hidden="true">▧</span>
            <span class="loan-evidence-copy">
                <strong>Bukti peminjaman</strong>
                <small>Lihat foto barang saat diterima</small>
            </span>
            <span class="loan-evidence-arrow" aria-hidden="true">↗</span>
        </a>
    @endif
    @if($loan->return_evidence_path)
        <a class="loan-evidence-link returned" href="{{ route('loans.evidence', [$loan, 'return']) }}" target="_blank" rel="noopener">
            <span class="loan-evidence-icon" aria-hidden="true">✓</span>
            <span class="loan-evidence-copy">
                <strong>Bukti pengembalian</strong>
                <small>Lihat foto barang yang dikembalikan</small>
            </span>
            <span class="loan-evidence-arrow" aria-hidden="true">↗</span>
        </a>
    @endif
    @if(!$loan->borrowing_evidence_path && !$loan->return_evidence_path)
        <span class="loan-evidence-empty">Belum ada bukti</span>
    @endif
</div>
