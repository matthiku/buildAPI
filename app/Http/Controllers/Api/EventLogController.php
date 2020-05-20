<?php namespace App\Http\Controllers\Api;

/* 

list of methods per Routing table

METHOD      URL                     CONTROLLER
-------------------------------------------------------------------------
$app->get(   '/eventlog',                 'EventLogController@index' );
// get latest power data (no auth req'd)
$app->get(   '/eventlog/latest',          'EventLogController@latest' );
// only with authentication
$app->post(  '/eventlog',                 'EventLogController@store'  );

*/

use App\Http\Controllers\Controller;

// 'event' table model
use App\EventLog;

// methods to access the http form data
use Illuminate\Http\Request;


class EventLogController extends Controller
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
        $data = EventLog::orderBy('updated_at', 'DESC')->first();
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

        //$this->validateRequest($request);

        // create a date range based on the 'requested' arguments, 
        // defaulting to 1 hour back from now
        list($from, $to) = $this->findDateRange($request);

        $data = EventLog::whereBetween('updated_at', [$from, $to] )->get();
        if (count($data)) {
            return $this->createSuccessResponse( $data, 200 );
        }
        // in the context of logfiles, finding no data for a certain timespan is no error, so we will always return with code 200
        return $this->createErrorResponse( "no data found for $from $to", 200 );
    }





    /**
     *
     * CREATE a new record
     *
     */
    public function store(Request $request)
    {
        // validate form data
        $this->validateRequest($request);

        // create a new event record in the DB table and return a confirmation
        $data = EventLog::create( $request->all() );
        return $this->createSuccessResponse( ["A new log record was created: ", $data->toJson()], 201 );
    }






    
    /**
     *
     * validate the fields received from the URL/POST methods
     *
     */ 
    function validateRequest($request) {

        // we no longer check for the event id to be in the local DB,
        // as the backend now gets upcoming events from c-CPOT!
        $rules = 
        [
            'event_id'   => 'required|numeric',
            'eventStart' => 'date_format:"G:i"',
            'estimateOn' => 'date_format:"G:i"',
            'actualOn'   => 'date_format:"G:i"',
            'actualOff'  => 'date_format:"G:i"',
        ];        
        /* from the migration:
            $table->integer(  'event_id'  );
            $table->time(     'eventStart');
            $table->time(     'estimateOn');
            $table->time(     'actualOn'  );
            $table->time(     'actualOff' );
        */
        // if validation fails, it will produce an error response }
        $this->validate($request, $rules);

    }



}
