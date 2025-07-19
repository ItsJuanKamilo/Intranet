<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $usersWithoutRoleCount = User::whereNull('role_id')->count();
        $roles = Role::withCount('users')->orderBy('name', 'asc')->get();
        return view('admin.roles.index', compact('roles', 'usersWithoutRoleCount'));

    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array|exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);
        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado con éxito.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id); // Encuentra el rol
        $permissions = Permission::all(); // Obtiene todos los permisos
        $rolePermissions = $role->permissions->pluck('id')->toArray(); // Obtiene los IDs de permisos asignados

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'array|exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);
        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado con éxito.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Informatica') {
            return redirect()->route('admin.roles.index')->with('error', 'No puedes eliminar el rol.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado con éxito.');
    }
}
