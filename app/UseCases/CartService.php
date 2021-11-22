<?php

namespace App\UseCases;

use App\Entities\CartItem;
use App\Entities\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    private ?Store $store = null;
    private Collection $items;

    public function __construct()
    {
        $this->items = new Collection();
    }

    public function getItem(string $id)
    {
        $this->loadItems();
        foreach ($this->items as $i => $current) {
            if ($current->product_id === $id)
                return $this->items[$i];
        }
        throw new \DomainException('Товар не найден.');
    }

    public function getItems(): Collection
    {
        $this->loadItems();
        return $this->items;
    }

    public function getAmount(): float
    {
        $this->loadItems();
        $total = 0;
        foreach ($this->items as $item) $total += $item->getAmount($this->store);

        return $total;
    }

    public function getTotalAmount(): float
    {
        $this->loadItems();
        $total = 0;
        // TODO
        foreach ($this->items as $item) $total += $item->getAmount($this->store);

        return $total;
    }

    public function setStore(Store $store): void
    {
        $this->store = $store;
    }

    public function add(CartItem $item): void
    {
        $this->loadItems();
        foreach ($this->items as $current) {
            if ($current->product_id === $item->product_id) {
                $current->plus($item->quantity);
                $this->saveItems();
                return;
            }
        }
        $this->items->add($item);
        $this->saveItems();
    }

    public function set(string $id, int $quantity): void
    {
        $this->loadItems();
        foreach ($this->items as $current) {
            if ($current->product_id === $id) {
                $current->changeQuantity($quantity);
                $this->saveItems();
                return;
            }
        }
        throw new \DomainException('Товар не найден.');
    }

    public function remove(string $id): void
    {
        $this->loadItems();
        foreach ($this->items as $i => $current) {
            if ($current->product_id === $id) {
                unset($this->items[$i]);
                $this->saveItems();
                return;
            }
        }
        throw new \DomainException('Товар не найден.');
    }

    public function clear(): void
    {
        $this->items = new Collection();
        $this->saveItems();
    }

//    public function getCost(): Cost
//    {
//        $this->loadItems();
//        $cost = 0;
//        foreach ($this->items as $item) $cost += $item->getCost();
//        return new Cost($cost);
//    }

    private function loadItems(): void
    {
        if (!$this->items->count())
            $this->items = Auth::check() ? Auth::user()->cartItems : session('cartItems', new Collection());
    }

    private function saveItems(): void
    {
        if (Auth::check())
            Auth::user()->cartItems()->saveMany($this->items);
        else
            session(['cartItems' => $this->items]);
    }
}
