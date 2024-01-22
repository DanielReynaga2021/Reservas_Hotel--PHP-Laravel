<?php

namespace App\Repositories;

use App\Exceptions\DbException;
use App\Models\Reservation;
use Exception;
use Illuminate\Http\Response;

class ReservationRepository
{
    public function getReservation(string $hotel, string $room, string $dateFrom, string $dateUntil)
    {
        try{
            $reservationCount = Reservation::select('reservations.id')
                    ->join('room_types', 'room_types.id', '=', 'reservations.room_type_id')
                    ->join('hotels', 'hotels.id', '=', 'room_types.hotel_id')
                    ->where('hotels.name', $hotel)
                    ->where('room_types.name', $room)
                    ->where('reservations.date_from', '>=', $dateFrom)
                    ->where('reservations.date_until', '<=', $dateUntil)
                    ->count();
    
            return $reservationCount;
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
