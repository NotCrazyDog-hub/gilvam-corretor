<?php

namespace App\Domains\Profile\Http\Controllers;

use App\Domains\Profile\Http\Requests\ProfileUpdateRequest;
use App\Domains\Shared\Services\ImageStorage;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->except(['avatar', 'remove_avatar']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            $imagemAnterior = $user->avatar;
            $diskAnterior = $user->avatar_disk;
            $imagem = app(ImageStorage::class)->store($request->file('avatar'), 'avatars');

            $user->avatar = $imagem['path'];
            $user->avatar_disk = $imagem['disk'];
            $user->save();

            if ($imagemAnterior) {
                app(ImageStorage::class)->delete($imagemAnterior, $diskAnterior);
            }
        } elseif ($request->boolean('remove_avatar') && $user->avatar) {
            $imagemAnterior = $user->avatar;
            $diskAnterior = $user->avatar_disk;
            $user->avatar = null;
            $user->save();

            app(ImageStorage::class)->delete($imagemAnterior, $diskAnterior);
        } else {
            $user->save();
        }

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
}
