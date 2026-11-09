<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        return view('inventories.index', ['inventories' => Inventory::latest()->get()]);
    }

    public function catalog(): View
    {
        return view('inventories.catalog', [
            'inventories' => Inventory::orderBy('name')->get(),
        ]);
    }

    public function image(Inventory $inventory)
    {
        abort_if(! $inventory->image_path || ! Storage::disk('public')->exists($inventory->image_path), 404);

        return Storage::disk('public')->response($inventory->image_path, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
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
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $imagePath = $request->file('image')->store('inventories', 'public');
        abort_if($imagePath === false, 500, 'Foto inventaris gagal disimpan. Silakan coba lagi.');
        $data['image_path'] = $imagePath;
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $usedStock = $inventory->total_stock - $inventory->available_stock;
        abort_if($data['total_stock'] < $usedStock, 422, 'Stok total tidak boleh lebih kecil dari stok yang sedang dipinjam.');
        $data['available_stock'] = $data['total_stock'] - $usedStock;
        unset($data['image']);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('inventories', 'public');
            abort_if($imagePath === false, 500, 'Foto inventaris gagal disimpan. Silakan coba lagi.');
            $previousImagePath = $inventory->image_path;
            $data['image_path'] = $imagePath;
        }
        $inventory->update($data);
        if (isset($previousImagePath)) {
            Storage::disk('public')->delete($previousImagePath);
        }

        return back()->with('success', 'Inventaris berhasil diperbarui.');
    }

    public function destroy(Inventory $inventory): RedirectResponse
    {
        abort_if($inventory->loans()->whereIn('status', ['pending', 'approved', 'borrowed', 'return_pending'])->exists(), 422, 'Inventaris masih memiliki peminjaman aktif.');
        $inventory->delete();
        if ($inventory->image_path) {
            Storage::disk('public')->delete($inventory->image_path);
        }

        return back()->with('success', 'Inventaris berhasil dihapus.');
    }
}
