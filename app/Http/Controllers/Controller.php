<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    //generic method to return data
    public function createSuccessResponse($data, $code)
    {
        return response()->json(['data' => $data], $code);
    }



    //generic method to return error message
    public function createErrorResponse($message, $code)
    {
        return response()->json(['message' => $message, 'code' => $code], $code);
    }



    /**
      * Create 2 mySQL timestamps (from and to) to generate a time range 
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
      *
     * @param  \Illuminate\Http\Request  $request
     * @return array (timestamp from, timestamp to)
      */
    public function findDateRange(Request $request)
    {

        // default values 
        $howmuch = 1;
        $unit = 'hour';
        if ($request->has('howmuch')) {
            $howmuch = $request->get('howmuch');
        }
        if ($request->has('unit')) {
            $unit = $request->get('unit');
        }
        
        // user-defined start time?
        if ($request->has('from')) {
            $from = date("Y-m-d H:i:s", strtotime( $request->get('from') ) );
        } else {
            $from = date("Y-m-d H:i:s", strtotime("-$howmuch $unit") );
        }

        // user-defined end-time (instead of now)?
        if ($request->has('to')) {
            $to = date("Y-m-d H:i:s", strtotime( $request->get('to') ) ); # user-defined
        } else {
            $to = date("Y-m-d H:i:s"); # now
        }

        return array( $from, $to) ;
    }




    /**
     * Create the response for when a request fails validation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $errors
     * @return \Illuminate\Http\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        // we use our own method to return the validation error in JSON format
        return $this->createErrorResponse($errors, 422);
        
        /* the following was in the original code and needs to be ignored since 
           we always want to return a JSON and never redirect!
        return redirect()->to($this->getRedirectUrl())
                        ->withInput($request->input())
                        ->withErrors($errors, $this->errorBag());
        */
    }

}
