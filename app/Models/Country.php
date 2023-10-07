<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Location[] $locations
 *
 * @package App\Models
 */
class Country extends Model
{
	protected $table = 'countrys';

	protected $fillable = [
		'name'
	];

	public function locations()
	{
		return $this->hasMany(Location::class);
	}
}
