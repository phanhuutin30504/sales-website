<?php

namespace App\Enums\Product;

use App\Support\Enum;

enum ProductInstock: int
{
    use Enum;

    case Yes = 1;
    case No = 0;

    public function badge()
    {
        return match ($this) {
            ProductInstock::Yes => 'bg-green',
            ProductInstock::No => '',
        };
    }
}
