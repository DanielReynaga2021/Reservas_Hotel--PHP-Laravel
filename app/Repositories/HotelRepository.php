<?php

namespace App\Repositories;
use App\Models\Hotel;

class HotelRepository{
    public function getHotelsByLocation(string $location, string $country){
        $hotels = Hotel::select('hotels.name', 'hotels.rating')
            ->join('locations', 'hotels.location_id', '=', 'locations.id')
            ->join('countrys', 'locations.country_id', '=', 'countrys.id')
            ->where('locations.name', '=', $location)
            ->where('countrys.name', '=', $country)
            ->get();
        return $hotels;
    }

    public function getHotelAndRoom(string $hotel, string $room){
            $hotelAndRoom = Hotel::select('hotels.id as hotelId', 'room_types.id as roomTypesId')
            ->join('room_types','hotels.id','=','room_types.hotel_id')
            ->where('hotels.name', $hotel)
            ->where('room_types.name', $room)
            ->first();
        return $hotelAndRoom;
    }
}