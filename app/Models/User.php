<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',           // active / inactive
        'technician_status' // optional, misal untuk technician busy / available
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * ================================
     * RELASI KE ORDERS
     * ================================
     */

    // Orders yang dibuat sebagai customer
    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // Alias untuk customerOrders (digunakan di widget customer)
    public function orders(): HasMany
    {
        return $this->customerOrders();
    }

    // Orders yang ditugaskan ke technician
    public function technicianOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'technician_id');
    }

    // Relasi ke assignments (jika ada tabel order_assignments)
    public function orderAssignments(): HasMany
    {
        return $this->hasMany(OrderAssignment::class, 'technician_id');
    }

    /**
     * ================================
     * FILAMENT PANEL ACCESS
     * ================================
     */

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return match ($panel->getId()) {
            'admin'      => $this->role === 'admin',
            'customer'   => $this->role === 'customer',
            'technician' => $this->role === 'technician',
            default      => false,
        };
    }
}
