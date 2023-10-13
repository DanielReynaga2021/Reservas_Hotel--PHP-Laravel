<?php

namespace App\Http\Controllers;

use App\Http\Services\RoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    private RoomService $roomService;

    public function __construct(
        RoomService $roomService,
    )
    {
        $this->roomService = $roomService;
    }
    public function searchRoomsByHotel(Request $request){
        return $this->roomService->searchRoomsByHotel($request);
    }
}
