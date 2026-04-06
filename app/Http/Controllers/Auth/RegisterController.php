<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\RegisterRequest;
use App\Models\User;
use App\Notifications\WelcomeUser;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
        ]);

        // Notification de bienvenue
        $user->notify(new WelcomeUser());

        Auth::login($user);

        // Fusionner le panier invité avec le nouveau compte
        app(CartService::class)->mergeGuestCart($user->id);

        return redirect()->route('home')
            ->with('success', '🎉 Bienvenue ' . $user->name . ' ! Votre compte a été créé avec succès.');
    }
}
