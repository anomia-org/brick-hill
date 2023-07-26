<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHostKeysToSets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->string('host_key')->nullable()->after('active');
            $table->index('host_key');
        });
        Schema::table('game_tokens', function (Blueprint $table) {
            $table->string('validation_token')->nullable()->after('ip');
            $table->dropColumn('host_id');
            $table->foreignId('set_id')->after('user_id')->constrained('sets');
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
            $table->dropColumn('host_key');
        });
        Schema::table('game_tokens', function (Blueprint $table) {
            $table->dropColumn('validation_token');
            $table->integer('host_id')->after('ip');
            $table->dropConstrainedForeignId('set_id');
        });
    }
}
