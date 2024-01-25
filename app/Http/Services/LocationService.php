<?php

namespace App\Http\Services;

use App\Models\Location;
use App\Repositories\LocationRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class LocationService{

    public function __construct(
        protected LocationRepository $locationRepository,
    ){}
    public function buildLocation(string $title, int $geoId, int $countryId){
        $location = new Location();
        $location->name = $title;
        $location->geo_id = $geoId;
        $location->country_id = $countryId;
        return $location;
    }

    public function getGeoId(string $location, string $country){
       return $this->locationRepository->getGeoId($location, $country);
    }

    public function createLocation(Location $location){
        $this->locationRepository->createLocation($location);
    }

    public function validateGeoId($geoId){
        if(empty($geoId)){
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "geoId not found for location"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }

    public function getLocationId(string $location, $country){
       return $this->locationRepository->getLocationId($location, $country);
    }
}