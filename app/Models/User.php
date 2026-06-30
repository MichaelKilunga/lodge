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
        'role',
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
        // Keep checking the string for legacy/hybrid compat, or check relationship
        return $this->role === 'Customer' || ($this->role_id && $this->userRole->name === 'Customer');
    }

    public function hasPermission(string $permission)
    {
        if ($this->role === 'Super' || ($this->role_id && $this->userRole->name === 'Super')) {
            return true;
        }

        if (!$this->role_id) {
            return false;
        }

        return $this->userRole->hasPermission($permission);
    }
}
