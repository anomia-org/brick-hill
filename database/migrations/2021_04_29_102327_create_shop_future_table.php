<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopFutureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->boolean('is_approved')->default(0);
            $table->boolean('carried_out')->default(0);
            $table->string('name', 52)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('bits')->nullable();
            $table->unsignedInteger('bucks')->nullable();
            $table->boolean('timer');
            $table->timestamp('timer_date')->nullable();
            $table->boolean('special');
            $table->boolean('special_edition');
            $table->unsignedInteger('special_q')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_schedules');
    }
}
