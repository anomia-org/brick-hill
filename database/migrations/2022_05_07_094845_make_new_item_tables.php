<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNewItemTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series', function (Blueprint $table) {
            // item stores a series_id, to find other items in series query against item table
            // this is simply to just keep track of the series
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('series_id')->nullable()->after('type_id')->constrained('series');
            $table->foreignId('event_id')->nullable()->after('series_id')->constrained('events');
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignId('tag_id')->constrained('tags');
            $table->unsignedBigInteger('taggable_id');
            $table->unsignedInteger('taggable_type');

            $table->index(['tag_id', 'taggable_id', 'taggable_type']);

            $table->primary(['tag_id', 'taggable_id', 'taggable_type']);
        });
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('asset_id')->constrained('assets');
            $table->timestamps();
        });
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            // polymorphic as both outfits and items will need to use the table
            $table->unsignedBigInteger('wishlistable_id');
            $table->unsignedInteger('wishlistable_type');
            $table->boolean('active');
            $table->timestamps();

            $table->index(['wishlistable_id', 'wishlistable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('series_id');
        });
        Schema::dropIfExists('series');
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('versions');
        Schema::dropIfExists('wishlists');
    }
}
