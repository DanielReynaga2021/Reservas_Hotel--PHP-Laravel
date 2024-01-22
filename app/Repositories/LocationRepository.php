<?php

namespace App\Repositories;
use App\Exceptions\DbException;
use App\Models\Location;
use Exception;
use Illuminate\Http\Response;

class LocationRepository{
    public function getGeoId(string $location, string $country){
        try{
            $geoId = Location::select('locations.geo_id')
                ->join('countrys', 'locations.country_id', '=', 'countrys.id')
                ->where('locations.name', '=', $location)
                ->where('countrys.name', '=', $country)
                ->first();
            return $geoId->geo_id ?? null;
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getLocationId(string $location, string $country){
        try{
            $locationId = Location::select('locations.id')
                ->join('countrys', 'locations.country_id', '=', 'countrys.id')
                ->where('locations.name', '=', $location)
                ->where('countrys.name', '=', $country)
                ->first();
            return $locationId->id;
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createLocation(Location $location){
        try{
            $location->save();
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}