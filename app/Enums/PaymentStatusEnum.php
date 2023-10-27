<?php

namespace App\Enums;

class PaymentStatusEnum{
    const PENDING = 1;
    const COMPLETED = 2;
    const PAYMENT_DUE = 3;
    const CANCELLED = 4;
    const FAILED = 5;

    const array = [
        self::PENDING => "PENDING",
        self::COMPLETED => "COMPLETED",
        self::PAYMENT_DUE => "PAYMENT_DUE",
        self::CANCELLED => "CANCELLED",
        self::FAILED => "FAILED"
    ];
    public static function getPaymentStatus(int $status): string {
        return self::array[$status];
    }
}