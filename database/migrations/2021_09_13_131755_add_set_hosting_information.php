<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSetHostingInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->integer('max_players')->nullable()->after('active');
            $table->boolean('friends_only')->nullable()->after('max_players');
            $table->boolean('is_dedicated')->nullable()->after('friends_only');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->dropColumn(['max_players', 'friends_only', 'is_dedicated']);
        });
    }
}
