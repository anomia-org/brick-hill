<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCrateUpdatedTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crate', function (Blueprint $table) {
            $table->timestamp('created_at', 3)->nullable()->default(null)->change();
            $table->timestamp('updated_at', 3)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crate', function (Blueprint $table) {
            //
        });
    }
}
