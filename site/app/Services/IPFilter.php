<?php

namespace App\Services;

use App\Entities\User;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class IPFilter
{
//    public function __construct(private string $ip, private ?User $user = null) {}
//
//    public function filter()
//    {
//        /** @var PriceLimit $limit */
//        if ($limit = PriceLimit::query()->where('ip', $this->ip)) {
//            if ((time() - $limit->last_request) < (1 / $limit->tps) or $limit->isExpired())
//                throw new TooManyRequestsHttpException('Исчерпан лимит на просмотр цен');
//        }
//        else
//            $limit = PriceLimit::create($this->ip);
//
//        $limit->request();
//        if ($this->user and !$limit->user) {
//            $limit->reset();
//            $limit->user()->associate($this->user);
//        }
//        elseif (!$this->user and $limit->user) {
//            $limit->reset();
//            $limit->user_id = null;
//        }
//
//        if ($limit->expires < (new \DateTimeImmutable()))
//            $limit->reset();
//
//        $limit->save();
//    }
}
