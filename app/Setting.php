<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
* 
*/
class Setting extends Model
{
    
    // fields that cannot be changed via the API
    protected $hidden = [   'created_at' ];

    // fields that can be changed via the API
    protected $fillable = [ 'id', 'key', 'value', 'note' ];

}