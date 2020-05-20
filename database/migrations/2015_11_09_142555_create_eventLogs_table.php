<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->timestamp('updated_at')->primary();
            $table->integer(  'event_id'  )->references('id')->on('events');;
            $table->time(     'eventStart');
            $table->time(     'estimateOn');
            $table->time(     'actualOn'  );
            $table->time(     'actualOff' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_logs');
    }
}



/*
from the SQL definition:

CREATE TABLE IF NOT EXISTS `heating_logbook` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `eventID` tinyint(4) NOT NULL COMMENT 'event ID (index)',
  `eventStart` time NOT NULL,
  `estimateOn` time NOT NULL COMMENT 'calculated switch-on time',
  `actualOn` time NOT NULL COMMENT 'actual switch on time',
  `actualOff` time NOT NULL COMMENT 'actual switch off time',
  PRIMARY KEY (`timestamp`)
)

*/