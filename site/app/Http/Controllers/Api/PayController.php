<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PayController
{
    public function handle(Request $request): Response
    {
        $data = [
            'amount' . $request->get('amount'),
            'mdOrder' . $request->get('mdOrder'),
            'operation' . $request->get('operation'),
            'status' . $request->get('status')
        ];
        $data = implode(';', $data) . ';';

        $publicKey = Storage::get('callback.cer');

        $binarySignature = hex2bin(strtolower($request->get('checksum')));
        $isVerify = openssl_verify($data, $binarySignature, $publicKey, OPENSSL_ALGO_SHA512);

        if ($isVerify !== 1) return new Response(status: SymfonyResponse::HTTP_PAYMENT_REQUIRED);

        $order = Order::query()->find((int)$request->get('orderNumber'));

        return new Response();
    }

    private function checkStatus(Order $order, string $operation, int $status): bool
    {
        switch ($operation) {
            case 'approved': break;
            case 'deposited':
                if ($status === 1) {
                    $order->sent();
                    return true;
                }
            case 'reversed': break;
            case 'refunded': break;
            case 'declinedByTimeout': break;
        }

        return false;
    }
}
