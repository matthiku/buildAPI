<?php namespace App\Http\Controllers\Api;

/* 

list of methods per Routing table

METHOD      URL                     CONTROLLER
-------------------------------------------------------------------------
GET         /events                 EventController@index
POST        /events                 EventController@store  (+ form data)
GET         /events/{event}         EventController@show
PUT         /events/{event}         EventController@update (+ form data)
PATCH       /events/{event}         EventController@update (+ form data)
PATCH       /events/{event}/nextdate/{date} EventController@updateNextdate
PATCH       /events/{event}/status/{status} EventController@updateStatus
DELETE      /events/{event}         EventController@destroy

*/

use App\Http\Controllers\Controller;

// 'event' table model
use App\Event;

// methods to access the http form data
use Illuminate\Http\Request;


class EventController extends Controller
{



    // use OAuth in all methods but index and show!
    public function __construct()
    {
        $this->middleware( 'oauth', ['except' => ['index', 'show', 'byStatus'] ] );
    }





    /**
     *
     * Show ALL events
     *
     */
    public function index()
    {
        $events = Event::all();
        return $this->createSuccessResponse( $events, 200 );
    }




    /**
     *
     * Show ALL events by Status
     *
     */
    public function byStatus($status)
    {
        $events = Event::where('status', $status)->get();
        return $this->createSuccessResponse( $events, 200 );
    }




    /**
     *
     * Show a specific event
     *
     */
    public function show($id)
    {        
        $event = Event::find($id);
        if ($event) {
            return $this->createSuccessResponse( $event, 200 );
        }
        return $this->createErrorResponse( "Event with id $id not found!", 404 );
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
        $event = Event::create( $request->all() );
        return $this->createSuccessResponse( "A new event with id {$event->id} was created", 201 );
    }





    
    /**
     *
     * UPDATE a specific event
     *
     */
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if ($event) {
            
            // validate form data
            $this->validateRequest($request);

            // modify each field
            $event->seed       = $request->seed;  // only a valid seed will be accepted
            $event->title      = $request->title;
            $event->rooms      = $request->rooms;
            $event->start      = $request->start;
            $event->end        = $request->end;
            $event->weekday    = $request->weekday;
            $event->status     = "UPDATE"; // the change needs to be verified by the backend process
            $event->repeats    = $request->repeats;
            $event->nextdate   = $request->nextdate;
            $event->targetTemp = $request->targetTemp;
            // update event record in the DB table and return a confirmation
            $event->save();

            return $this->createSuccessResponse( "The event with id {$event->id} was updated", 202 );
        }

        return $this->createErrorResponse( "Event with id $id not found!", 404 );
    }


    
    /**
     *
     * UPDATE the 'nextdate' field of a specific event
     *
     * set nextdate of a certain event (once an event is over)
     */
    public function updateNextdate($id, $nextdate)
    {
        $event = Event::find($id);

        if ($event) {
            $event->nextdate   = $nextdate;
            $event->save();

            return $this->createSuccessResponse( "The event with id {$event->id} was updated", 202 );
        }

        return $this->createErrorResponse( "Event with id $id not found!", 404 );
    }



    
    /**
     *
     * UPDATE status to 'OK' for specific event
     *
     * set nextdate of a certain event (once an event is over)
     */
    public function updateStatus($id, $value)
    {
        $event = Event::find($id);

        if ($event) {
            $event->status = $value;
            $event->save();

            return $this->createSuccessResponse( "The status of event with id {$event->id} was updated to {$event->status}", 202 );
        }

        return $this->createErrorResponse( "Event with id $id not found!", 404 );
    }




    
    /**
     *
     * DELETE a specific event
     *
     * We cannot actually delete records but mark them with status=OLD
     * because we (might) have linked records in the eventLogs table
     */
    public function destroy($id)
    {
        $event = Event::find($id);

        if ($event) {

            $event->status = "OLD";
            $event->save();

            return $this->createSuccessResponse( "The event with id $id was marked as 'OLD'.", 200 );
        }

        return $this->createErrorResponse( "Event with id $id not found!", 404 );
    }




    
    /**
     *
     * validate the fields received from the URL/POST methods
     *
     */ 
    function validateRequest($request) {

        $rules = 
        [
            'seed'      => 'required|numeric',
            'title'     => 'required',
            'rooms'     => 'required',
            'start'     => 'required|date_format:"G:i"',
            'end'       => 'required|date_format:"G:i"',
            'targetTemp'=> 'required|numeric',
            'nextdate'  => 'required|date|after:today',
            'repeats'   => 'required|in:once,weekly,monthly,biweekly',
            'status'    => 'required|in:DELETE,NEW,UPDATE,TAN-REQ',
            'weekday'   => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'
        ];        
        /* from the migration:
            $table->string(     'ipaddr',15 ); // ipaddr or name of user
            $table->integer(    'seed'      ); // TAN
            $table->enum(       'status',   ['OK','DELETE','NEW','UPDATE','OLD','TAN-REQ','TANERR']);
            $table->enum(       'weekday',  ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']);
            $table->time(       'start'     ); // start time of event
            $table->time(       'end'       ); // end time of event
            $table->string(     'title', 30 );
            $table->enum(       'repeats',  ['once','weekly','monthly','biweekly']);
            $table->date(       'nextdate'  ); // pre-calculated date for next occurence of this event
            $table->string(     'rooms',5   ); // list of affected rooms (numbers)
            $table->integer(    'targetTemp'); // desired room temperature for this event
        */
        // if validation fails, it will produce an error response }
        $this->validate($request, $rules);

    }



}
