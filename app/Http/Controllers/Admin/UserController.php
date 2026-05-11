<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware géré directement dans les routes (web.php)
    }

    /**
     * Liste des utilisateurs
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');

        $users = User::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->orderBy('name')
            ->paginate(25)
            ->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $roles = [
            'admin' => 'Admin - Accès complet à l\'administration',
            'dp' => 'DP - Direction des Partenariats',
            'rh' => 'RH - Ressources Humaines',
            'agent' => 'Agent - Accès limité'
        ];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Créer un utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:admin,dp,rh,agent',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher un utilisateur
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = [
            'admin' => 'Admin - Accès complet à l\'administration',
            'dp' => 'DP - Direction des Partenariats',
            'rh' => 'RH - Ressources Humaines',
            'agent' => 'Agent - Accès limité'
        ];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|confirmed|min:8',
            'role' => 'required|in:admin,dp,rh,agent',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $nom = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "L'utilisateur {$nom} a été supprimé.");
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     */
    public function resetPassword(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Mot de passe réinitialisé avec succès.');
    }

    /**
     * Désactiver/Activer un utilisateur
     */
    public function toggleStatus(string $id)
    {
        $user = User::findOrFail($id);

        // Empêcher la désactivation de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        // Utiliser email_verified_at comme indicateur de statut
        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            $message = 'Utilisateur désactivé.';
        } else {
            $user->email_verified_at = now();
            $message = 'Utilisateur activé.';
        }

        $user->save();

        return redirect()
            ->back()
            ->with('success', $message);
    }
}
