<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Location
 * 
 * @property int $id
 * @property string $name
 * @property int $geo_id
 * @property int $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Country $country
 * @property Collection|Hotel[] $hotels
 *
 * @package App\Models
 */
class Location extends Model
{
	protected $table = 'locations';

	protected $casts = [
		'geo_id' => 'int',
		'country_id' => 'int'
	];

	protected $fillable = [
		'name',
		'geo_id',
		'country_id'
	];

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function hotels()
	{
		return $this->hasMany(Hotel::class);
	}
}
