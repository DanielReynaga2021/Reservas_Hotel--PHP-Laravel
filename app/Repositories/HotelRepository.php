<?php

namespace App\Repositories;
use App\Models\Hotel;

class HotelRepository{
    public function getHotelsByLocation(string $location, string $country){
        $hotels = Hotel::select('hotels.name', 'hotels.address', 'hotels.rating')
            ->join('locations', 'hotels.location_id', '=', 'locations.id')
            ->join('countrys', 'locations.country_id', '=', 'countrys.id')
            ->where('locations.name', '=', $location)
            ->where('countrys.name', '=', $country)
            ->get();
        return $hotels;
    }
}