<?php

namespace App\Contracts;

use App\Models\Basket;

interface SpecialOfferInterface
{
    public function calculateDiscount(Basket $basket): float;
}