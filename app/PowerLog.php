<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
*
* TempLog records temperature measurements over time
*
*/
class PowerLog extends Model
{

	/**
	 * No need for that column in this table
	 */
	public function setCreatedAt($value)
	{
	    // Do nothing.
	}

    // fields that can be changed via the API
    protected $fillable = [ 'updated_at', 'heating_on', 'power', 'boiler_on' ];

    // fields that should be casted as numbers, not strings
    protected $casts = [ 
        'power' => 'int',
        'heating_on' => 'bool',
        'boiler_on' => 'bool',
    ];



}