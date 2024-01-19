<?php

namespace App\Repositories;
use App\Models\Address;

class AddressRepositoty{
    public function getAddressByHotel(int $hotelId){
            $address = Address::select('address.name')
            ->join('hotels','hotels.id','=','address.hotel_id')
            ->where('address.hotel_id', $hotelId)
            ->first();
        return $address->name;
    }
}