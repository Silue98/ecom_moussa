<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /** Affiche le formulaire de demande de réinitialisation */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /** Envoie le lien de réinitialisation par email */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(
            ['email' => ['required', 'email']],
            ['email.required' => "L'adresse e-mail est obligatoire.",
             'email.email'    => "L'adresse e-mail n'est pas valide."]
        );

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
