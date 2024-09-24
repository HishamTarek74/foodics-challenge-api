<?php

namespace App\Interfaces;

use App\Models\Order;

interface OrderValidatorInterface
{
    public function validate($dataOrder): bool;
}
