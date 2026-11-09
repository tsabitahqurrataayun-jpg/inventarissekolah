@extends('layouts.app')

@section('content')
<div class="inventory-page inventory-catalog-page">
    <div class="inventory-page-header">
        <div>
            <p class="eyebrow">KATALOG BARANG</p>
            <h2>Daftar inventaris</h2>
            <p class="muted">Lihat foto dan ketersediaan barang, lalu ajukan peminjaman yang kamu perlukan.</p>
        </div>
        <a class="button primary inventory-catalog-request-link" href="{{ route('loans.create') }}">Ajukan peminjaman <span aria-hidden="true">→</span></a>
    </div>

    <section class="inventory-catalog-grid" aria-label="Daftar inventaris sekolah">
        @forelse($inventories as $inventory)
            <article class="inventory-catalog-card">
                @if($inventory->image_path)
                    <img class="inventory-catalog-image" src="{{ route('inventories.image', $inventory) }}" alt="Foto {{ $inventory->name }}" loading="lazy">
                @else
                    <div class="inventory-catalog-image inventory-catalog-placeholder" role="img" aria-label="Foto {{ $inventory->name }} belum tersedia">
                        <span aria-hidden="true">▤</span>
                        <small>Foto belum tersedia</small>
                    </div>
                @endif
                <div class="inventory-catalog-details">
                    <div class="inventory-catalog-category">{{ $inventory->category }}</div>
                    <h3>{{ $inventory->name }}</h3>
                    <p class="inventory-catalog-code">{{ $inventory->code }}</p>
                    @if($inventory->description)
                        <p class="inventory-catalog-description">{{ $inventory->description }}</p>
                    @endif
                    <div class="inventory-catalog-meta">
                        <span class="inventory-catalog-stock {{ $inventory->available_stock > 0 ? 'available' : 'unavailable' }}">
                            {{ $inventory->available_stock > 0 ? $inventory->available_stock.' unit tersedia' : 'Stok habis' }}
                        </span>
                        <span>Kondisi {{ $inventory->condition }}</span>
                    </div>
                    @if($inventory->available_stock > 0)
                        <a class="inventory-order-button" href="{{ route('loans.create', ['inventory_id' => $inventory->id]) }}">Klik untuk memesan <span aria-hidden="true">→</span></a>
                    @else
                        <button class="inventory-order-button" type="button" disabled>Stok tidak tersedia</button>
                    @endif
                </div>
            </article>
        @empty
            <div class="inventory-catalog-empty">
                <span aria-hidden="true">▤</span>
                <h3>Belum ada inventaris</h3>
                <p>Daftar barang akan muncul di sini setelah dicatat oleh admin.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection
