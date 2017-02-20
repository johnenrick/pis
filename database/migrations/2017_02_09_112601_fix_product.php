<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('products', function(Blueprint $table)
      {
          $table->boolean('is_sell')->change();
          $table->boolean('is_purchase')->change();
          $table->boolean('is_commission')->change();
          $table->boolean('is_transferred')->change();
          $table->boolean('inactive')->change();
          $table->boolean('locked')->after("inactive");
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
