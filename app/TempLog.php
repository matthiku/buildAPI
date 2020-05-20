<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
*
* TempLog records temperature measurements over time
*
*/
class TempLog extends Model
{
    

    /**
     * No need for that column in this table
     */
    public function setCreatedAt($value)
    {
        // Do nothing.
    }


    // fields that can be changed via the API
    protected $fillable = [ 'updated_at', 'mainroom', 'auxtemp', 'frontroom', 'heating_on', 'power', 'outdoor', 'babyroom' ];


    // fields that should be casted as numbers, not strings
    protected $casts = [ 
        'mainroom' => 'float',
        'auxtemp' => 'float',
        'frontroom' => 'float',
        'heating_on' => 'float',
        'power' => 'int',
        'outdoor' => 'float',
        'babyroom' => 'float',
    ];


}