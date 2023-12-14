<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'products_id',
        'users_id',
    ];

    protected $hidden =  [];

    public function product() {
        return $this->hasOne(Product::class, 'id', 'products_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
