<?php

namespace App\Repositories;
use App\Exceptions\DbException;
use App\Models\Hotel;
use Exception;
use Illuminate\Http\Response;

class HotelRepository{
    public function getHotelsByLocation(string $location, string $country){
        try{
            $hotels = Hotel::select('hotels.name', 'hotels.rating')
                ->join('locations', 'hotels.location_id', '=', 'locations.id')
                ->join('countrys', 'locations.country_id', '=', 'countrys.id')
                ->where('locations.name', '=', $location)
                ->where('countrys.name', '=', $country)
                ->get();
            return $hotels;
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getHotelAndRoom(string $hotel, string $room){
        try{
            $hotelAndRoom = Hotel::select('hotels.id as hotelId', 'room_types.id as roomTypesId')
            ->join('room_types','hotels.id','=','room_types.hotel_id')
            ->where('hotels.name', $hotel)
            ->where('room_types.name', $room)
            ->first();
            return $hotelAndRoom;
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createHotel(Hotel $hotel){
        try{
            $hotel->save();
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}