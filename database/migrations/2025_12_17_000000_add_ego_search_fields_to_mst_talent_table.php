<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEgoSearchFieldsToMstTalentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_talent', function (Blueprint $table) {
            $table->string('group_name')->nullable()->comment('グループ名');
            $table->integer('group_id')->nullable()->comment('グループID');
            $table->json('twitter_accounts')->nullable()->comment('Twitterアカウント（配列）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_talent', function (Blueprint $table) {
            $table->dropColumn(['group_name', 'group_id', 'twitter_accounts']);
        });
    }
}
