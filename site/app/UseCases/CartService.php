<?php

namespace App\UseCases;

use App\Models\CartItem;
use App\Models\Store;
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

    public function getItem(string $productId): CartItem
    {
        $this->loadItems();
        foreach ($this->items as $i => $current) {
            if ($current->product_id === $productId)
                return $this->items[$i];
        }
        throw new \DomainException('Товар не найден.');
    }

    public function getItems(): Collection
    {
        $this->loadItems();
        return $this->items;
    }

    public function getTotal(): int
    {
        $this->loadItems();
        $total = 0;
        foreach ($this->items as $item) $total += $item->quantity;

        return $total;
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

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function add(CartItem $item): void
    {
        $this->loadItems();
        try {
            $current = $this->getItem($item->product_id);
            $current->plus($item->quantity);
        }
        catch (\DomainException $e) {
            $this->items->push($item);
        }
        $this->saveItems();
    }

    public function set(string $productId, int $quantity): void
    {
        $this->loadItems();
        $current = $this->getItem($productId);
        $current->changeQuantity($quantity);
        $this->saveItems();
    }

    public function setItems(Collection $items): void
    {
        $this->loadItems();
        $this->items = $items;
        $this->saveItems();
    }

    public function remove(string $productId): void
    {
        $this->loadItems();
        $this->items = $this->items->filter(fn(CartItem $item) => $item->product_id !== $productId);
        $this->saveItems();
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
        if (!$this->items->count()) {
            $this->items = session('cartItems', new Collection());
        }
    }

    private function saveItems(): void
    {
        session(['cartItems' => $this->items]);
    }
}
