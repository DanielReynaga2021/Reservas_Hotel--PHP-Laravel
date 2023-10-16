<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Http\Services\RoomService;

class RoomController extends Controller
{
    private RoomService $roomService;

    public function __construct(
        RoomService $roomService,
    )
    {
        $this->roomService = $roomService;
    }
    public function searchRoomsByHotel(RoomRequest $request){
        return $this->roomService->searchRoomsByHotel($request);
    }
}
