<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Afficher la liste des rôles
     */
    public function index()
    {
        $roles = Role::withCount('permissions', 'users')->orderBy('name')->paginate(20);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        // Grouper par préfixe (ex: "voir agents" -> "Agents")
        $permissionsGrouped = $permissions->groupBy(function($permission) {
            $parts = explode(' ', $permission->name, 2);
            return ucfirst($parts[1] ?? 'Général');
        });
        return view('admin.roles.create', compact('permissions', 'permissionsGrouped'));
    }

    /**
     * Enregistrer un nouveau rôle
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return redirect()->route('admin.roles.show', $role)
                ->with('success', 'Rôle créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la création du rôle: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un rôle
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        $allPermissions = Permission::orderBy('name')->get();
        return view('admin.roles.show', compact('role', 'allPermissions'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        // Grouper par préfixe (ex: "voir agents" -> "Agents")
        $permissionsGrouped = $permissions->groupBy(function($permission) {
            $parts = explode(' ', $permission->name, 2);
            return ucfirst($parts[1] ?? 'Général');
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'permissionsGrouped', 'rolePermissions'));
    }

    /**
     * Mettre à jour un rôle
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();
            return redirect()->route('admin.roles.show', $role)
                ->with('success', 'Rôle mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la mise à jour du rôle: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un rôle
     */
    public function destroy(Role $role)
    {
        $name = $role->name;
        $role->delete();
        return redirect()->route('admin.roles.index')
            ->with('success', "Le rôle {$name} a été supprimé.");
    }

    /**
     * Gérer les permissions d'un rôle
     */
    public function managePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return back()->with('success', 'Permissions mises à jour avec succès.');
    }
}
