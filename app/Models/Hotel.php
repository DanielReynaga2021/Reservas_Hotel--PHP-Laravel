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
 * @property string $address
 * @property int $number_hotel
 * @property float $rating
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|RoomType[] $room_types
 *
 * @package App\Models
 */
class Hotel extends Model
{
	protected $table = 'hotels';

	protected $casts = [
		'number_hotel' => 'int',
		'rating' => 'float'
	];

	protected $fillable = [
		'name',
		'address',
		'number_hotel',
		'rating'
	];

	public function room_types()
	{
		return $this->hasMany(RoomType::class);
	}
}
