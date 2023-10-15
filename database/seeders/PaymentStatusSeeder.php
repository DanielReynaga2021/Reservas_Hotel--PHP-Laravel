<?php

namespace Database\Seeders;

use App\Models\PaymentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentStatus = new PaymentStatus();
        $paymentStatus->id = 1;
        $paymentStatus->name = 'pending';
        $paymentStatus->save();

        $paymentStatus = new PaymentStatus();
        $paymentStatus->id = 2;
        $paymentStatus->name = 'completed';
        $paymentStatus->save();
        
        $paymentStatus = new PaymentStatus();
        $paymentStatus->id = 3;
        $paymentStatus->name = 'payment due';
        $paymentStatus->save();

        $paymentStatus = new PaymentStatus();
        $paymentStatus->id = 4;
        $paymentStatus->name = 'cancelled';
        $paymentStatus->save();

        $paymentStatus = new PaymentStatus();
        $paymentStatus->id = 5;
        $paymentStatus->name = 'failed';
        $paymentStatus->save();
    }
}
