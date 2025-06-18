<?php

namespace App\Enums;

enum Offer: string 
{
    case BUY_ONE_GET_SECOND_HALF = 'buy_one_get_second_half';

    public function type(): string 
    {
        return match ($this) {
            self::BUY_ONE_GET_SECOND_HALF => 'buy_one_get_second_half',
        };
    }
}