<?php

namespace App\Services;

use App\Contracts\SpecialOfferInterface;
use App\Models\Basket;

class BuyOneGetSecondHalfService implements SpecialOfferInterface
{
    private Basket $basket;

    /**
     * Calculate total discounts for products in a basket 
     */
    public function calculateDiscount(Basket $basket): float
    {
        $this->basket = $basket;
        $discount = 0.0;
        $products = $basket->getProducts();
        $productCodes = array_count_values(array_map(fn($product) => $product->getCode(), $products));

        foreach ($productCodes as $code => $count) {
            if ($code == 'R01') {
                $discount += $this->calculateProductDiscount($code, $count);
            }
        }

        return $discount;
    }

    /**
     * Calculate discount per product 
     */
    private function calculateProductDiscount(string $code, int $count): float
    {
        $discount = 0.0;
        $discountedCount = (int)($count / 2);
        $product = $this->basket->findProductByCode($code);

        if ($product) {
            $discount = $discountedCount * ($product->getPrice() / 2);
        }

        return $discount;
    }
}