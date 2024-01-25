<?php

namespace App\Http\Services;
use App\Models\Country;
use App\Repositories\CountryRepository;

class CountryService{

    public function __construct(
        protected CountryRepository $countryRepository,
    ){}
    public function buildCountry(string $name){
        $country = new Country();
        $country->name = $name;
        return $country;
    }

    public function getCountry($country){
        return $this->countryRepository->getCountry($country);
    }

    public function createCountry($country){
        $this->countryRepository->createCountry($country);
    }
}