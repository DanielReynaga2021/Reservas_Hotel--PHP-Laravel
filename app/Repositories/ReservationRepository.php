<?php

namespace App\Repositories;

use App\Models\Reservation;

class ReservationRepository
{
    public function getReservation(string $hotel, string $room, string $dateFrom, string $dateUntil)
    {
        $reservationCount = Reservation::select('reservations.id')
                ->join('room_types', 'room_types.id', '=', 'reservations.room_type_id')
                ->join('hotels', 'hotels.id', '=', 'room_types.hotel_id')
                ->where('hotels.name', $hotel)
                ->where('room_types.name', $room)
                ->where('reservations.date_from', '>=', $dateFrom)
                ->where('reservations.date_until', '<=', $dateUntil)
                ->count();

        return $reservationCount;
    }
}
