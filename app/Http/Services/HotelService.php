<?php

namespace App\Http\Services;

use App\Helpers\ResponseHelper;
use App\Helpers\StringHelper;
use App\Http\Requests\HotelRequest;
use App\Http\WebServices\GeoIdWebService;
use App\Http\WebServices\SearchHotelsWebService;
use App\Models\Address;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\Rating;
use App\Repositories\HotelRepository;
use App\Repositories\LocationRepository;
use Carbon\Carbon;
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
            return ResponseHelper::Response(true, 'hoteles', $hotels);
        }

        $geoId = $this->locationRepository->getGeoId($hotelRequest->location, $hotelRequest->country);
        if (empty($geoId)) {
            $responseGeoId = $this->geoIdWebService->getGeoId($hotelRequest->location, $hotelRequest->country);
            //echo "<pre>";print_r($responseGeoId->object()->data);echo "<br>"; exit;
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
        $checkIn = Carbon::createFromFormat('d-m-Y', $hotelRequest->checkIn)->format('Y-m-d');
        $checkOut = Carbon::createFromFormat('d-m-Y', $hotelRequest->checkOut)->format('Y-m-d');

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
                $hotel->address = $value->secondaryInfo;
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
        return ResponseHelper::Response(true, 'hoteles', $hotels);
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
                    'message' => "no se encontro hoteles para la localizacion"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }

    public function validateGeoId($geoId)
    {
        if(empty($geoId)){
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "no se encontro el geoId para la localizacion"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }
}
