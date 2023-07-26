<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MergeDisabledAccountsAndBanned extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("INSERT INTO ban_types (`id`, `name`, `default_note`, `default_length`) VALUES (31, 'Account disabled', 'Account disabled. Contact us at help@brick-hill.com to restore it.', 37317600)");
        Schema::table('bans', function (Blueprint $table) {
            $table->softDeletes();
            $table->text('note')->nullable()->change();
        });
        \DB::statement("
            INSERT INTO bans (`id`, `user_id`, `admin_id`, `note`, `ban_type_id`, `length`, `active`, `created_at`, `updated_at`) 
            SELECT NULL, `user_id`, ".config('site.main_account_id').", NULL, 31, 37317600, 1, now(), now() FROM disabled_accounts
        ");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bans', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->text('note')->change();
        });
    }
}
