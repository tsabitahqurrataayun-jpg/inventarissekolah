@extends('layouts.app')

@section('content')
<div class="loan-request-hero">
    <div>
        <p class="eyebrow">PERMINTAAN BARU</p>
        <h2>Ajukan peminjaman</h2>
        <p class="muted">Pilih perlengkapan, tentukan durasi, lalu kirim permintaan kepada petugas.</p>
    </div>
    <div class="loan-hero-art" aria-hidden="true"><span>▣</span><i>↗</i></div>
</div>

<div class="loan-request-layout">
    <div class="form-panel loan-form-panel">
        <div class="form-panel-heading">
            <div>
                <h3>Detail peminjaman</h3>
                <p>Isi data berikut agar pengajuan dapat segera diverifikasi.</p>
                @if(auth()->user()->role === 'siswa')
                    <p class="student-loan-limit-note" role="status">Maksimal 3 unit per pengajuan.</p>
                @endif
            </div>
            <span class="form-step">01 / 03</span>
        </div>
        <form method="POST" action="{{ route('loans.store') }}" class="form-grid" data-loan-request-form data-student-unit-limit="{{ auth()->user()->role === 'siswa' ? 3 : 0 }}">
            @csrf
            <label class="wide">
                Inventaris
                <select name="inventory_id" required data-inventory-select>
                    <option value="">Pilih barang</option>
                    @foreach($inventories as $inventory)
                        <option value="{{ $inventory->id }}" data-available-stock="{{ $inventory->available_stock }}" data-inventory-name="{{ $inventory->name }}" @selected(old('inventory_id', request()->query('inventory_id')) == $inventory->id)>
                            {{ $inventory->name }} · {{ $inventory->available_stock }} unit tersedia
                        </option>
                    @endforeach
                </select>
            </label>
            <label>
                Jumlah unit
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required data-loan-quantity>
            </label>
            <label>
                Tanggal pinjam
                <input type="date" name="borrowed_at" min="{{ today()->toDateString() }}" value="{{ old('borrowed_at', today()->toDateString()) }}" required>
            </label>
            <label>
                Dipinjam sampai
                <input type="date" name="due_at" min="{{ old('borrowed_at', today()->toDateString()) }}" value="{{ old('due_at') }}" required>
            </label>
            <label class="wide">
                Nomor telepon yang bisa dihubungi
                <input type="tel" name="borrower_phone" value="{{ old('borrower_phone') }}" placeholder="Contoh: 081234567890" autocomplete="tel" maxlength="30" pattern="[+]?[0-9 ().-]{8,30}" required>
                <small>Nomor ini akan digunakan petugas jika peminjaman terlambat dikembalikan.</small>
                @error('borrower_phone')
                    <small class="evidence-error">{{ $message }}</small>
                @enderror
            </label>
            <label class="wide">
                Catatan
                <textarea name="notes" rows="4" placeholder="Contoh: untuk kegiatan praktik atau rapat kelas (opsional)">{{ old('notes') }}</textarea>
            </label>
            <div class="wide form-actions">
                <a class="button secondary" href="{{ route('loans.index') }}">Batal</a>
                <button class="button primary" type="submit">Kirim pengajuan <span>→</span></button>
            </div>
        </form>
    </div>

    <aside class="loan-request-info">
        <div class="info-heading">
            <span class="info-icon">i</span>
            <div>
                <h3>Alur pengajuan</h3>
                <p>Hanya perlu tiga langkah sederhana.</p>
            </div>
        </div>
        <ol class="loan-steps">
            <li><b>01</b><div><strong>Pilih inventaris</strong><small>Pastikan stok barang masih tersedia.</small></div></li>
            <li><b>02</b><div><strong>Tentukan pengembalian</strong><small>Pilih tanggal yang sesuai kebutuhan.</small></div></li>
            <li><b>03</b><div><strong>Tunggu verifikasi</strong><small>Petugas akan memeriksa permintaanmu.</small></div></li>
        </ol>
        <div class="available-box">
            <div><strong>{{ $inventories->count() }}</strong><small>jenis tersedia</small></div>
            <span>▤</span>
        </div>
    </aside>
</div>

<div class="stock-toast{{ $errors->has('quantity') ? ' visible' : '' }}" data-stock-toast role="status" aria-live="polite" aria-atomic="true" @if(!$errors->has('quantity')) hidden @endif>
    <span class="stock-toast-icon" aria-hidden="true">!</span>
    <div>
        <strong data-stock-toast-title>{{ $errors->has('quantity') ? 'Periksa jumlah peminjaman' : 'Stok tidak mencukupi' }}</strong>
        <p data-stock-toast-message>{{ $errors->first('quantity') }}</p>
    </div>
    <button type="button" data-stock-toast-close aria-label="Tutup notifikasi">&times;</button>
</div>

<div class="loan-confirmation" data-loan-confirmation hidden>
    <section class="loan-confirmation-dialog" role="dialog" aria-modal="true" aria-labelledby="loan-confirmation-title" aria-describedby="loan-confirmation-description">
        <span class="loan-confirmation-icon" aria-hidden="true">?</span>
        <h3 id="loan-confirmation-title">Konfirmasi pengajuan</h3>
        <p id="loan-confirmation-description">Apakah kamu benar mau mengajukan peminjaman?</p>
        <div class="loan-confirmation-actions">
            <button type="button" class="loan-confirmation-no" data-loan-confirmation-no>Tidak</button>
            <button type="button" class="loan-confirmation-yes" data-loan-confirmation-yes>Ya, ajukan</button>
        </div>
    </section>
</div>

<script>
    const loanRequestForm = document.querySelector('[data-loan-request-form]');
    if (loanRequestForm) {
        const inventorySelect = loanRequestForm.querySelector('[data-inventory-select]');
        const quantityInput = loanRequestForm.querySelector('[data-loan-quantity]');
        const stockToast = document.querySelector('[data-stock-toast]');
        const stockToastTitle = stockToast.querySelector('[data-stock-toast-title]');
        const stockToastMessage = stockToast.querySelector('[data-stock-toast-message]');
        const studentUnitLimit = Number(loanRequestForm.dataset.studentUnitLimit);
        const confirmation = document.querySelector('[data-loan-confirmation]');
        const confirmationNo = confirmation.querySelector('[data-loan-confirmation-no]');
        const confirmationYes = confirmation.querySelector('[data-loan-confirmation-yes]');
        const submitButton = loanRequestForm.querySelector('button[type="submit"]');
        let stockToastTimeout;
        let lastStockWarning = '';
        let confirmationAccepted = false;

        const showStockToast = function (message, title) {
            stockToastTitle.textContent = title;
            stockToastMessage.textContent = message;
            stockToast.hidden = false;
            stockToast.classList.remove('visible');
            window.requestAnimationFrame(function () {
                stockToast.classList.add('visible');
            });
            window.clearTimeout(stockToastTimeout);
            stockToastTimeout = window.setTimeout(function () {
                stockToast.classList.remove('visible');
                window.setTimeout(function () {
                    stockToast.hidden = true;
                }, 200);
            }, 5000);
        };

        stockToast.querySelector('[data-stock-toast-close]').addEventListener('click', function () {
            window.clearTimeout(stockToastTimeout);
            stockToast.classList.remove('visible');
            window.setTimeout(function () {
                stockToast.hidden = true;
            }, 200);
        });

        const checkAvailableStock = function () {
            const selectedInventory = inventorySelect.options[inventorySelect.selectedIndex];
            const quantity = Number(quantityInput.value);
            const availableStock = Number(selectedInventory.dataset.availableStock);

            if (!selectedInventory.value || !quantity || quantity <= availableStock) {
                lastStockWarning = '';
                return true;
            }

            const warning = `Tidak bisa mengajukan ${quantity} unit ${selectedInventory.dataset.inventoryName}. Barang yang tersedia hanya ${availableStock} unit.`;
            if (warning !== lastStockWarning) {
                showStockToast(warning, 'Stok tidak mencukupi');
                lastStockWarning = warning;
            }

            return false;
        };

        const checkStudentUnitLimit = function () {
            const quantity = Number(quantityInput.value);
            if (!studentUnitLimit || !quantity || quantity <= studentUnitLimit) {
                return true;
            }

            showStockToast('Siswa hanya dapat meminjam maksimal 3 unit barang dalam satu pengajuan.', 'Melebihi batas peminjaman');
            return false;
        };

        inventorySelect.addEventListener('change', checkAvailableStock);
        quantityInput.addEventListener('input', checkAvailableStock);
        loanRequestForm.addEventListener('submit', function (event) {
            if (!checkStudentUnitLimit()) {
                event.preventDefault();
                return;
            }

            if (!checkAvailableStock()) {
                event.preventDefault();
                return;
            }

            if (confirmationAccepted) {
                confirmationAccepted = false;
                return;
            }

            event.preventDefault();
            confirmation.hidden = false;
            confirmationNo.focus();
        });

        const closeConfirmation = function () {
            confirmation.hidden = true;
            confirmationAccepted = false;
            submitButton.focus();
        };

        confirmationNo.addEventListener('click', closeConfirmation);
        confirmationYes.addEventListener('click', function () {
            confirmation.hidden = true;
            confirmationAccepted = true;
            loanRequestForm.requestSubmit();
        });
        confirmation.addEventListener('click', function (event) {
            if (event.target === confirmation) closeConfirmation();
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !confirmation.hidden) closeConfirmation();
        });
    }
</script>
@endsection
