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
        $this->middleware('permission:manage_staff')->except(['show', 'updateProfile']);
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
        $request->merge([
            'role' => $role->name,
            'role_id' => $role->id,
        ]);

        $user = $this->userRepository->store($request);

        return redirect()->route('user.index')->with('success', 'User '.$user->name.' created');
    }

    public function show(User $user)
    {
        activity()->causedBy(auth()->user())->log('User '.$user->name.' viewed');
        if ($user->isCustomer()) {
            $customer = Customer::where('user_id', $user->id)->first();
            if (!$customer) {
                $customer = new Customer([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'address' => '',
                    'job' => '',
                    'birthdate' => null,
                    'gender' => 'Male'
                ]);
            }
            // Bind user relation manually if transient
            $customer->setRelation('user', $user);

            return view('customer.show', [
                'customer' => $customer,
            ]);
        }

        return view('user.show', [
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        if (auth()->id() !== $user->id && !auth()->user()->hasPermission('Super')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string|max:255',
            'job' => 'nullable|string|max:255',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $path = public_path('img/user/' . $user->name . '-' . $user->id);
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $avatar->move($path, $filename);
            $user->avatar = $filename;
        }

        $user->save();

        if ($user->isCustomer()) {
            $customer = $user->customer ?: new Customer(['user_id' => $user->id]);
            $customer->name = $request->name;
            $customer->address = $request->address ?? $customer->address ?? '';
            $customer->job = $request->job ?? $customer->job ?? '';
            if ($request->filled('birthdate')) {
                $customer->birthdate = $request->birthdate;
            }
            if ($request->filled('gender')) {
                $customer->gender = $request->gender;
            }
            $customer->save();
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
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
                $user->role_id = $role->id;
                $user->role = $role->name;
            }
        }
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        if ($user->isCustomer()) {
            $user->customer->update([
                'name' => $request->name,
            ]);
        }

        return redirect()->route('user.index')->with('success', 'User '.$user->name.' updated!');
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
