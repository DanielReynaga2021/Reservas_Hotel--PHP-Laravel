<?php

namespace App\Repositories;
use App\Exceptions\DbException;
use App\Models\Country;
use Exception;
use Illuminate\Http\Response;

class CountryRepository{

    public function getCountry(string $country){
        try{
            return Country::Where("name", $country)->first();
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createCountry(Country $country){
        try {
            $country->save();
        } catch (Exception $e) {
            throw new DbException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}