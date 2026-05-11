<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Afficher la liste des permissions
     */
    public function index()
    {
        $permissions = Permission::with('roles')->orderBy('name')->paginate(50);
        // Extraire les modules à partir des noms de permissions
        $modules = Permission::get()->map(function($permission) {
            $parts = explode(' ', $permission->name, 2);
            return ucfirst($parts[1] ?? 'Général');
        })->unique()->sort()->values();
        return view('admin.permissions.index', compact('permissions', 'modules'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $modules = ['Agents', 'Utilisateurs', 'Rôles', 'Permissions', 'Partenaires', 'Demandes', 'Paramètres', 'Import', 'Dashboard'];
        return view('admin.permissions.create', compact('modules'));
    }

    /**
     * Enregistrer une nouvelle permission
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            Permission::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission créée avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une permission
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        $allRoles = \Spatie\Permission\Models\Role::all();
        return view('admin.permissions.show', compact('permission', 'allRoles'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Permission $permission)
    {
        $modules = ['Agents', 'Utilisateurs', 'Rôles', 'Permissions', 'Partenaires', 'Demandes', 'Paramètres', 'Import', 'Dashboard'];
        return view('admin.permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Mettre à jour une permission
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $permission->update([
                'name' => $request->name,
            ]);

            return redirect()->route('admin.permissions.show', $permission)
                ->with('success', 'Permission mise à jour avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une permission
     */
    public function destroy(Permission $permission)
    {
        $name = $permission->name;
        $permission->delete();
        return redirect()->route('admin.permissions.index')
            ->with('success', "La permission {$name} a été supprimée.");
    }
}
