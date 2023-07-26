<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('avatar_hash');
        });
        Schema::table('servers', function (Blueprint $table) {
            $table->index('last_post');
        });
        Schema::table('sets', function (Blueprint $table) {
            $table->index('is_dedicated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('avatar_hash');
        });
        Schema::table('servers', function (Blueprint $table) {
            $table->dropIndex('last_post');
        });
        Schema::table('sets', function (Blueprint $table) {
            $table->dropIndex('is_dedicated');
        });
    }
}
