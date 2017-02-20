<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('products', function (Blueprint $table) {
          $table->increments('id');
          $table->integer("category_id");
          $table->integer("classification");
          $table->integer("unit_id");
          $table->char("code", 30);
          $table->char("description", 100);
          $table->boolean("is_sell")->comment("Ill sell this item");
          $table->boolean("is_purchase")->comment("Ill purchase this item");
          $table->boolean("is_commission");
          $table->boolean("is_transferred")->comment("Transferred to Poultry Main");
          $table->boolean("inactive");
          $table->boolean("locked");
          $table->integer("packing");
          $table->tinyInteger("cost_method")->comment("1 - average, 2 LIFO");
          $table->text("dosage");
          $table->double("stock");
          $table->double("reorder_level");
          $table->double("quantity_on_order");
          $table->double("pending_customer_order");
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
