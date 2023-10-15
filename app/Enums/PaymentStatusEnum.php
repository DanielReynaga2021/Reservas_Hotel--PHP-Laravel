<?php

namespace App\Enums;

class PaymentStatusEnum{
    const PENDING = 1;
    const COMPLETED = 2;
    const PAYMENT_DUE = 3;
    const CANCELLED = 4;
    const FAILED = 5;
}