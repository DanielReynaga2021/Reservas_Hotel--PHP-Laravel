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
 * @property Carbon $date_from
 * @property Carbon $date_until
 * @property int $user_id
 * @property int $room_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property RoomType $room_type
 * @property User $user
 * @property Payment $payment
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
		'room_type_id' => 'int'
	];

	protected $fillable = [
		'date_from',
		'date_until',
		'user_id',
		'room_type_id'
	];

	public function room_type()
	{
		return $this->belongsTo(RoomType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function payment()
	{
		return $this->hasOne(Payment::class);
	}
}
