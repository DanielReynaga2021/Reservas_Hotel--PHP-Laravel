<?php

namespace App\Repositories;

use App\Models\Country;

class CountryRepository{

    public function getCountry(string $country){
        return Country::Where("name", $country)->first();
    }

    public function createCountry(Country $country){
        $country->save();
    }
}