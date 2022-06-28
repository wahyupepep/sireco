<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    //     'role',
    //     'status'
    // ];

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const WORK_TYPE = ['Goverment', 'Lifestyle', 'Business', 'Community', 'Startup', 'Art', 'F&B', 'Media', 'Education', 'NGO', 'Perorangan'];
    const AGE = ['15-24', '25-34', '35-44', '45-54', '55+'];
    const HOBBY = ['Bisnis', 'Pemasaran & Komunikasi', 'Science & Tech', 'Kesehatan Jasmani & Mental', 'Seni Rupa', 'Permainan & Hiburan', 'Pengembangan Diri', 'Desain, Fesyen, & Kriya', 'Sosial & Lingkungan', 'Makanan & Minuman'];
}
