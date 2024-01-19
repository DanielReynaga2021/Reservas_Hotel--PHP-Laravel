<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Address
 * 
 * @property int $id
 * @property string $name
 * @property int $hotel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Hotel $hotel
 *
 * @package App\Models
 */
class Address extends Model
{
	protected $table = 'address';

	protected $casts = [
		'hotel_id' => 'int'
	];

	protected $fillable = [
		'name',
		'hotel_id'
	];

	public function hotel()
	{
		return $this->belongsTo(Hotel::class);
	}
}
