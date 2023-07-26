<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeThumbnailsPolymorphicPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thumbnailables', function (Blueprint $table) {
            $table->foreignId('thumbnail_id')->constrained('thumbnails');
            // type of thumbnail, casted as an enum in php, ex 1 = "avatar_thumbnail"
            $table->unsignedInteger('thumbnail_type');
            $table->unsignedBigInteger('thumbnailable_id');
            $table->unsignedInteger('thumbnailable_type');

            $table->index(['thumbnailable_id', 'thumbnailable_type']);

            $table->primary(['thumbnail_type', 'thumbnail_id', 'thumbnailable_id', 'thumbnailable_type'], "combined_primary_key");
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropConstrainedForeignId("thumbnail_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thumbnailables');

        Schema::table('items', function (Blueprint $table) {
            $table->foreignId("thumbnail_id")->nullable()->after("is_public")->constrained("thumbnails");
        });
    }
}
