<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 * 
 * @property int $id
 * @property int $reservation_id
 * @property int $payment_status_id
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PaymentStatus $payment_status
 * @property Reservation $reservation
 *
 * @package App\Models
 */
class Payment extends Model
{
	protected $table = 'payment';

	protected $casts = [
		'reservation_id' => 'int',
		'payment_status_id' => 'int'
	];

	protected $fillable = [
		'reservation_id',
		'payment_status_id'
	];

	public function payment_status()
	{
		return $this->belongsTo(PaymentStatus::class);
	}

	public function reservation()
	{
		return $this->belongsTo(Reservation::class);
	}
}
