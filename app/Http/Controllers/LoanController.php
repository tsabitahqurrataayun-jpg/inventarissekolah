<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        $loans = auth()->user()->role === 'admin'
            ? Loan::with(['user', 'inventory'])->latest()->get()
            : auth()->user()->loans()->with('inventory')->latest()->get();

        return view('loans.index', compact('loans'));
    }

    public function returnedIndex(): View
    {
        $loans = Loan::with(['user', 'inventory'])
            ->where('status', 'returned')
            ->whereNotNull('returned_at')
            ->orderByDesc('returned_at')
            ->orderByDesc('id')
            ->get();

        return view('loans.returned', [
            'loans' => $loans,
            'returnedCount' => $loans->count(),
            'returnedUnits' => $loans->sum('quantity'),
        ]);
    }

    public function report(Request $request): View
    {
        $status = $request->query('status', 'all');
        $query = $this->loanReportQuery($status);

        $loans = $query->with(['user', 'inventory'])->latest()->get();
        $analytics = $this->reportAnalytics();

        $stats = [
            'total' => Loan::count(),
            'borrowed' => Loan::whereIn('status', ['approved', 'borrowed'])->count(),
            'borrowers' => Loan::distinct('user_id')->count('user_id'),
            'late' => Loan::whereIn('status', ['approved', 'borrowed'])->whereDate('due_at', '<', now()->toDateString())->count(),
            'pending' => Loan::whereIn('status', ['pending', 'return_pending'])->count(),
            'returned' => Loan::where('status', 'returned')->count(),
        ];

        $statusLabel = [
            'all' => 'Semua',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'late' => 'Terlambat',
            'pending' => 'Menunggu Verifikasi',
            'return_pending' => 'Menunggu Verifikasi Pengembalian',
        ];

        return view('reports.index', [
            'loans' => $loans,
            'status' => $status,
            'statusLabel' => $statusLabel[$status] ?? 'Semua',
            'stats' => $stats,
            'analytics' => $analytics,
            'currentStatus' => $status,
        ]);
    }

    public function printReport(Request $request): View
    {
        $status = $request->query('status', 'all');
        $loans = $this->loanReportQuery($status)->with(['user', 'inventory'])->latest()->get();

        return view('reports.print', [
            'loans' => $loans,
            'statusLabel' => $this->reportStatusLabels()[$status] ?? 'Semua',
            'analytics' => $this->reportAnalytics(),
            'totalQuantity' => $loans->sum('quantity'),
            'printedAt' => now()->translatedFormat('d F Y, H:i'),
        ]);
    }

    private function reportAnalytics(): array
    {
        $statusCounts = Loan::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusGroups = [
            ['label' => 'Dipinjam', 'color' => '#32805b', 'statuses' => ['approved', 'borrowed']],
            ['label' => 'Dikembalikan', 'color' => '#3978c5', 'statuses' => ['returned']],
            ['label' => 'Menunggu verifikasi', 'color' => '#d79a2b', 'statuses' => ['pending', 'return_pending']],
            ['label' => 'Ditolak', 'color' => '#cf5656', 'statuses' => ['rejected']],
        ];
        $statusData = collect($statusGroups)->map(function (array $group) use ($statusCounts): array {
            $group['count'] = collect($group['statuses'])->sum(fn (string $status): int => (int) ($statusCounts[$status] ?? 0));
            unset($group['statuses']);

            return $group;
        });
        $statusTotal = $statusData->sum('count');
        $statusData = $statusData->map(function (array $group) use ($statusTotal): array {
            $group['percentage'] = $statusTotal > 0 ? round($group['count'] / $statusTotal * 100) : 0;

            return $group;
        });

        $firstMonth = now()->startOfMonth()->subMonths(5);
        $monthlyCounts = Loan::query()
            ->whereDate('borrowed_at', '>=', $firstMonth->toDateString())
            ->whereDate('borrowed_at', '<=', now()->toDateString())
            ->pluck('borrowed_at')
            ->countBy(fn ($date): string => \Illuminate\Support\Carbon::parse($date)->format('Y-m'));

        $monthlyData = collect(range(0, 5))->map(function (int $offset) use ($firstMonth, $monthlyCounts): array {
            $month = $firstMonth->copy()->addMonths($offset);

            return [
                'label' => $month->translatedFormat('M'),
                'count' => (int) ($monthlyCounts[$month->format('Y-m')] ?? 0),
            ];
        });

        $topItems = Inventory::query()
            ->join('loans', 'inventories.id', '=', 'loans.inventory_id')
            ->whereIn('loans.status', ['approved', 'borrowed', 'returned'])
            ->select('inventories.name', 'inventories.category', DB::raw('SUM(loans.quantity) as quantity'))
            ->groupBy('inventories.id', 'inventories.name', 'inventories.category')
            ->orderByDesc('quantity')
            ->orderBy('inventories.name')
            ->limit(5)
            ->get();

        return [
            'statuses' => $statusData,
            'statusTotal' => $statusTotal,
            'months' => $monthlyData,
            'monthlyMax' => max(1, (int) $monthlyData->max('count')),
            'topItems' => $topItems,
        ];
    }

    public function exportReport(Request $request)
    {
        $status = $request->query('status', 'all');
        $loans = $this->loanReportQuery($status)->with(['user', 'inventory'])->latest()->get();
        $statusSlug = array_key_exists($status, $this->reportStatusLabels()) ? $status : 'all';
        $fileName = 'rekap-peminjaman-'.$statusSlug.'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($loans) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Nama Peminjam',
                'Peran',
                'Nama Inventaris',
                'Kode Inventaris',
                'Jumlah',
                'Tanggal Pinjam',
                'Jatuh Tempo',
                'Tanggal Kembali',
                'Status',
            ], ';', '"', '');

            foreach ($loans as $index => $loan) {
                fputcsv($handle, [
                    $index + 1,
                    $this->safeSpreadsheetText($loan->user?->name ?? '-'),
                    $loan->user?->role ?? 'siswa',
                    $this->safeSpreadsheetText($loan->inventory?->name ?? '-'),
                    $this->safeSpreadsheetText($loan->inventory?->code ?? '-'),
                    $loan->quantity.' unit',
                    $loan->borrowed_at?->format('d/m/Y') ?? '-',
                    $loan->due_at?->format('d/m/Y') ?? '-',
                    $loan->returned_at?->format('d/m/Y') ?? '-',
                    $this->loanStatusLabel($loan->status),
                ], ';', '"', '');
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    private function reportStatusLabels(): array
    {
        return [
            'all' => 'Semua',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'late' => 'Terlambat',
            'pending' => 'Menunggu Verifikasi',
            'return_pending' => 'Menunggu Verifikasi Pengembalian',
        ];
    }

    private function loanStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Verifikasi',
            'return_pending' => 'Menunggu Verifikasi Pengembalian',
            'returned' => 'Dikembalikan',
            'approved', 'borrowed' => 'Dipinjam',
            'rejected' => 'Ditolak',
            default => ucfirst($status),
        };
    }

    private function safeSpreadsheetText(string $value): string
    {
        return preg_match('/^\s*[=+\-@]/u', $value) === 1 ? "'".$value : $value;
    }

    protected function loanReportQuery(string $status = 'all')
    {
        $query = Loan::query();

        if ($status === 'pending') {
            return $query->whereIn('status', ['pending', 'return_pending']);
        }

        if ($status === 'returned') {
            return $query->where('status', 'returned');
        }

        if ($status === 'borrowed') {
            return $query->whereIn('status', ['approved', 'borrowed']);
        }

        if ($status === 'late') {
            return $query->whereIn('status', ['approved', 'borrowed'])
                ->whereDate('due_at', '<', now()->toDateString());
        }

        return $query;
    }

    public function create(): View
    {
        return view('loans.create', [
            'inventories' => Inventory::where('available_stock', '>', 0)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $quantityRules = ['required', 'integer', 'min:1'];
        if ($request->user()->role === 'siswa') {
            $quantityRules[] = 'max:3';
        }

        $data = $request->validate([
            'inventory_id' => ['required', 'exists:inventories,id'],
            'quantity' => $quantityRules,
            'borrowed_at' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'due_at' => ['required', 'date_format:Y-m-d', 'after_or_equal:borrowed_at'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'quantity.max' => 'Siswa hanya dapat meminjam maksimal 3 unit barang dalam satu pengajuan.',
        ]);
        $inventory = Inventory::findOrFail($data['inventory_id']);
        if ($data['quantity'] > $inventory->available_stock) {
            return back()
                ->withInput()
                ->withErrors([
                    'quantity' => "Tidak bisa mengajukan {$data['quantity']} unit {$inventory->name}. Barang yang tersedia hanya {$inventory->available_stock} unit.",
                ]);
        }
        Loan::create($data + [
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return redirect()->route('loans.index')->with('success', 'Pengajuan peminjaman dikirim untuk ditinjau admin.');
    }

    public function requestReturn(Request $request, Loan $loan): RedirectResponse
    {
        abort_unless($loan->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'return_evidence' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $returnRequested = DB::transaction(function () use ($loan, $data): bool {
            $lockedLoan = Loan::whereKey($loan->id)->lockForUpdate()->firstOrFail();
            if (! in_array($lockedLoan->status, ['approved', 'borrowed'], true)) {
                return false;
            }

            $evidencePath = $data['return_evidence']->store('loan-evidence/return', 'local');
            abort_if($evidencePath === false, 500, 'Bukti pengembalian gagal disimpan. Silakan coba lagi.');

            $lockedLoan->update([
                'return_evidence_path' => $evidencePath,
                'status' => 'return_pending',
            ]);

            return true;
        });

        if (! $returnRequested) {
            return redirect()->route('loans.index')->with('error', 'Bukti pengembalian sudah dikirim atau peminjaman ini tidak lagi aktif. Silakan tunggu verifikasi admin.');
        }

        return redirect()->route('loans.index')->with('success', 'Bukti pengembalian dikirim. Stok akan diperbarui setelah admin memverifikasi barang.');
    }

    public function showEvidence(Request $request, Loan $loan, string $type)
    {
        abort_unless($request->user()->role === 'admin' || $loan->user_id === $request->user()->id, 403);

        $path = match ($type) {
            'borrowing' => $loan->borrowing_evidence_path,
            'return' => $loan->return_evidence_path,
            default => null,
        };

        abort_if($path === null || ! Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function updateStatus(Request $request, Loan $loan): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected,borrowed,returned'],
            'rejection_reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:1000'],
        ]);
        DB::transaction(function () use ($loan, $data) {
            $loan = Loan::whereKey($loan->id)->lockForUpdate()->firstOrFail();
            $oldStatus = $loan->status;
            $newStatus = $data['status'];
            if ($newStatus === 'approved' && $oldStatus === 'pending') {
                $inventory = Inventory::whereKey($loan->inventory_id)->lockForUpdate()->first();
                abort_if($inventory->available_stock < $loan->quantity, 422, 'Stok sudah tidak mencukupi.');
                $inventory->decrement('available_stock', $loan->quantity);
            }
            if ($newStatus === 'rejected' && $oldStatus !== 'pending') {
                abort(422, 'Hanya pengajuan yang menunggu verifikasi yang dapat ditolak.');
            }
            if ($newStatus === 'borrowed' && $oldStatus !== 'approved') {
                abort(422, 'Status hanya dapat diubah menjadi dipinjam setelah disetujui.');
            }
            if ($newStatus === 'returned') {
                abort_unless($oldStatus === 'return_pending' && $loan->return_evidence_path, 422, 'Periksa bukti pengembalian sebelum menyetujui.');
                Inventory::whereKey($loan->inventory_id)->lockForUpdate()->firstOrFail()
                    ->increment('available_stock', $loan->quantity);
                $loan->returned_at = now();
            }
            abort_if($newStatus === 'approved' && $oldStatus !== 'pending', 422, 'Pengajuan ini tidak lagi menunggu verifikasi.');
            $loan->status = $newStatus;
            if ($newStatus === 'rejected') {
                $loan->rejection_reason = $data['rejection_reason'];
            }
            $loan->save();
        });

        return back()->with('success', 'Status peminjaman diperbarui.');
    }
}
