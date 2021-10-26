<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Entities\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PharmacyController extends Controller
{
    public function index(Request $request): View
    {
        $title = $this->title . '| Точки самовывоза';
        $city = $request->cookie('city', $this->defaultCity);
        $paginator = Store::query()->active()->where('address', 'like', $city . '%')->paginate(20);

        return view('pharmacy.index', compact('title', 'paginator'));
    }

    public function show(Request $request, string $id): View
    {
        $title = $this->title;
        $city = $request->cookie('city', $this->defaultCity);
        if(!$store = Store::query()->find($id))
            throw new HttpException(400, 'Информации по аптеке не найдено.');

        return view('pharmacy.show', compact('title', 'store'));
    }
}
