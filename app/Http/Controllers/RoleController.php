<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_system' => false,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('role.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($role->is_system) {
            // Cannot rename a system role, but can update its description and permissions
            $role->update([
                'description' => $request->description,
            ]);
        } else {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
        }

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->sync([]);
        }

        return redirect()->route('role.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('role.index')->with('failed', 'Cannot delete a system role.');
        }
        
        if ($role->users()->count() > 0) {
            return redirect()->route('role.index')->with('failed', 'Cannot delete role because it is assigned to one or more users.');
        }

        $role->delete();

        return redirect()->route('role.index')->with('success', 'Role deleted successfully.');
    }
}
