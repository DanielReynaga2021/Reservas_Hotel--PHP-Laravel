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

    public function getroomsAvailable(string $hotel, string $room){
        return RoomType::join('hotels', 'hotels.id', '=', 'room_types.hotel_id')
        ->where('hotels.name', $hotel)
        ->where('room_types.name', $room)
        ->value('room_types.rooms_available');
    }
}