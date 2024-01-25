<?php

namespace App\Http\Services;

use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use App\Http\Requests\HotelRequest;
use App\Http\WebServices\GeoIdWebService;
use App\Http\WebServices\SearchHotelsWebService;
use App\Models\Hotel;
use App\Repositories\HotelRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class HotelService{
    public function __construct(
        protected GeoIdWebService $geoIdWebService,
        protected SearchHotelsWebService $searchHotelsWebService,
        protected HotelRepository $hotelRepository,
        protected CountryService $countryService,
        protected LocationService $locationService,
    ){}

    public function searchHotels(HotelRequest $hotelRequest)
    {
        $countryNormalized = StringHelper::normalizeString($hotelRequest->country);
        $locationNormalized = StringHelper::normalizeString($hotelRequest->location);
        
        $hotels = $this->hotelRepository->getHotelsByLocation($locationNormalized, $countryNormalized);
        if (!$hotels->isEmpty()){
            $hotelsArray = ['hotels' => $hotels];
            return ResponseHelper::Response(true, 'select a hotel', Response::HTTP_OK, $hotelsArray);
        }

        $geoId = $this->locationService->getGeoId($hotelRequest->location, $hotelRequest->country);
        if (empty($geoId)) {
            $responseGeoId = $this->geoIdWebService->getGeoId($hotelRequest->location, $hotelRequest->country);
            $this->validateWebService($responseGeoId);

            $country = $this->countryService->getCountry($countryNormalized);
            if (empty($country)) {
                $country = $this->countryService->buildCountry($countryNormalized);
                $this->countryService->createCountry($country);
            }
            
            $arrayData = $responseGeoId->object()->data;
            foreach ($arrayData as $value) {
                $title = StringHelper::normalizeString($value->title);
                if ($title === $locationNormalized) {
                    $geoId = $value->geoId;
                    $location = $this->locationService->buildLocation($title, $value->geoId, $country->id);
                    $this->locationService->createLocation($location);
                    break;
                }
            }
        }
        $this->locationService->validateGeoId($geoId);
        $checkIn = date("Y-m-d");
        $checkOut = date("Y-m-d", strtotime($checkIn . " +50 days"));

        $responseHotels = $this->searchHotelsWebService->getHotels($geoId, $checkIn, $checkOut);
        $this->validateWebService($responseHotels);

        $hotelsArray = $responseHotels->object()->data->data;
        $hotels = [];
        $locationId = $this->locationService->getLocationId($locationNormalized, $countryNormalized);

            DB::beginTransaction();
        try{
            foreach($hotelsArray as $value){
                    $hotelNormalized = StringHelper::normalizeHotel($value->title); 
                    $hotel = $this->buildHotel($hotelNormalized, $value->bubbleRating->rating, $value->id, $locationId);
                    $this->hotelRepository->createHotel($hotel);
                    unset($hotel["updated_at"]);
                    unset($hotel["created_at"]);
                    unset($hotel["location_id"]);
                    unset($hotel["id"]); 
                    unset($hotel["number_hotel"]);
                    array_push($hotels, $hotel);
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
        $hotelsArray = ['hotels' => $hotels];
        return ResponseHelper::Response(true, 'select a hotel', Response::HTTP_OK, $hotelsArray);
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
                    'message' => "no hotels found for the location"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }
    public function buildHotel(string $name, ?int $rating, int $numberHotel, int $locationId){
        $hotel = new Hotel();
        $hotel->name = $name;
        $hotel->rating = $rating ?? 0;
        $hotel->number_hotel = $numberHotel;
        $hotel->location_id = $locationId;
        return $hotel;
    }

    public function getHotelAndRoom(string $hotel, string $room){
        $hotelAndRoom = $this->hotelRepository->getHotelAndRoom($hotel, $room);
        $this->validateHotelAndRoom($hotelAndRoom);
        return $hotelAndRoom;
    }

    public function validateHotelAndRoom($hotelAndRoom){
        if(is_null($hotelAndRoom)){
            throw new ModelNotFoundException("the hotel/room was not found");
        }
    }

    public function getHotel(string $hotel){
     $hotel = $this->hotelRepository->getHotel($hotel);
     $this->validateHotel($hotel);
     return $hotel;
    }

    public function validateHotel($hotel){
        if(is_null($hotel)){
            throw new ModelNotFoundException("the hotel was not found");
        }
    }
}