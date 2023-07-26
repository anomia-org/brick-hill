<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetPassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('set_id')->constrained('sets');
            $table->string('name', 52); // item table uses 52 so why not use it here too
            $table->string('description')->nullable();
            $table->boolean('scrubbed')->default(0);
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
        Schema::dropIfExists('set_passes');
    }
}
