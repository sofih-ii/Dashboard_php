<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'two_factor_enabled'    => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    public function generateTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code'       => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);
    }

    public function clearTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
        ]);
    }
}