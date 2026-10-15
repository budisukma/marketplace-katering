<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $fillable = ['merchant_id','name','description','photo','price','is_available'];
    protected function casts(): array { return ['price' => 'decimal:2', 'is_available' => 'boolean']; }
    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
}
