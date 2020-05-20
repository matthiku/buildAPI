<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
* 
*/
class Event extends Model
{
    


    // fields that cannot be changed via the API
    protected $hidden = [ 'ipaddr', 'created_at' ];

    // fields that can be changed via the API
    protected $fillable = [
        'id', 'seed', 'status', 'weekday', 'start', 'end', 'updated_at', 
        'title', 'repeats', 'nextdate', 'rooms', 'targetTemp' 
    ];



    // define relationship
    public function eventLogs() 
    {
        return $this->hasMany('App\EventLog');
    }

    // fields that should be casted as numbers, not strings
    protected $casts = [ 
        'id' => 'int',
        'seed' => 'int',
        'targetTemp' => 'int',
    ];


}