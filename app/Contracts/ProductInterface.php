<?php

namespace App\Contracts;

interface ProductInterface
{
    public function getName(): string;
    public function getPrice(): float;
    public function getCode(): string;
}