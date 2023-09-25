<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentStatus
 * 
 * @property int $id
 * @property string $name
 * 
 * @property Collection|Payment[] $payments
 *
 * @package App\Models
 */
class PaymentStatus extends Model
{
	protected $table = 'payment_status';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}
}
