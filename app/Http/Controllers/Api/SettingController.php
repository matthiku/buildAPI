<?php namespace App\Http\Controllers\Api;

/* 

list of methods as per Routing table

METHOD      URL                     CONTROLLER
-------------------------------------------------------------------------
// only with authentication
$app->get(   '/settings',                   'SettingController@index'  );
$app->get(   '/settings/{setting}',         'SettingController@show'   );
$app->post(  '/settings',                   'SettingController@store'  );
$app->put(   '/settings/{id}',              'SettingController@update' );
$app->patch( '/settings/{id}',              'SettingController@update' );
$app->patch( '/settings/status/{status}',   'SettingController@updateStatus' );
$app->delete('/settings/{id}',              'SettingController@destroy');

*/

use App\Http\Controllers\Controller;

// 'setting' table model
use App\Setting;

// methods to access the http form data
use Illuminate\Http\Request;


class SettingController extends Controller
{



    // use OAuth in ALL methods!
    public function __construct()
    {
        $this->middleware( 'auth:api' );
    }





    /**
     *
     * Get ALL settings
     *
     */
    public function index()
    {
        $data = Setting::all();
        return $this->createSuccessResponse( $data, 200 );
    }





    /**
     *
     * Get a specific setting by 'key'
     *
     */
    public function show($key)
    {        
        $setting = Setting::where('key', $key)->get();
        if ($setting) {
            return $this->createSuccessResponse( $setting, 200 );
        }
        return $this->createErrorResponse( "Setting with key $key not found!", 404 );
    }





    /**
     *
     * CREATE a new setting
     *
     */
    public function store(Request $request)
    {
        // validate form data
        $this->validateRequest($request);

        // create a new setting record in the DB table and return a confirmation
        $setting = Setting::create( $request->all() );
        return $this->createSuccessResponse( "A new setting with id {$setting->id} was created", 201 );
    }





    
    /**
     *
     * UPDATE a specific setting
     *
     */
    public function update(Request $request, $id)
    {
        $setting = Setting::find( $id );

        if ($setting) {
            
            // validate fields
            $this->validateRequest($request);

            // not allowed to change the key string
            if ($setting->key  != $request->key) {
                return $this->createErrorResponse( "Not allowed to change the key string!", 403 ); 
            }

            // check if the new value is any different from the old
            // TODO  TODO  TODO  TODO 
            if (strval($setting->value) === strval($request->value)) {
                return $this->createErrorResponse( "Setting value same as old!", 406 );
            }

            // modify the fields
            if ( $request->has('note') ) {
                $setting->note  = $request->note; 
            }            
            else {
                $setting->note  = 'old value: '.$setting->value;      # save the old value in the note
            }
            $setting->value = $request->value; 
            // update setting record in the DB table and return a confirmation
            $setting->save();


            // updating seed and status in order to trigger a download of the new values to the local control program
            $data = Setting::where('key', 'status')->update(['value' => 'UPDATE'      ]);
            if ($data !== 1) {
                return $this->createErrorResponse( "Error when trying to change status field!", 500 );
            }
            $data = Setting::where('key', 'seed'  )->update(['value' => $request->seed]);
            if ($data !== 1) {
                return $this->createErrorResponse( "Error when trying to change seed field!", 500 );
            }


            return $this->createSuccessResponse( "The setting with id {$setting->id} ({$setting->key}) was updated to {$setting->value}", 202 );
        }

        return $this->createErrorResponse( "Setting with id $id not found!", 404 );
    }




    
    /**
     *
     * UPDATE status to 'OK'
     *
     */
    public function updateStatus($value)
    {

        $data = Setting::where('key', 'status')
                        ->update(['value' => $value]);

        return $this->createSuccessResponse( "The status of the settings was updated to {$value}", 202 );
    }




    
    /**
     *
     * DELETE a specific setting
     *
     * We cannot actually delete records but mark them with note=OLD
     * TODO: we need a seed and to set UPDATE as the status key value!
     */
    public function destroy($id)
    {
        $setting = Setting::find($id);

        if ($setting) {
            $setting->delete();
            return $this->createSuccessResponse( "The setting with id $id was deleted.", 200 );
        }

        return $this->createErrorResponse( "Setting with id $id not found!", 404 );
    }




    
    /**
     *
     * validate the fields received from the URL/POST methods
     *
     */ 
    function validateRequest($request) {

        $rules = 
        [
            'key'       => 'required|max:25|not_in:seed,status',  # not allowed to change seed or status directly
            'value'     => 'required|max:155',
            'seed'      => 'required',
            'note'      => 'max:255',
        ];        
        /* from the migration:
            $table->increments('id');
            $table->char('key',   25);
            $table->char('value',155);
            $table->char('note', 255);
            $table->timestamps();
        */
        // if validation fails, it will produce an error response }
        $this->validate($request, $rules);

    }



}
