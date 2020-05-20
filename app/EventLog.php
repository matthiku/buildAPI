<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
* table to record event-driven activities 
*
* (formerly named 'heating_logs')
*/
class EventLog extends Model
{
    

    /**
     * No need for that column in this table
     */
    public function setCreatedAt($value)
    {
        // Do nothing.
    }


    // fields that can be changed via the API
    protected $fillable = [ 'updated_at', 'event_id', 'eventStart', 'estimateOn', 'actualOn', 'actualOff' ];


    // define relationship
    public function event() 
    {
        return $this->belongsTo('App\Event');
    }

    // fields that should be casted as numbers, not strings
    protected $casts = [ 
        'event_id' => 'int',
    ];


}