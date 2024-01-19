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
            $table->float('rating', 3,1)->nullable();
            $table->integer('number_hotel');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->timestamps();
        });
        
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedBigInteger('hotel_id')->unique();
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->timestamps();
        });

        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->integer('rooms_available');
            $table->unsignedBigInteger('hotel_id');
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->timestamps();
        });
        
        Schema::create('payment_status', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->date('date_from');
            $table->date('date_until');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('room_type_id');
            $table->foreign('room_type_id')->references('id')->on('room_types');
            $table->unsignedBigInteger('payment_status_id');
            $table->foreign('payment_status_id')->references('id')->on('payment_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('payment_status');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('address');
        Schema::dropIfExists('hotels');
    }
};
