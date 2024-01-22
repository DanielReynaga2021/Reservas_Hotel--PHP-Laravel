<?php

namespace App\Repositories;
use App\Exceptions\DbException;
use App\Models\Address;
use Exception;
use Illuminate\Http\Response;

class AddressRepository{
    public function getAddressByHotel(int $hotelId){
        try{
            $address = Address::select('address.name')
            ->join('hotels','hotels.id','=','address.hotel_id')
            ->where('address.hotel_id', $hotelId)
            ->first();
        return $address->name;
        } catch (Exception $e) {
        throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}