<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Loan;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $loanScope = $user->role === 'admin'
            ? Loan::query()
            : $user->loans();
        $loans = $user->role === 'admin'
            ? Loan::with(['user', 'inventory'])->latest()->take(6)->get()
            : $user->loans()->with('inventory')->latest()->take(6)->get();

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
            'returnDates' => (clone $loanScope)->whereIn('status', ['approved', 'borrowed'])
                ->whereBetween('due_at', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
                ->get(['due_at'])
                ->groupBy(fn (Loan $loan): string => $loan->due_at->format('Y-m-d'))
                ->map->count(),
            'loans' => $loans,
        ]);
    }
}
