<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Products extends Migration
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
          $table->integer("classification_id");
          $table->integer("unit_id");
          $table->char("description", 100);
          $table->binary("is_sell")->comment("Ill sell this item");
          $table->binary("is_purchase")->comment("Ill purchase this item");
          $table->binary("is_commission");
          $table->binary("is_transferred")->comment("Transferred to Poultry Main");
          $table->binary("inactive");
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
