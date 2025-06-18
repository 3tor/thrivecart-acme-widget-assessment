<?php

namespace App\Models;

use App\Contracts\ProductInterface;

class Product implements ProductInterface
{
    protected string $name;
    protected float $price;
    protected string $code;

    public function __construct(string $name, float $price, string $code)
    {
        $this->name = $name;
        $this->price = $price;
        $this->code = $code;
    }

    public function getName(): string 
    {
        return $this->name;
    }
    
    public function getPrice(): float 
    {
        return $this->price;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}