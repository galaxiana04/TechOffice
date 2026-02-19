<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'rule',
        'role_id',
        'unit_id', // tambahkan ini
        'waphonenumber',
        'telegram_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function memoSekdivAccesses()
    {
        return $this->hasMany(MemoSekdivAccess::class);
    }

    public function eventParticipations()
    {
        return $this->hasMany(EventParticipant::class);
    }
    // Monitoring User
    public function progressHistories()
    {
        return $this->hasMany(Newprogressreporthistory::class, 'drafter', 'initial');
    }

    public function progressHistoriesAsChecker()
    {
        return $this->hasMany(Newprogressreporthistory::class, 'checker', 'initial');
    }


}
