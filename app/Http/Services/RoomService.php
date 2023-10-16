<?php

namespace App\Http\Services;
use App\Helpers\ResponseHelper;
use App\Http\Requests\RoomRequest;
use App\Http\WebServices\SearchHotelsWebService;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Repositories\RoomRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class RoomService{

    private RoomRepository $roomRepository;
    private SearchHotelsWebService $searchHotelsWebService;

    public function __construct(
        RoomRepository $roomRepository,
        SearchHotelsWebService $searchHotelsWebService,
    )
    {
        $this->roomRepository = $roomRepository;
        $this->searchHotelsWebService = $searchHotelsWebService;
    }
    public function searchRoomsByHotel(RoomRequest $request){
        $hotel = Hotel::where('name','=', $request->hotel)->first();
        $this->validateHotel($hotel);
        
        $rooms = $this->roomRepository->getRoomsByHotel($request->hotel);

        $checkIn = date("Y-m-d");
        $checkOut = date("Y-m-d", strtotime($checkIn . " +50 days"));

        if($rooms->isEmpty()){
            $response = $this->searchHotelsWebService->getRooms($hotel->number_hotel, $checkIn, $checkOut);
            $rooms = [];
            $this->validateWebService($response);
            $responseData = $response->object()->data->amenitiesScreen;
            foreach($responseData as $value){
                if($value->title === 'Room types'){
                    foreach($value->content as $valueRoom){
                        $room = new RoomType();
                        $room->name = $valueRoom;
                        $room->hotel_id = $hotel->id;
                        $room->save();
                        array_push($rooms, $room->name);
                    }
                }
            }
        }
        $response = $this->buildResponse($rooms, $hotel->name);
        return ResponseHelper::Response(true, 'select a room type', $response);
    }

    public function validateHotel($hotel){
        if (empty($hotel)) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "no se encontro el hotel"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }

    public function validateWebService($response)
    {
        if ($response->failed()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Internal Server Error'], Response::HTTP_SERVICE_UNAVAILABLE)
            );
        }
        
        if (empty($response->object()->data)) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "no se encontro habitaciones"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }

    public function buildResponse($rooms, $hotelName){
        if(!is_array($rooms)){
            $rooms = json_decode($rooms, true);
            $rooms = array_column($rooms, 'name');
        }
        $response = ["hotel" => $hotelName, "room_types" => $rooms];
        return $response;
    }
}