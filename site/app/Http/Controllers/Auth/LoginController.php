<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Entities\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LoginController extends Controller
{
    public function login(LoginRequest $request): RedirectResponse
    {
        if (Auth::attempt($request->only(['phone', 'password']), $request->filled('remember'))) {
            $request->session()->regenerate();
            if (Auth::user()->isWait()) {
                Auth::logout();
                return back()->with('error', 'You need to confirm your account. Please check your email.');
            }
            return back();
        }

        return back()->with('error', trans('auth.failed'));
    }

    public function verify(Request $request): RedirectResponse
    {
        $this->validate($request, ['token' => 'required|string']);

        if (!$session = $request->session()->get('auth'))
            throw new BadRequestHttpException('Missing token info.');

        $user = User::findOrFail($session['id']);

        if ($request['token'] === $session['token']) {
            $request->session()->flush();
            Auth::login($user, $session['remember']);
            return redirect()->intended(route('cabinet.home'));
        }

        throw ValidationException::withMessages(['token' => ['Invalid auth token.']]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        return back();
    }
}
