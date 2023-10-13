<?php

namespace App\Http\Services;
use App\Helpers\ResponseHelper;
use App\Http\WebServices\SearchHotelsWebService;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Repositories\RoomRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;


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
    public function searchRoomsByHotel(Request $request){
        $hotel = Hotel::where('name','=', $request->hotel)->first();
        $this->validateHotel($hotel);

        $roomsArray = $this->roomRepository->getRoomsByHotel($request->hotel);

        $checkIn = Carbon::createFromFormat('d-m-Y', $request->checkIn)->format('Y-m-d');
        $checkOut = Carbon::createFromFormat('d-m-Y', $request->checkOut)->format('Y-m-d');

        if($roomsArray->isEmpty()){
            $rooms = $this->searchHotelsWebService->getRooms($hotel->number_hotel, $checkIn, $checkOut);
            $roomsArray = [];
            $this->validateWebService($rooms);
            $roomsArray = $rooms->object()->data->amenitiesScreen;
            foreach($roomsArray as $value){
                if($value->title === 'Room types'){
                    foreach($value->content as $valueRoom){
                        $room = new RoomType();
                        $room->name = $valueRoom;
                        $room->hotel_id = $hotel->id;
                        $room->save();
                        unset($hotel["updated_at"]);
                        unset($hotel["created_at"]);
                        unset($hotel["hotel_id"]);
                        unset($hotel["id"]);
                        array_push($roomsArray, $rooms);
                    }
                }
                break;
            }
        }
        $response = $this->buildResponse($roomsArray, $hotel->name);
        return ResponseHelper::Response(true, 'select a room type', $response);
    }

    public function validateHotel($hotel){
        if (empty($hotel)) {
            throw new BadRequestHttpException(
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
        $rooms = json_decode($rooms, true);
        $rooms = array_column($rooms, 'name');
        $response = ["hotel" => $hotelName, "room_types" => $rooms];
        return $response;
    }
}