<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): RedirectResponse
    {
        Auth::login(User::register($request['name'], $request['email'], $request['phone'], $request['password']));
        $request->session()->regenerate();

        return back();
    }

    public function verify(string $token)
    {
        if (!$user = User::query()->where('confirm_token', $token)->first())
            return back()->with('error', 'Sorry your link cannot be identified.');

        try {
            $user->verify();
            return back()->with('success', 'Your e-mail is verified. You can now login.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
