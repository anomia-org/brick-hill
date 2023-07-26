<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeCratePolymorphic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crate', function (Blueprint $table) {
            $table->unsignedBigInteger('crateable_id')->nullable()->after('user_id');
            $table->unsignedInteger('crateable_type')->nullable()->after('crateable_id');
            $table->index(['crateable_id', 'crateable_type']);
        });

        \DB::statement("
            UPDATE `crate` SET `crateable_id` = `item_id`, `crateable_type` = 1
        ");

        Schema::table('crate', function (Blueprint $table) {
            $table->unsignedBigInteger('crateable_id')->nullable(false)->change();
            $table->unsignedInteger('crateable_type')->nullable(false)->change();

            $table->foreignId('item_id')->nullable()->change();
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
            $table->dropIndex(['crateable_id', 'crateable_type']);
            $table->dropColumn(['crateable_id', 'crateable_type']);

            $table->foreignId('item_id')->nullable(false)->change();
        });
    }
}
