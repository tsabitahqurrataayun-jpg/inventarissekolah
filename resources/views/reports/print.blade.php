<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Peminjaman {{ $statusLabel }} | SIPARIS</title>
    <style>
        :root {
            color: #172b45;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            max-width: 1200px;
            margin: 32px auto;
            padding: 0 24px;
        }

        .print-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 22px;
        }

        .print-actions button,
        .print-actions a {
            display: inline-block;
            padding: 10px 16px;
            border: 0;
            border-radius: 6px;
            background: #17365b;
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
        }

        .print-actions a {
            background: #edf1f6;
            color: #273c56;
        }

        .report-heading {
            padding-bottom: 18px;
            border-bottom: 2px solid #17365b;
        }

        .report-heading .brand {
            margin: 0 0 8px;
            color: #526983;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        .report-heading p {
            margin: 7px 0 0;
            color: #596b7f;
        }

        .report-meta {
            display: flex;
            gap: 30px;
            margin: 18px 0;
        }

        .report-meta div {
            display: grid;
            gap: 4px;
        }

        .report-meta span {
            color: #63758a;
            font-size: 10px;
            text-transform: uppercase;
        }

        .report-meta strong {
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            padding: 8px 7px;
            border: 1px solid #cbd4df;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        th {
            background: #eaf0f6;
            color: #1d344f;
            font-size: 10px;
            font-weight: 700;
        }

        td {
            color: #293b50;
            font-size: 10px;
            line-height: 1.35;
        }

        tbody tr:nth-child(even) {
            background: #f7f9fb;
        }

        .col-number { width: 4%; }
        .col-borrower { width: 15%; }
        .col-role { width: 8%; }
        .col-item { width: 16%; }
        .col-code { width: 9%; }
        .col-quantity { width: 8%; }
        .col-date { width: 10%; }
        .col-status { width: 10%; }

        .empty-row {
            padding: 22px;
            color: #63758a;
            text-align: center;
        }

        .signoff {
            display: flex;
            justify-content: flex-end;
            margin-top: 36px;
        }

        .signoff div {
            width: 220px;
            text-align: center;
        }

        .signoff p {
            margin: 0 0 64px;
        }

        .signoff strong {
            display: block;
            padding-top: 6px;
            border-top: 1px solid #52647a;
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        @media print {
            body {
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            .report-heading {
                padding-bottom: 10px;
            }

            h1 {
                font-size: 20px;
            }

            .report-meta {
                margin: 12px 0;
            }

            thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            th,
            td {
                padding: 6px 5px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .signoff {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <a href="{{ route('reports.index', ['status' => request('status', 'all')]) }}">Kembali ke Laporan</a>
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <header class="report-heading">
        <p class="brand">SIPARIS · SMK NEGERI 5 SURAKARTA</p>
        <h1>Rekap Peminjaman Inventaris</h1>
        <p>Status: {{ $statusLabel }}</p>
    </header>

    <section class="report-meta" aria-label="Ringkasan laporan">
        <div><span>Tanggal dicetak</span><strong>{{ $printedAt }}</strong></div>
        <div><span>Jumlah transaksi</span><strong>{{ $loans->count() }}</strong></div>
        <div><span>Total barang dipinjam</span><strong>{{ $totalQuantity }} unit</strong></div>
    </section>

    <table>
        <thead>
            <tr>
                <th class="col-number">No.</th>
                <th class="col-borrower">Nama Peminjam</th>
                <th class="col-role">Peran</th>
                <th class="col-item">Nama Inventaris</th>
                <th class="col-code">Kode</th>
                <th class="col-quantity">Jumlah</th>
                <th class="col-date">Tanggal Pinjam</th>
                <th class="col-date">Jatuh Tempo</th>
                <th class="col-date">Tanggal Kembali</th>
                <th class="col-status">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $index => $loan)
                @php
                    $loanStatusLabel = match($loan->status) {
                        'pending' => 'Menunggu Verifikasi',
                        'return_pending' => 'Menunggu Verifikasi Pengembalian',
                        'returned' => 'Dikembalikan',
                        'approved', 'borrowed' => 'Dipinjam',
                        'rejected' => 'Ditolak',
                        default => ucfirst($loan->status),
                    };
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $loan->user?->name ?? '-' }}</td>
                    <td>{{ ucfirst($loan->user?->role ?? 'siswa') }}</td>
                    <td>{{ $loan->inventory?->name ?? '-' }}</td>
                    <td>{{ $loan->inventory?->code ?? '-' }}</td>
                    <td>{{ $loan->quantity }} unit</td>
                    <td>{{ $loan->borrowed_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $loan->due_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $loan->returned_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $loanStatusLabel }}</td>
                </tr>
            @empty
                <tr>
                    <td class="empty-row" colspan="10">Tidak ada transaksi untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer class="signoff">
        <div>
            <p>Petugas Inventaris</p>
            <strong>Admin SIPARIS</strong>
        </div>
    </footer>
</body>
</html>
