<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyItemSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_schedules', function (Blueprint $table) {
            $table->integer('type_id')->nullable()->after('description')->constrained('item_types');
            $table->foreignId('series_id')->nullable()->after('type_id')->constrained('series');
            $table->foreignId('event_id')->nullable()->after('series_id')->constrained('events');
            $table->boolean('hide_update')->default(0)->after('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_schedules', function (Blueprint $table) {
            $table->dropColumn('hide_update');
            $table->dropConstrainedForeignId('type_id');
            $table->dropConstrainedForeignId('series_id');
            $table->dropConstrainedForeignId('event_id');
        });
    }
}
