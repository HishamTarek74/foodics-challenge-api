<?php

namespace App\Services;

use App\Interfaces\OrderValidatorInterface;
use App\Models\Order;

class OrderValidator implements OrderValidatorInterface
{

    public function validate($dataOrder): bool
    {
        // TODO: Implement validate() method.
        return true;
    }
}
