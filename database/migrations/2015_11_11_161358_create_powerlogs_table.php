<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePowerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('power_logs', function (Blueprint $table) {
            $table->timestamp('updated_at')->primary();
            $table->integer(  'power'     );
            $table->boolean(  'heating_on');
            $table->boolean(  'boiler_on' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('power_logs');
    }
}
