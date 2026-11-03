<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        return view('inventories.index', ['inventories' => Inventory::latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:inventories,code'],
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string'],
            'total_stock' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'string', 'max:30'],
        ]);
        $data['available_stock'] = $data['total_stock'];
        Inventory::create($data);

        return back()->with('success', 'Inventaris berhasil ditambahkan.');
    }

    public function update(Request $request, Inventory $inventory): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:60'],
            'total_stock' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'string', 'max:30'],
        ]);
        $usedStock = $inventory->total_stock - $inventory->available_stock;
        abort_if($data['total_stock'] < $usedStock, 422, 'Stok total tidak boleh lebih kecil dari stok yang sedang dipinjam.');
        $data['available_stock'] = $data['total_stock'] - $usedStock;
        $inventory->update($data);

        return back()->with('success', 'Inventaris berhasil diperbarui.');
    }

    public function destroy(Inventory $inventory): RedirectResponse
    {
        abort_if($inventory->loans()->whereIn('status', ['pending', 'approved', 'borrowed', 'return_pending'])->exists(), 422, 'Inventaris masih memiliki peminjaman aktif.');
        $inventory->delete();

        return back()->with('success', 'Inventaris berhasil dihapus.');
    }
}
