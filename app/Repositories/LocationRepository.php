<?php

namespace App\Repositories;
use App\Models\Location;

class LocationRepository{
    public function getGeoId(string $location, string $country){
        $geoId = Location::select('locations.geo_id')
            ->join('countrys', 'locations.country_id', '=', 'countrys.id')
            ->where('locations.name', '=', $location)
            ->where('countrys.name', '=', $country)
            ->first();
        return $geoId->geo_id ?? null;
    }

    public function getLocationId(string $location, string $country){
        $locationId = Location::select('locations.id')
            ->join('countrys', 'locations.country_id', '=', 'countrys.id')
            ->where('locations.name', '=', $location)
            ->where('countrys.name', '=', $country)
            ->first();
        return $locationId->id;
    }
}