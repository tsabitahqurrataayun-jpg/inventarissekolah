<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'inventory_id', 'quantity', 'borrowed_at', 'due_at',
        'returned_at', 'status', 'notes', 'borrower_phone', 'borrowing_evidence_path',
        'return_evidence_path', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'date',
            'due_at' => 'date',
            'returned_at' => 'date',
        ];
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn (): string => match ($this->status) {
            'pending' => 'Menunggu verifikasi',
            'return_pending' => 'Menunggu verifikasi pengembalian',
            'approved', 'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
