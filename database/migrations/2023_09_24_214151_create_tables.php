<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('address', 255);
            $table->integer('number_hotel');
            $table->float('rating', 1,1);
            $table->timestamps();
        });

        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->unsignedBigInteger('hotel_id');
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->date('date_from');
            $table->date('date_until');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('room_type_id');
            $table->foreign('room_type_id')->references('id')->on('room_types');
            $table->timestamps();
        });

        Schema::create('payment_status', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->unique();
            $table->foreign('reservation_id')->references('id')->on('reservations');
            $table->unsignedBigInteger('payment_status_id');
            $table->foreign('payment_status_id')->references('id')->on('payment_status');
            $table->date('created_at')->useCurrent();
            $table->date('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_status');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('hotels');
    }
};
