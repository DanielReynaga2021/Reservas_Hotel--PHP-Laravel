<?php

namespace App\Http\Services;
use App\Models\Address;
use App\Repositories\AddressRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddressService{

    private AddressRepository $addressRepository;

    public function __construct(
        AddressRepository $addressRepository,
    )
    {
        $this->addressRepository = $addressRepository;
    }
    public function buildAddress(string $name, int $hotelId){
        $address = new Address();
        $address->name = $name;
        $address->hotel_id = $hotelId;
        return $address;
    }

    public function createAddress(Address $address){
        $this->addressRepository->createAddress($address);
    }

    public function getAddressByHotel(int $hotelId){
        $address = $this->addressRepository->getAddressByHotel($hotelId);
        $this->validateAddress($address);
        return $address->name;
    }

    private function validateAddress($address){
        if(is_null($address)){
            throw new ModelNotFoundException("the address was not found");
        }
    }
}
