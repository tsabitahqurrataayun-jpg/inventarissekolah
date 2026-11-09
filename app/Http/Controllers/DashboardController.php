<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Loan;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $loanScope = $user->role === 'admin'
            ? Loan::query()
            : $user->loans();
        $loans = $user->role === 'admin'
            ? Loan::with(['user', 'inventory'])
                ->orderByRaw("CASE WHEN status IN ('approved', 'borrowed', 'return_pending') THEN 0 WHEN status = 'returned' THEN 2 ELSE 1 END")
                ->latest()
                ->take(6)
                ->get()
            : $user->loans()->with('inventory')->latest()->take(6)->get();
        $overdueLoans = $user->role === 'admin'
            ? Loan::with(['user', 'inventory'])
                ->whereIn('status', ['approved', 'borrowed'])
                ->whereDate('due_at', '<', today())
                ->orderBy('due_at')
                ->get()
            : collect();
        $calendarReturns = (clone $loanScope)->with(['user', 'inventory'])
            ->whereIn('status', ['approved', 'borrowed'])
            ->whereBetween('due_at', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->get()
            ->groupBy(fn (Loan $loan): string => $loan->due_at->format('Y-m-d'));

        return view('dashboard', [
            'inventoryCount' => Inventory::count(),
            'availableCount' => Inventory::sum('available_stock'),
            'activeLoans' => (clone $loanScope)->whereIn('status', ['approved', 'borrowed'])->count(),
            'pendingLoans' => (clone $loanScope)->whereIn('status', ['pending', 'return_pending'])->count(),
            'lateLoans' => (clone $loanScope)->whereIn('status', ['approved', 'borrowed'])
                ->whereDate('due_at', '<', today())->count(),
            'returnedToday' => (clone $loanScope)->where('status', 'returned')
                ->whereDate('returned_at', today())->count(),
            'upcomingReturns' => (clone $loanScope)->with(['user', 'inventory'])
                ->whereIn('status', ['approved', 'borrowed'])
                ->whereDate('due_at', '>=', today())
                ->orderBy('due_at')
                ->take(6)
                ->get(),
            'calendarMonth' => Carbon::today()->startOfMonth(),
            'calendarReturns' => $calendarReturns,
            'returnDates' => $calendarReturns->map->count(),
            'overdueLoans' => $overdueLoans,
            'loans' => $loans,
        ]);
    }
}
