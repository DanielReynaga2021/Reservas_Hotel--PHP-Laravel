<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Hotel
 * 
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property float|null $rating
 * @property int $number_hotel
 * @property int $location_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Location $location
 * @property Collection|RoomType[] $room_types
 *
 * @package App\Models
 */
class Hotel extends Model
{
	protected $table = 'hotels';

	protected $casts = [
		'rating' => 'float',
		'number_hotel' => 'int',
		'location_id' => 'int'
	];

	protected $fillable = [
		'name',
		'address',
		'rating',
		'number_hotel',
		'location_id'
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function address()
	{
		return $this->hasOne(Address::class);
	}

	public function room_types()
	{
		return $this->hasMany(RoomType::class);
	}
}
