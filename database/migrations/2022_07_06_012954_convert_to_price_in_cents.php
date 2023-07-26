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
        Schema::table('billing_products', function (Blueprint $table) {
            $table->integer('price_in_cents')->after('price')->nullable();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('gross_in_cents')->after('gross')->nullable();
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->integer('funds_in_cents')->after('funds')->nullable();
        });
        \DB::statement("UPDATE `billing_products` SET `price_in_cents` = `price` * 100");
        \DB::statement("UPDATE `payments` SET `gross_in_cents` = `gross` * 100");
        \DB::statement("UPDATE `activity_logs` SET `funds_in_cents` = `funds` * 100");
        Schema::table('billing_products', function (Blueprint $table) {
            $table->integer('price_in_cents')->change();
            $table->dropColumn('price');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('gross_in_cents')->change();
            $table->dropColumn('gross');
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->integer('funds_in_cents')->change();
            $table->dropColumn('funds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_products', function (Blueprint $table) {
            //
        });
    }
};
