<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\InviteCode;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['required', 'string', 'max:20'],
        ]);

        // Validate invite code
        $inviteCode = InviteCode::where('code', strtoupper($request->invite_code))
            ->where('is_active', true)
            ->first();

        if (!$inviteCode) {
            return back()->withErrors([
                'invite_code' => 'The provided invite code is invalid or has been deactivated.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        if (!$inviteCode->canBeUsed()) {
            return back()->withErrors([
                'invite_code' => 'This invite code has already been used and cannot be used again.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Create user with collaborator role by default
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'collaborator', // Set default role
        ]);

        // Mark invite code as used
        $inviteCode->markAsUsed($user->id);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
