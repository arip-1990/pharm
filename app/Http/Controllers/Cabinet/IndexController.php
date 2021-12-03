<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\EditProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(Request $request): View
    {
        $title = $this->title . ' | ' . 'Профиль';
        $city = $request->cookie('city', $this->defaultCity);

        $user = Auth::user();
        $cartService = $this->cartService;
        return view('cabinet.index', compact('title', 'city', 'user', 'cartService'));
    }

    public function edit(Request $request): View
    {
        $title = $this->title . ' | ' . 'Изменить данные';
        $city = $request->cookie('city', $this->defaultCity);

        $user = Auth::user();
        $cartService = $this->cartService;
        return view('cabinet.edit', compact('title', 'city', 'user', 'cartService'));
    }

    public function update(EditProfileRequest $request): RedirectResponse
    {
        Auth::user()->update($request->only(['name', 'email', 'phone']));
        return redirect()->route('cabinet.profile');
    }
}
