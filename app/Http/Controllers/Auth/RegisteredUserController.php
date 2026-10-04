<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Support\NotificationService;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $accountType = $request->input('account_type', User::ROLE_ANGGOTA);

    $request->validate([
        'account_type' => ['nullable', Rule::in([User::ROLE_PELANGGAN, User::ROLE_ANGGOTA])],
        'name' => ['required', 'string', 'max:255'],
        'no_hp' => ['required', 'string', 'max:20'],
        'tanggal_lahir' => [Rule::requiredIf($accountType === User::ROLE_ANGGOTA), 'nullable', 'date'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'terms' => ['accepted'],
    ], [
        'terms.accepted' => 'Anda harus menyetujui syarat & ketentuan.',
    ]);

    $user = User::create([
        'name' => $request->name,
        'no_hp' => $request->no_hp,
        'tanggal_lahir' => $request->tanggal_lahir,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $accountType,
        'status' => $accountType === User::ROLE_ANGGOTA ? 'Calon' : 'Aktif',
    ]);

    event(new Registered($user));
    Auth::login($user);

    // Notify admin + pengurus about new user registration
    NotificationService::userBaruTerdaftar($user);

    return redirect(route($user->dashboardRouteName(), absolute: false));
}
}
