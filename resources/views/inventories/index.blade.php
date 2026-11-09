@extends('layouts.app')

@section('content')
<div class="inventory-page">
    <div class="inventory-page-header">
        <div>
            <p class="eyebrow">KATALOG BARANG</p>
            <h2>Inventaris sekolah</h2>
            <p class="muted">Kelola stok, kondisi, dan ketersediaan barang.</p>
        </div>
    </div>

    <section class="inventory-card inventory-form-card" aria-labelledby="inventory-form-title">
        <div class="inventory-card-header">
            <div>
                <h3 id="inventory-form-title">Tambah inventaris</h3>
                <p>Lengkapi informasi barang yang akan dicatat.</p>
            </div>
            <span class="inventory-card-icon" aria-hidden="true">+</span>
        </div>

        <form method="POST" action="{{ route('inventories.store') }}" class="inventory-form-grid" enctype="multipart/form-data">
            @csrf
            <label>Kode aset
                <input name="code" placeholder="AST-020" required>
            </label>
            <label>Nama barang
                <input name="name" placeholder="Nama barang" required>
            </label>
            <label>Kategori
                <input name="category" placeholder="Elektronik" required>
            </label>
            <label>Stok total
                <input type="number" name="total_stock" min="1" placeholder="1" required>
            </label>
            <label>Kondisi
                <select name="condition">
                    <option>Baik</option>
                    <option>Perlu perawatan</option>
                    <option>Rusak</option>
                </select>
            </label>
            <label>Foto barang
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
                <small class="inventory-image-hint">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</small>
            </label>
            <div class="inventory-form-actions">
                <button class="inventory-submit" type="submit">Tambah inventaris <span aria-hidden="true">+</span></button>
            </div>
        </form>
    </section>

    <section class="inventory-card inventory-list-card" aria-labelledby="inventory-list-title">
        <div class="inventory-card-header inventory-list-header">
            <div>
                <h3 id="inventory-list-title">Daftar barang</h3>
                <p>Informasi stok dan kondisi inventaris sekolah.</p>
            </div>
            <span class="inventory-count">{{ $inventories->count() }} barang</span>
        </div>

        <div class="table-wrap inventory-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Aset</th>
                        <th>Foto</th>
                        <th>Kategori</th>
                        <th>Stok tersedia</th>
                        <th>Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inventory)
                        <tr>
                            <td class="inventory-asset">
                                <strong>{{ $inventory->name }}</strong>
                                <small>{{ $inventory->code }}</small>
                            </td>
                            <td>
                                @if($inventory->image_path)
                                    <img class="inventory-admin-thumbnail" src="{{ route('inventories.image', $inventory) }}" alt="Foto {{ $inventory->name }}">
                                @else
                                    <form method="POST" action="{{ route('inventories.update', $inventory) }}" enctype="multipart/form-data" class="inventory-photo-upload">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="name" value="{{ $inventory->name }}">
                                        <input type="hidden" name="category" value="{{ $inventory->category }}">
                                        <input type="hidden" name="total_stock" value="{{ $inventory->total_stock }}">
                                        <input type="hidden" name="condition" value="{{ $inventory->condition }}">
                                        <label class="inventory-photo-upload-label">
                                            <span>Unggah foto</span>
                                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
                                        </label>
                                        <button class="inventory-photo-save" type="submit">Simpan</button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ $inventory->category }}</td>
                            <td>
                                <span class="inventory-stock">
                                    <strong>{{ $inventory->available_stock }}</strong>
                                    <span>dari {{ $inventory->total_stock }} unit</span>
                                </span>
                            </td>
                            <td>
                                <span class="inventory-condition {{ $inventory->condition === 'Baik' ? 'good' : ($inventory->condition === 'Perlu perawatan' ? 'maintenance' : 'damaged') }}">
                                    <span aria-hidden="true"></span>{{ $inventory->condition }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('inventories.destroy', $inventory) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inventory-delete" onclick="return confirm('Hapus inventaris ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="inventory-empty">Belum ada inventaris. Tambahkan data pertama melalui formulir di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
