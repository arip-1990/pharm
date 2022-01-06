<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Entities\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index(Request $request): View
    {
        $title = ' | Точки самовывоза';
        $city = $request->cookie('city', config('data.city')[0]);
        $paginator = Store::query()->active()->where('address', 'like', $city . '%')->paginate(20);
        $cartService = $this->cartService;

        return view('pharmacy.index', compact('title', 'paginator', 'cartService'));
    }

    public function show(Store $store): View
    {
        $cartService = $this->cartService;

        return view('pharmacy.show', compact('store', 'cartService'));
    }
}
