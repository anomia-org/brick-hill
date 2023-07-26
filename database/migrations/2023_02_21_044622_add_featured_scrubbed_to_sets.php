<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->boolean('is_featured')->nullable()->after('is_dedicated');
            $table->boolean('is_name_scrubbed')->default(0)->after('name');
            $table->boolean('is_description_scrubbed')->default(0)->after('description');
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
            $table->dropColumn(['is_featured', 'is_name_scrubbed', 'is_description_scrubbed']);
        });
    }
};
