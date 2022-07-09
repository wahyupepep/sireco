<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function notification_admin()
    {
        return $this->hasOne(Notification::class, 'reservation_id')->where('user_id', Auth::user()->id);
    }
}
