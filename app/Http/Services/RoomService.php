<?php

namespace App\Http\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\RoomRequest;
use App\Http\WebServices\SearchHotelsWebService;
use App\Models\RoomType;
use App\Repositories\RoomRepository;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class RoomService{

    public function __construct(
        protected RoomRepository $roomRepository,
        protected SearchHotelsWebService $searchHotelsWebService,
        protected HotelService $hotelService,
        protected AddressService $addressService,
    ){}
    public function searchRoomsByHotel(RoomRequest $request){

        $hotel = $this->hotelService->getHotel($request->hotel);
        $rooms = $this->roomRepository->getRoomsByHotel($request->hotel);

        $checkIn = date("Y-m-d");
        $checkOut = date("Y-m-d", strtotime($checkIn . " +50 days"));

        if($rooms->isEmpty()){
            $response = $this->searchHotelsWebService->getRooms($hotel->number_hotel, $checkIn, $checkOut);
            $rooms = [];
            $this->validateWebService($response);
            $responseData = $response->object()->data;
            $amenitiesScreenData = $responseData->amenitiesScreen;

                DB::beginTransaction();
            try{
                $addressData = $responseData->location->address;
                $address = $this->addressService->buildAddress($addressData, $hotel->id);
                $this->addressService->createAddress($address);
    
                foreach($amenitiesScreenData as $value){
                    if($value->title === 'Room types'){
                        foreach($value->content as $valueRoom){
                            $room = $this->buildRoom($valueRoom, $hotel->id);
                            $this->roomRepository->createRoom($room);
                            array_push($rooms, $room->name);
                        }
                    }
                }
                DB::commit();
            }catch(Exception $e){
                DB::rollBack();
                throw $e;
            }

        }
        $response = $this->buildResponse($rooms, $hotel->name);
        return ResponseHelper::Response(true, 'select a room type', Response::HTTP_OK, $response);
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
                    'message' => "no rooms were found"], Response::HTTP_NOT_ACCEPTABLE)
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

    public function buildRoom(string $name, int $hotelId){
        $room = new RoomType();
        $room->name = $name;
        $room->rooms_available = rand(1,10);
        $room->hotel_id = $hotelId;
        return $room;
    }

    public function getRoomsAvailable(string $hotel, string $room){
       return $this->roomRepository->getRoomsAvailable($hotel, $room);
    }
}