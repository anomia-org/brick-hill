<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeTypesConsistent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_types', function (Blueprint $table) {
            $table->renameColumn('type', 'name');
        });

        Schema::table('ban_types', function (Blueprint $table) {
            $table->renameColumn('type', 'name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_types', function (Blueprint $table) {
            $table->renameColumn('name', 'type');
        });

        Schema::table('ban_types', function (Blueprint $table) {
            $table->renameColumn('name', 'type');
        });
    }
}
