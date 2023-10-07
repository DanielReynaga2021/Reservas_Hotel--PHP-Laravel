<?php

namespace App\Http\WebServices;
use Illuminate\Support\Facades\Http;

class SearchHotelsWebService{
    public function getHotels(int $geoId, string $checkIn, string $checkOut){
        //consultar a la otra api externa con el geoID
        $parameter = ['geoId' => $geoId,
                      'checkIn' => $checkIn,
                      'checkOut' => $checkOut,
                      'currencyCode' => 'USD'];
        $headers = ['X-RapidAPI-Host' => env('X_RapidAPI_Host'),
            'X-RapidAPI-Key' => env('X_RapidAPI_Key')];
        $url = env('URL_SEARCH_HOTELS');
        return Http::withHeaders($headers)->withQueryParameters($parameter)->get($url);
    }
}
