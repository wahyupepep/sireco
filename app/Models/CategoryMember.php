<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMember extends Model
{
    use HasFactory;
    protected $guarded = [];

    const TYPE_MEMBER = ['HARIAN', 'BULANAN', 'DEDICATED'];
}
