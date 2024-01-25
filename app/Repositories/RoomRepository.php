<?php

namespace App\Repositories;

use App\Models\RoomType;

class RoomRepository{
    public function getRoomsByHotel(string $hotel){
        return RoomType::select('room_types.name')
        ->join('hotels','hotels.id','=', 'room_types.hotel_id')
        ->where('hotels.name','=',$hotel)
        ->get();
    }

    public function getRoomsAvailable(string $hotel, string $room){
        return RoomType::join('hotels', 'hotels.id', '=', 'room_types.hotel_id')
        ->where('hotels.name', $hotel)
        ->where('room_types.name', $room)
        ->value('room_types.rooms_available');
    }

    public function createRoom(RoomType $room){
        $room->save();
    }
}