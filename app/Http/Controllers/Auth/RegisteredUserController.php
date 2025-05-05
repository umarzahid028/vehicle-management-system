<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        } catch (\Illuminate\Database\QueryException $e) {
            // Check specifically for duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()
                    ->withErrors(['email' => 'A user with this email already exists.']);
            }
            
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'An error occurred during registration. Please try again.']);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }
}
