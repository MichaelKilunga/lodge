<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Interface\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $users = $this->userRepository->showUser($request);
        $customers = $this->userRepository->showCustomer($request);

        return view('user.index', [
            'users' => $users,
            'customers' => $customers,
        ]);
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'Customer')->get();
        return view('user.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        activity()->causedBy(auth()->user())->log('User '.$request->name.' created');
        
        $role = Role::find($request->role_id);
        $request->merge(['role' => $role->name]);

        $user = $this->userRepository->store($request);

        return redirect()->route('user.index')->with('success', 'User '.$user->name.' created');
    }

    public function show(User $user)
    {
        activity()->causedBy(auth()->user())->log('User '.$user->name.' viewed');
        if ($user->role === 'Customer') {
            $customer = Customer::where('user_id', $user->id)->first();

            return view('customer.show', [
                'customer' => $customer,
            ]);
        }

        return view('user.show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return view('user.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(User $user, UpdateCustomerRequest $request)
    {
        activity()->causedBy(auth()->user())->log('User '.$user->name.' updated');
        
        if ($request->has('role_id')) {
            $role = Role::find($request->role_id);
            if ($role) {
                $request->merge(['role' => $role->name]);
            }
        }
        
        $user->update($request->all());

        if ($user->isCustomer()) {
            $user->customer->update([
                'name' => $request->name,
            ]);
        }

        return redirect()->route('user.index')->with('success', 'User '.$user->name.' udpated!');
    }

    public function destroy(User $user)
    {
        activity()->causedBy(auth()->user())->log('User '.$user->name.' updated');
        try {
            $user->delete();

            return redirect()->route('user.index')->with('success', 'User '.$user->name.' deleted!');
        } catch (\Exception $e) {
            return redirect()->route('user.index')->with('failed', 'Customer '.$user->name.' cannot be deleted! Error Code:'.$e->errorInfo[1]);
        }
    }
}
