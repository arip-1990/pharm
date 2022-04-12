<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index(Request $request): View
    {
        $title = ' | Точки самовывоза';
        $paginator = Store::query()->active()->where('address', 'like', $this->city . '%')->paginate(20);
        $cartService = $this->cartService;

        return view('pharmacy.index', compact('title', 'paginator', 'cartService'));
    }

    public function show(Store $store): View
    {
        $cartService = $this->cartService;

        return view('pharmacy.show', compact('store', 'cartService'));
    }
}
