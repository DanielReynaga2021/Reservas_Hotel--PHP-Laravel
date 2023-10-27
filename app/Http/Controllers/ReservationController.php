<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Http\Services\ReservationService;

class ReservationController extends Controller
{
    private ReservationService $reservationService;

    public function __construct(
        ReservationService $reservationService,
    )
    {
        $this->reservationService = $reservationService;
    }
    public function createReservation(ReservationRequest $hotelRequest){
        return $this->reservationService->createReservation($hotelRequest);
    }
}
