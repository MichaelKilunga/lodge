<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'role_id',
        'avatar',
        'password',
        'random_key',
    ];

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

    public function getAvatar()
    {
        if (! $this->avatar) {
            return asset('img/default/default-user.jpg');
        }

        return asset('img/user/'.$this->name.'-'.$this->id.'/'.$this->avatar);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function userRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function isCustomer()
    {
        // Check exact role string or role relationship
        if (strcasecmp((string) $this->role, 'Customer') === 0 || 
            ($this->role_id && $this->userRole && strcasecmp((string) $this->userRole->name, 'Customer') === 0)) {
            return true;
        }

        // Fallback: If user has a customer record and is not Super/Admin/Front Desk, treat as customer
        if ($this->customer()->exists() && !in_array(strtolower((string) $this->role), ['super', 'admin', 'front desk'])) {
            return true;
        }

        return false;
    }

    public function hasPermission(string $permission)
    {
        if (strcasecmp((string) $this->role, 'Super') === 0 || ($this->role_id && $this->userRole && strcasecmp((string) $this->userRole->name, 'Super') === 0)) {
            return true;
        }

        if (!$this->role_id || !$this->userRole) {
            return false;
        }

        return $this->userRole->hasPermission($permission);
    }

    public function isSuperAdmin(): bool
    {
        return strcasecmp((string) $this->role, 'Super') === 0 || 
               ($this->role_id && $this->userRole && strcasecmp((string) $this->userRole->name, 'Super') === 0);
    }

    public function isSystemAdminOrOwner(): bool
    {
        return $this->isSuperAdmin() || 
               ($this->role_id && $this->userRole && strcasecmp((string) $this->userRole->name, 'Owner') === 0) ||
               strcasecmp((string) $this->role, 'Owner') === 0;
    }
}

