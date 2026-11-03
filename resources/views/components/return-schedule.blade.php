<aside class="return-schedule-panel">
    <div class="return-schedule-heading">
        <div>
            <p class="schedule-kicker">JADWAL PENGEMBALIAN</p>
            <h3>Pengembalian terdekat</h3>
            <p>Atur pengingat agar tidak lupa jadwal pengembalian.</p>
        </div>
        <span class="schedule-count">{{ $upcomingReturns->count() }} jadwal</span>
    </div>

    <div class="return-schedule-content">
        <div class="return-calendar" aria-label="Kalender pengembalian {{ $calendarMonth->translatedFormat('F Y') }}">
            <div class="calendar-heading">
                <strong>{{ $calendarMonth->translatedFormat('F Y') }}</strong>
                <span class="calendar-legend"><i></i> Ada pengembalian</span>
            </div>
            <div class="calendar-weekdays">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
                    <span>{{ $weekday }}</span>
                @endforeach
            </div>
            <div class="calendar-days">
                @for($blank = 1; $blank < $calendarMonth->dayOfWeekIso; $blank++)
                    <span class="calendar-day empty-day"></span>
                @endfor
                @for($day = 1; $day <= $calendarMonth->daysInMonth; $day++)
                    @php($dateKey = $calendarMonth->copy()->day($day)->format('Y-m-d'))
                    <span class="calendar-day {{ $dateKey === today()->format('Y-m-d') ? 'today' : '' }} {{ $returnDates->has($dateKey) ? 'has-return' : '' }}">
                        {{ $day }}
                        @if($returnDates->has($dateKey))<b>{{ $returnDates->get($dateKey) }}</b>@endif
                    </span>
                @endfor
            </div>
        </div>

        <div class="upcoming-return-list">
            <div class="upcoming-list-heading"><strong>Akan dikembalikan</strong><a href="{{ route('loans.index') }}">Lihat semua</a></div>
            @forelse($upcomingReturns as $loan)
                <div class="upcoming-return-item" data-reminder-id="{{ $loan->id }}" data-due-date="{{ $loan->due_at->format('Y-m-d') }}" data-item-name="{{ $loan->inventory->name }}">
                    <span class="upcoming-item-icon">▣</span>
                    <div class="upcoming-item-copy">
                        <strong>{{ $loan->inventory->name }}</strong>
                        <small>{{ $loan->quantity }} unit @if(auth()->user()->role === 'admin') · {{ $loan->user->name }} @endif</small>
                        <span>{{ $loan->due_at->isToday() ? 'Hari ini' : $loan->due_at->translatedFormat('l, d F') }}</span>
                    </div>
                    <button type="button" class="reminder-button" data-reminder-button aria-label="Aktifkan pengingat untuk {{ $loan->inventory->name }}" title="Aktifkan pengingat">☆</button>
                </div>
            @empty
                <div class="schedule-empty">Belum ada jadwal pengembalian aktif.</div>
            @endforelse
        </div>
    </div>

    <div class="reminder-note"><span>i</span><div><strong>Pengingat pengembalian</strong><small>Aktifkan ikon bintang pada barang agar browser mengingatkan jadwalnya.</small></div></div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-reminder-button]').forEach(function (button) {
            const item = button.closest('[data-reminder-id]');
            const key = 'siparis-return-reminder-' + item.dataset.reminderId;
            const updateButton = function (active) {
                button.textContent = active ? '★' : '☆';
                button.classList.toggle('active', active);
                button.setAttribute('aria-label', active ? 'Nonaktifkan pengingat' : 'Aktifkan pengingat');
            };

            updateButton(localStorage.getItem(key) === '1');
            button.addEventListener('click', function () {
                const active = localStorage.getItem(key) !== '1';
                if (active && 'Notification' in window && Notification.permission === 'default') {
                    Notification.requestPermission();
                }
                localStorage.setItem(key, active ? '1' : '0');
                updateButton(active);
            });
        });
    });
</script>
