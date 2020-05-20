<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments( 'id'        );
            $table->timestamps(             );
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('events');
    }
}
