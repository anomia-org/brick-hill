<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstallerTagToClientBuild extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // all old versions are broken so just delete them
        \DB::statement("TRUNCATE client_builds");
        Schema::table('client_builds', function (Blueprint $table) {
            $table->boolean('is_installer')->default(0)->after('is_release');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_builds', function (Blueprint $table) {
            $table->dropColumn('is_installer');
        });
    }
}
