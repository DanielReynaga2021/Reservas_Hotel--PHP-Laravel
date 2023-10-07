<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelRequest;
use App\Http\Services\HotelService;
use App\Repositories\LocationRepository;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    private HotelService $hotelService;

    public function __construct(
        HotelService $hotelService,
    )
    {
        $this->hotelService = $hotelService;
    }
    public function searchHotels(HotelRequest $hotelRequest){
        return $this->hotelService->searchHotels($hotelRequest);
    }
}