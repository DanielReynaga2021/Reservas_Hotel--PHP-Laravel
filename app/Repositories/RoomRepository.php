<?php

namespace App\Repositories;
use App\Exceptions\DbException;
use App\Models\RoomType;
use Exception;
use Illuminate\Http\Response;

class RoomRepository{
    public function getRoomsByHotel($hotel){
        try{
            $rooms = RoomType::select('room_types.name')
            ->join('hotels','hotels.id','=', 'room_types.hotel_id')
            ->where('hotels.name','=',$hotel)
            ->get();
    
            return $rooms;
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getroomsAvailable(string $hotel, string $room){
        try{
            return RoomType::join('hotels', 'hotels.id', '=', 'room_types.hotel_id')
            ->where('hotels.name', $hotel)
            ->where('room_types.name', $room)
            ->value('room_types.rooms_available');
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}