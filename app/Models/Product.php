<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function get_brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function get_category()
    {
        return $this->belongsTo(TypeGood::class, 'type_good_id');
    }
}
