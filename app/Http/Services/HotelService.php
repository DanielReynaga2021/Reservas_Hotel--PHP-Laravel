<?php

namespace App\Http\Services;

use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use App\Http\Requests\HotelRequest;
use App\Http\WebServices\GeoIdWebService;
use App\Http\WebServices\SearchHotelsWebService;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Location;
use App\Repositories\HotelRepository;
use App\Repositories\LocationRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class HotelService
{
    private LocationRepository $locationRepository;
    private GeoIdWebService $geoIdWebService;
    private SearchHotelsWebService $searchHotelsWebService;
    private HotelRepository $hotelRepository;
    public function __construct(
        LocationRepository $locationRepository,
        GeoIdWebService $geoIdWebService,
        SearchHotelsWebService $searchHotelsWebService,
        HotelRepository $hotelRepository,
    ) {
        $this->locationRepository = $locationRepository;
        $this->geoIdWebService = $geoIdWebService;
        $this->searchHotelsWebService = $searchHotelsWebService;
        $this->hotelRepository = $hotelRepository;
    }

    public function searchHotels(HotelRequest $hotelRequest)
    {
        $countryNormalized = StringHelper::normalizeString($hotelRequest->country);
        $locationNormalized = StringHelper::normalizeString($hotelRequest->location);
        
        $hotels = $this->hotelRepository->getHotelsByLocation($locationNormalized, $countryNormalized);
        if (!$hotels->isEmpty()){
            $hotelsArray = ['hotels' => $hotels];
            return ResponseHelper::Response(true, 'select a hotel', $hotelsArray);
        }

        $geoId = $this->locationRepository->getGeoId($hotelRequest->location, $hotelRequest->country);
        if (empty($geoId)) {
            $responseGeoId = $this->geoIdWebService->getGeoId($hotelRequest->location, $hotelRequest->country);
            $this->validateWebService($responseGeoId);
            $country = Country::Where("name", $countryNormalized)->first();
            if (empty($country)) {
                $country = new Country();
                $country->name = $countryNormalized;
                $country->save();
            }
            $arrayData = $responseGeoId->object()->data;
            foreach ($arrayData as $value) {
                $title = StringHelper::normalizeString($value->title);
                if ($title === $locationNormalized) {
                    $geoId = $value->geoId;
                    $location = new Location();
                    $location->name = $title;
                    $location->geo_id = $value->geoId;
                    $location->country_id = $country->id;
                    $location->save();
                    break;
                }
            }
        }
        $this->validateGeoId($geoId);
        $checkIn = date("Y-m-d");
        $checkOut = date("Y-m-d", strtotime($checkIn . " +50 days"));

        $responseHotels = $this->searchHotelsWebService->getHotels($geoId, $checkIn, $checkOut);
        $this->validateWebService($responseHotels);
        $hotelsArray = $responseHotels->object()->data->data;
        $hotels = [];
        $locationId = $this->locationRepository->getLocationId($locationNormalized, $countryNormalized);
        foreach($hotelsArray as $value){
            $hotelNormalized = StringHelper::normalizeHotel($value->title);
                $hotel = new Hotel();
                $hotel->name = $hotelNormalized;
                $hotel->rating = $value->bubbleRating->rating;
                $hotel->number_hotel = $value->id;
                $hotel->location_id = $locationId;
                $hotel->save();
                unset($hotel["updated_at"]);
                unset($hotel["created_at"]);
                unset($hotel["location_id"]);
                unset($hotel["id"]); 
                unset($hotel["number_hotel"]);
                array_push($hotels, $hotel);
        }
        $hotelsArray = ['hotels' => $hotels];
        return ResponseHelper::Response(true, 'select a hotel', $hotelsArray);
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

    public function validateGeoId($geoId)
    {
        if(empty($geoId)){
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "geoId not found for location"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }
}
