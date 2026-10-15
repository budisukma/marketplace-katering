<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    protected $fillable = ['user_id','company_name','address','phone','description','location','food_type'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function menus(): HasMany { return $this->hasMany(Menu::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
}
