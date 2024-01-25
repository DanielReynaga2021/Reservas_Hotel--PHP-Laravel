<?php

namespace App\Http\Services;

use App\Enums\PaymentStatusEnum;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Repositories\ReservationRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReservationService{

    public function __construct(
        protected ReservationRepository $reservationRepository,
        protected HotelService $hotelService,
        protected RoomService $roomService,
        protected AddressService $addressService,
    ){}
    public function createReservation(ReservationRequest $request){
        
        $hotelAndRoom = $this->hotelService->getHotelAndRoom($request->hotel, $request->room);

        $dateFrom = Carbon::createFromFormat('d-m-Y', $request->dateFrom)->format('Y-m-d');
        $dateUntil = Carbon::createFromFormat('d-m-Y', $request->dateUntil)->format('Y-m-d');

        $reservationCount = $this->reservationRepository->getReservation($request->hotel, $request->room, $dateFrom, $dateUntil);
        $roomsAvailable = $this->roomService->getRoomsAvailable($request->hotel, $request->room);
        $this->validateMaximumReservation($reservationCount, $roomsAvailable);
        
        $reservation = $this->buildReservation($dateFrom, $dateUntil, $hotelAndRoom->roomTypesId);
        
        //Metodo 1
        // $address = DB::transaction(function () use($reservation, $hotelAndRoom){
        //     $this->reservationRepository->createReservation($reservation);
        //     $address = $this->addressService->getAddressByHotel($hotelAndRoom->hotelId);
        //     return $address;
        // });

        //Metodo 2
            DB::beginTransaction();
        try{
            $this->reservationRepository->createReservation($reservation);
            $address = $this->addressService->getAddressByHotel($hotelAndRoom->hotelId);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        $response = $this->buildResponse($reservation->code, $request, $address);
        return ResponseHelper::Response(true, 'successfully reservation', Response::HTTP_OK, $response);
    }

    public function validateMaximumReservation(int $reservationCount, int $roomsAvailable){
        if ($reservationCount == $roomsAvailable) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "no rooms available for the chosen date"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
    }

    public function buildResponse(string $reservationCode, ReservationRequest $request, string $address){
        $response = [ 'reservationCode' => $reservationCode,
                      'details'=>[
                      'hotel' => $request->hotel,
                      'room' => $request->room,
                      'address' => $address,
                      'dateFrom'=> $request->dateFrom,
                      'dateUntil' => $request->dateUntil,
                      'paymentStatus' => PaymentStatusEnum::getPaymentStatus(1),]
                    ];
        return $response;
    }

    public function buildReservation(string $dateFrom, string $dateUntil, int $roomId){
        $reservation = new Reservation();
        $reservation->code = strtoupper(Str::random(8));
        $reservation->date_from = $dateFrom;
        $reservation->date_until = $dateUntil;
        $reservation->user_id = auth()->user()->id;
        $reservation->room_type_id = $roomId;
        $reservation->payment_status_id = PaymentStatusEnum::PENDING;
        return $reservation;
    }
}