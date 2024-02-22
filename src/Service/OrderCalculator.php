<?php

namespace App\Service;

use App\Entity\CartItem;

class OrderCalculator
{

    public float $total = 0.00;

    public function calculateSubtotal(CartItem $cartItem)
    {
        return $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
    }

    public function calculateTotal(CartItem $cartItem): float
    {
        $this->total = $this->calculateSubtotal($cartItem) + $this->total;

        return $this->total;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function resetTotal(): void
    {
        $this->total = 0.00;
    }
}