<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function get_product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function get_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction_detail()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }
}
