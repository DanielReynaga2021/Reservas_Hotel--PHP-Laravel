<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Reservation
 * 
 * @property int $id
 * @property string $code
 * @property Carbon $date_from
 * @property Carbon $date_until
 * @property int $user_id
 * @property int $room_type_id
 * @property int $payment_status_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PaymentStatus $payment_status
 * @property RoomType $room_type
 * @property User $user
 *
 * @package App\Models
 */
class Reservation extends Model
{
	protected $table = 'reservations';

	protected $casts = [
		'date_from' => 'datetime',
		'date_until' => 'datetime',
		'user_id' => 'int',
		'room_type_id' => 'int',
		'payment_status_id' => 'int'
	];

	protected $fillable = [
		'code',
		'date_from',
		'date_until',
		'user_id',
		'room_type_id',
		'payment_status_id'
	];

	public function payment_status()
	{
		return $this->belongsTo(PaymentStatus::class);
	}

	public function room_type()
	{
		return $this->belongsTo(RoomType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
