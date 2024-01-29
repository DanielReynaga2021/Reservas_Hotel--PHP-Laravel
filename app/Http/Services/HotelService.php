<?php

namespace App\Http\Services;

use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use App\Helpers\ValidateHelper;
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
            ValidateHelper::validateWebService($responseGeoId, "no GeoId found for the location");

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
        ValidateHelper::validateWebService($responseHotels, "no hotels found for the location");

        $hotelsArray = $responseHotels->object()->data->data;
        $hotelsInsert = [];
        $hotelsResponse = [];
        $locationId = $this->locationService->getLocationId($locationNormalized, $countryNormalized);

            DB::beginTransaction();
        try{
            foreach($hotelsArray as $value){
                    $hotelNormalized = StringHelper::normalizeHotel($value->title); 
                    $hotel = $this->buildHotel($hotelNormalized, $value->bubbleRating->rating, $value->id, $locationId);
                    array_push($hotelsInsert, $hotel);
                    unset($hotel['number_hotel']);
                    unset($hotel['location_id']);
                    unset($hotel['created_at']);
                    unset($hotel['updated_at']);
                    array_push($hotelsResponse, $hotel);
                }
            Hotel::insert($hotels);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
        $hotelsArray = ['hotels' => $hotelsResponse];
        return ResponseHelper::Response(true, 'select a hotel', Response::HTTP_OK, $hotelsArray);
    }
    
    public function buildHotel(string $name, ?int $rating, int $numberHotel, int $locationId){
        return ['name' => $name, 
                'rating' => $rating ?? 0,
                'number_hotel' => $numberHotel,
                'location_id' => $locationId,
                'created_at' => now(),
                'updated_at' => now()];
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