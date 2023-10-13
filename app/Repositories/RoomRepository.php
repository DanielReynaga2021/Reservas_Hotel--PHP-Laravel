<?php

namespace App\Repositories;
use App\Models\RoomType;

class RoomRepository{
    public function getRoomsByHotel($hotel){
        $rooms = RoomType::select('room_types.name')
        ->join('hotels','hotels.id','=', 'room_types.hotel_id')
        ->where('hotels.name','=',$hotel)
        ->get();

        return $rooms;
    }
}