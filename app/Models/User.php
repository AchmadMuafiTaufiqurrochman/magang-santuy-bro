<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // Tentukan akses ke panel
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Cek status aktif
        if ($this->status !== 'active') {
            return false;
        }

        // Cek role sesuai panel
        return match ($panel->getId()) {
            'admin' => $this->role === 'admin',
            'customer' => $this->role === 'customer',
            'technician' => $this->role === 'technician',
            default => false,
        };
    }
}

