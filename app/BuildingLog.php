<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
*
* TempLog records temperature measurements over time
*
*/
class BuildingLog extends Model
{

	/**
	 * No need for that column in this table
	 */
	public function setCreatedAt($value)
	{
	    // Do nothing.
	}

    // fields that can be changed via the API
    protected $fillable = [ 'updated_at', 'what', 'where', 'text' ];


}