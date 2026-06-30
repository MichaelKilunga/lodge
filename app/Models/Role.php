<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_system',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $permissionKey): bool
    {
        if ($this->name === 'Super') {
            return true;
        }

        return $this->permissions()->where('key', $permissionKey)->exists();
    }
}
