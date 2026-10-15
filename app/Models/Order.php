<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = ['customer_id','merchant_id','delivery_date','delivery_address','notes','status','total'];
    protected function casts(): array { return ['delivery_date' => 'date', 'total' => 'decimal:2']; }
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function invoice(): HasOne { return $this->hasOne(Invoice::class); }
}
