<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\EditProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(): View
    {
        $title = ' | Профиль';

        $user = Auth::user();
        $cartService = $this->cartService;
        return view('cabinet.index', compact('title', 'user', 'cartService'));
    }

    public function edit(): View
    {
        $title = ' | Изменить данные';

        $user = Auth::user();
        $cartService = $this->cartService;
        return view('cabinet.edit', compact('title', 'user', 'cartService'));
    }

    public function update(EditProfileRequest $request): RedirectResponse
    {
        Auth::user()->update($request->only(['name', 'email', 'phone']));
        return redirect()->route('cabinet.profile');
    }
}
