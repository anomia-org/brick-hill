<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricalSpecialDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_special_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->unsignedInteger('active_copies');
            $table->unsignedInteger('unique_owners');
            $table->unsignedInteger('views_today');
            $table->unsignedInteger('avg_daily_views');
            $table->unsignedInteger('avg_price')->nullable();
            $table->unsignedInteger('volume_hoarded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_special_data');
    }
}
