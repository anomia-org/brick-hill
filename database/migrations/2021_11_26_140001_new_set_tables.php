<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewSetTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('set_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('set_id')->constrained('sets');
            $table->boolean('is_liked');
            // do we really need timestamps for a ratings table? seems like a lot of data storage for no reason
            // maybe we will need it later so just keep it there ig
            $table->timestamps();
        });

        Schema::table('sets', function (Blueprint $table) {
            $table->foreignId('genre_id')->nullable()->after('creator_id')->constrained('set_genres');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('set_genres');
    }
}
