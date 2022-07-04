<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function history_transaction()
    {
        return $this->hasOne(HistoryTransaction::class, 'reservation_id');
    }
}
