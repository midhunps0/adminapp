<?php
namespace Ynotz\AccessControl\Traits;

use Ynotz\AccessControl\Models\Role;
use Ynotz\AccessControl\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait WithRoles{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    public function hasRole($role): bool
    {
        if (is_int($role)) {
            return in_array($role, array_values($this->roles()->pluck('id')->toArray()));
        } elseif (is_string($role)) {
            return in_array($role, array_values($this->roles()->pluck('name')->toArray()));
        } elseif ($role instanceof Role) {
            return in_array($role->id, array_values($this->roles()->pluck('id')->toArray()));
        }
    }

    public function permissions(): array
    {
        $permissions = [];
        foreach ($this->roles as $role) {
            array_push($permissions, ...($role->permissions));
        }
        return $permissions;
    }

    public function hasPermissionTo($permission): bool
    {
        if (is_int($permission)) {
            return in_array($permission, array_values(collect($this->permissions())->pluck('id')->toArray()));
        } elseif (is_string($permission)) {
            return in_array($permission, array_values(collect($this->permissions())->pluck('name')->toArray()));
        } elseif ($permission instanceof Permission) {
            return in_array($permission->id, array_values(collect($this->permissions())->pluck('id')->toArray()));
        }
    }

    public function assignRole($role): bool
    {
        if (is_int($role)) {
            $theRole = Role::find($role);
        } elseif (is_string($role)) {
            $theRole = Role::where('name', $role)->get()->first();
        } elseif ($role instanceof Role) {
            $theRole = $role;
        }
        if ($theRole == null) {
            return false;
        }
        $this->roles()->attach($theRole->id);
        return true;
    }

    public function removeRole($role): bool
    {
        if (is_int($role)) {
            $theRole = Role::find($role);
        } elseif (is_string($role)) {
            $theRole = Role::where('name', $role)->get()->first();
        } elseif ($role instanceof Role) {
            $theRole = $role;
        }
        if ($theRole == null) {
            return false;
        }
        $this->roles()->detach($theRole->id);
        return true;
    }

    public function scopeWithRoles($query, $roles)
    {
        if (count($roles) > 0) {
            return $query;
        }
        if (is_int($roles[0])) {
            return $query->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            });
        }
        return $query->whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        });
    }
}