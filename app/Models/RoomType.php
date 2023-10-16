<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomType
 * 
 * @property int $id
 * @property string $name
 * @property int $rooms_available
 * @property int $hotel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Hotel $hotel
 * @property Collection|Reservation[] $reservations
 *
 * @package App\Models
 */
class RoomType extends Model
{
	protected $table = 'room_types';

	protected $casts = [
		'rooms_available' => 'int',
		'hotel_id' => 'int'
	];

	protected $fillable = [
		'name',
		'rooms_available',
		'hotel_id'
	];

	public function hotel()
	{
		return $this->belongsTo(Hotel::class);
	}

	public function reservations()
	{
		return $this->hasMany(Reservation::class);
	}
}
