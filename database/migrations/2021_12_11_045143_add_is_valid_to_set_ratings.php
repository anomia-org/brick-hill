<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsValidToSetRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('set_ratings', function (Blueprint $table) {
            // how did i forget this the first time
            $table->foreignId('user_id')->after('id')->constrained('users');
            // we only want to count a rating if they have done things like join a set and play it for a few mins
            $table->boolean('is_valid')->after('is_liked');
            $table->boolean('is_active')->after('is_valid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('set_ratings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['is_valid', 'is_active']);
        });
    }
}
