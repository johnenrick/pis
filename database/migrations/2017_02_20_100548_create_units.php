<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('units', function (Blueprint $table) {
          $table->increments('id');
          $table->char("code", 10);
          $table->char("description", 50);
          $table->float("multiplier");
          $table->timestamps();
          $table->SoftDeletes();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
