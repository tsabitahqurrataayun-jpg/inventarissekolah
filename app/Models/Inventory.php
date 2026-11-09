<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'category', 'description', 'total_stock',
        'available_stock', 'condition', 'image_path',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
