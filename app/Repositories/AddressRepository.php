<?php

namespace App\Repositories;

use App\Models\Address;

class AddressRepository{
    public function getAddressByHotel(int $hotelId){
        return Address::Where("hotel_id", $hotelId)->first();
    }

    public function createAddress(Address $address){
        $address->save();
    }
}