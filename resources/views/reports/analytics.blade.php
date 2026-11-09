@php
    $chartAngle = 0;
    $chartStops = [];
    foreach ($analytics['statuses'] as $status) {
        if ($status['count'] === 0 || $analytics['statusTotal'] === 0) {
            continue;
        }

        $nextAngle = $chartAngle + ($status['count'] / $analytics['statusTotal'] * 360);
        $chartStops[] = $status['color'].' '.$chartAngle.'deg '.$nextAngle.'deg';
        $chartAngle = $nextAngle;
    }
    $pieGradient = $chartStops ? implode(', ', $chartStops) : '#e8edf2';
@endphp

<section class="report-analytics" aria-label="Visualisasi laporan peminjaman">
    <div class="report-chart-card report-status-chart">
        <div class="report-chart-heading">
            <div>
                <h3>Diagram status peminjaman</h3>
                <p>Ringkasan dari seluruh transaksi</p>
            </div>
        </div>
        <div class="report-status-chart-content">
            <div class="report-donut" role="img" aria-label="Diagram lingkaran {{ $analytics['statusTotal'] }} transaksi berdasarkan status" style="--report-pie: conic-gradient({{ $pieGradient }})">
                <div class="report-donut-center">
                    <strong>{{ $analytics['statusTotal'] }}</strong>
                    <span>transaksi</span>
                </div>
            </div>
            <ul class="report-chart-legend">
                @foreach($analytics['statuses'] as $status)
                    <li>
                        <span class="report-legend-label"><i style="--legend-color: {{ $status['color'] }}"></i>{{ $status['label'] }}</span>
                        <strong>{{ $status['count'] }} <small>({{ $status['percentage'] }}%)</small></strong>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="report-chart-card report-month-chart">
        <div class="report-chart-heading">
            <div>
                <h3>Tren transaksi 6 bulan</h3>
                <p>Jumlah transaksi berdasarkan tanggal peminjaman</p>
            </div>
        </div>
        <div class="report-bar-chart" role="img" aria-label="Grafik batang jumlah transaksi dalam enam bulan terakhir">
            @foreach($analytics['months'] as $month)
                <div class="report-bar-column" aria-label="{{ $month['label'] }}: {{ $month['count'] }} transaksi">
                    <strong>{{ $month['count'] }}</strong>
                    <div class="report-bar-track">
                        <span style="height: {{ $month['count'] > 0 ? max(4, $month['count'] / $analytics['monthlyMax'] * 100) : 0 }}%"></span>
                    </div>
                    <small>{{ $month['label'] }}</small>
                </div>
            @endforeach
        </div>
    </div>

    <div class="report-chart-card report-top-items">
        <div class="report-chart-heading">
            <div>
                <h3>Barang paling sering dipinjam</h3>
                <p>Diurutkan berdasarkan total unit yang disetujui, dipinjam, atau dikembalikan</p>
            </div>
        </div>
        <div class="report-top-items-table-wrap">
            <table class="report-top-items-table">
                <thead>
                    <tr>
                        <th>Nama inventaris</th>
                        <th>Kategori</th>
                        <th>Total unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($analytics['topItems'] as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->category }}</td>
                            <td>{{ $item->quantity }} unit</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="report-chart-empty">Belum ada data barang dipinjam.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
