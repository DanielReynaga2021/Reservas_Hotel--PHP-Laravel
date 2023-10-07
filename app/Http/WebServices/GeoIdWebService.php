<?php

namespace App\Http\WebServices;
use Illuminate\Support\Facades\Http;

class GeoIdWebService{
    public function getGeoId(string $location, string $country){
        $query = $location . ', ' . $country;
        $parameter = ['query' => $query];
        $headers = ['X-RapidAPI-Host' => env('X_RapidAPI_Host'),
                    'X-RapidAPI-Key' => env('X_RapidAPI_Key')];
        $url = env('URL_SEARCH_GEO_ID');
        return Http::withHeaders($headers)->withQueryParameters($parameter)->get($url);
    }
}