<?php

namespace App\Repositories\Implementation;

use App\Models\User;
use App\Repositories\Interface\UserRepositoryInterface;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function store($userData)
    {
        $user = new User;
        $user->name = $userData->name;
        $user->email = $userData->email;
        $user->phone = $userData->phone;
        $user->password = bcrypt($userData->password);
        $user->role = $userData->role;
        $user->role_id = $userData->role_id;
        $user->random_key = Str::random(60);
        $user->save();

        return $user;
    }

    public function showUser($request)
    {
        $customerRole = \App\Models\Role::where('name', 'Customer')->first();
        $customerRoleId = $customerRole ? $customerRole->id : null;

        return User::where('role', '!=', 'Customer')
            ->when($customerRoleId, function ($query) use ($customerRoleId) {
                $query->where(function ($q) use ($customerRoleId) {
                    $q->where('role_id', '!=', $customerRoleId)
                      ->orWhereNull('role_id');
                });
            })
            ->orderBy('id', 'DESC')
            ->when($request->qu, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('email', 'LIKE', '%'.$request->qu.'%')
                      ->orWhere('name', 'LIKE', '%'.$request->qu.'%');
                });
            })
            ->paginate(5, ['*'], 'users')
            ->appends($request->all());
    }

    public function showCustomer($request)
    {
        $customerRole = \App\Models\Role::where('name', 'Customer')->first();
        $customerRoleId = $customerRole ? $customerRole->id : null;

        return User::where('role', 'Customer')
            ->when($customerRoleId, function ($query) use ($customerRoleId) {
                $query->orWhere('role_id', $customerRoleId);
            })
            ->orderBy('id', 'DESC')
            ->when($request->qc, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('email', 'LIKE', '%'.$request->qc.'%')
                      ->orWhere('name', 'LIKE', '%'.$request->qc.'%');
                });
            })
            ->paginate(5, ['*'], 'customers')
            ->appends($request->all());
    }

}
