<?php namespace App\Http\Controllers\Api;

/* 

list of methods per Routing table

METHOD          URL                         CONTROLLER
-------------------------------------------------------------------------
// get data for a certain time period (default: just the latest)
$app->get(   '/templog',                 'TempLogController@index' );
// get latest power data (no auth req'd)
$app->get(   '/templog/latest',          'TempLogController@latest' );
// ADD a new record (only with authentication)
$app->post(  '/templog',                 'TempLogController@store'  );

*/

use App\Http\Controllers\Controller;

// 'event' table model
use App\TempLog;

// methods to access the http form data
use Illuminate\Http\Request;


class TempLogController extends Controller
{



    // use OAuth in all methods 'latest'
    public function __construct()
    {
        $this->middleware( 'oauth', ['only' => ['store'] ] );
    }





    /**
     *
     * Show LATEST record
     *
     */
    public function latest()
    {
        $data = TempLog::orderBy('updated_at', 'DESC')->first();
        return $this->createSuccessResponse( $data, 200 );
    }





    /**
     *
     * Get records selected by URI parameters
     * (default is last 1 hour)
     *
      *   Possible parameters:
      *   HOWMUCH : (integer) number of ...
      *   UNIT    : (string)  ... hours, days, weeks etc
      *   FROM: specific date or PHP's strtotime() syntax
      *   TO  : specific date or PHP's strtotime() syntax
      *   (specific dates must be in this format: "Y-m-d H:i:s")      
      *  example: howmuch=1, unit=days - give me the exact date/time 24 hours ago
      *       or: from=yesterday
      *       or: from=2 weeks ago
      *       or: http://buildingapi.app/templog?from=2015-11-12%2000:00:01&to=2015-11-13%2023:59:59
     */
    public function index(Request $request)
    {

        $this->validateRequest($request);

        // create a date range based on the 'requested' arguments, 
        // defaulting to 1 hour back from now
        list($from, $to) = $this->findDateRange($request);

        $data = TempLog::whereBetween('updated_at', [$from, $to] )->get();
        if (count($data)) {
            return $this->createSuccessResponse( $data, 200 );
        }
        // in the context of logfiles, finding no data for a certain timespan is no error, so we will always return with code 200
        return $this->createErrorResponse( "no data found for $from $to", 200 );
    }






    /**
     *
     * CREATE a new event
     *
     */
    public function store(Request $request)
    {
        // validate form data
        $this->validateRequest($request);
        // create a new event record in the DB table and return a confirmation
        $data = TempLog::create( $request->all() );
        return $this->createSuccessResponse( ["A new log record was created: ", $data->toJson()], 201 );
    }






    
    /**
     *
     * validate the fields received from the URL/POST methods
     *
     */ 
    function validateRequest($request) {

        $rules = 
        [
            'mainroom'   => 'numeric',
            'auxtemp'    => 'numeric',
            'frontroom'  => 'numeric',
            'heating_on' => 'boolean',
            'power'      => 'numeric',
            'outdoor'    => 'numeric',
            'babyroom'   => 'numeric',
            'howmuch'    => 'numeric',
            'unit'       => 'in:minute,minutes,hour,hours,day,days,week,weeks,month,months',
        ];        
        /* from the migration:
            $table->decimal(  'mainroom',  2,2);
            $table->decimal(  'auxtemp',   2,2);
            $table->decimal(  'frontroom', 2,2);
            $table->decimal(  'heating_on',2,2);
            $table->integer(  'power'         );
            $table->decimal(  'outdoor',   2,2);
            $table->decimal(  'babyroom',  2,2);
        */
        // if validation fails, it will produce an error response }
        $this->validate($request, $rules);

    }



}
