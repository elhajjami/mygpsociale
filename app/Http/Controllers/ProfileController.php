<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's admin profile form.
     */
    public function adminEdit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        $user = $request->user();

        // Supprimer l'ancienne photo si demandé
        if ($request->has('remove_photo')) {
            if ($user->profile_photo_path) {
                $oldPath = public_path('avatars/' . $user->profile_photo_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $user->profile_photo_path = null;
                $user->save();
            }
            return Redirect::route('profile.edit')->with('status', 'photo-deleted');
        }

        // Upload nouvelle photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if ($user->profile_photo_path) {
                $oldPath = public_path('avatars/' . $user->profile_photo_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Générer nom unique
            $fileName = 'user_' . $user->id . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();

            // Déplacer le fichier
            $request->file('photo')->move(public_path('avatars'), $fileName);

            $user->profile_photo_path = $fileName;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'photo-updated');
    }
}
