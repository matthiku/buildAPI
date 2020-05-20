<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_logs', function (Blueprint $table) {
            $table->timestamp('updated_at'     )->primary();
            $table->decimal(  'mainroom',  3,1);
            $table->decimal(  'auxtemp',   3,1);
            $table->decimal(  'frontroom', 3,1);
            $table->boolean(  'heating_on'    );
            $table->integer(  'power'         );
            $table->decimal(  'outdoor',   3,1);
            $table->decimal(  'babyroom',  3,1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('temp_logs');
    }
}
