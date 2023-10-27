<?php

namespace App\Http\Services;
use App\Enums\PaymentStatusEnum;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Repositories\HotelRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\RoomRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ReservationService{

    private ReservationRepository $reservationRepository;
    private HotelRepository $hotelRepository;
    private RoomRepository $roomRepository;

    public function __construct(
        ReservationRepository $reservationRepository,
        HotelRepository $hotelRepository,
        RoomRepository $roomRepository,
    )
    {
        $this->reservationRepository = $reservationRepository;
        $this->hotelRepository = $hotelRepository;
        $this->roomRepository = $roomRepository;
    }
    public function createReservation(ReservationRequest $request){
        
        $hotelAndRoom = $this->hotelRepository->getHotelAndRoom($request->hotel, $request->room);
        $this->validateHotelAndRoom($hotelAndRoom);

        $dateFrom = Carbon::createFromFormat('d-m-Y', $request->dateFrom)->format('Y-m-d');
        $dateUntil = Carbon::createFromFormat('d-m-Y', $request->dateUntil)->format('Y-m-d');

        $reservationCount = $this->reservationRepository->getReservation($request->hotel, $request->room, $dateFrom, $dateUntil);
        $roomsAvailable = $this->roomRepository->getroomsAvailable($request->hotel, $request->room);
        $this->validateMaximumReservation($reservationCount, $roomsAvailable);
        
        $reservation = new Reservation();
        $reservation->code = strtoupper(Str::random(8));
        $reservation->date_from = $dateFrom;
        $reservation->date_until = $dateUntil;
        $reservation->user_id = auth()->user()->id;
        $reservation->room_type_id = $hotelAndRoom->roomTypesId;
        $reservation->payment_status_id = PaymentStatusEnum::PENDING;
        $reservation->save();

        $response = $this->buildResponse($reservation->code, $request);
        return ResponseHelper::Response(true, 'successfully reservation', $response);
    }

    public function validateHotelAndRoom($hotelAndRoom){
        if (empty($hotelAndRoom)) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "the hotel/room was not found"], Response::HTTP_NOT_ACCEPTABLE)
            );
        }
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

    public function buildResponse(string $reservationCode, ReservationRequest $request){
        $response = [ 'reservationCode' => $reservationCode,
                      'details'=>[
                      'hotel' => $request->hotel,
                      'room' => $request->room,
                      'dateFrom'=> $request->dateFrom,
                      'dateUntil' => $request->dateUntil,
                      'paymentStatus' => PaymentStatusEnum::getPaymentStatus(1),]
                    ];
        return $response;
    }
}