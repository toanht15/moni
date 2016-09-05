<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFangate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // 外部キー削除
        Schema::table('fangate_social_accounts', function(Blueprint $table)
        {
            $table->dropForeign('fangate_social_accounts_cp_fangate_action_id_foreign');
        });

        // テーブルリネーム
        Schema::rename('fangate_social_accounts', 'engagement_social_accounts');

        // カラムリネーム
        Schema::table('engagement_social_accounts', function(Blueprint $table)
        {
            $table->renameColumn('cp_fangate_action_id', 'cp_engagement_action_id');
        });

        // 参照先テーブルリネーム
        Schema::rename('cp_fangate_actions', 'cp_engagement_actions');

        // 外部キー追加
        Schema::table('engagement_social_accounts', function(Blueprint $table)
        {
            $table->foreign('cp_engagement_action_id')->references('id')->on('cp_engagement_actions');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        // 外部キー削除
        Schema::table('engagement_social_accounts', function($table)
        {
            $table->dropForeign('engagement_social_accounts_cp_engagement_action_id_foreign');
        });

        // テーブル名変更
        Schema::rename('cp_engagement_actions', 'cp_fangate_actions');
        Schema::rename('engagement_social_accounts', 'fangate_social_accounts');

        // カラム変更
        Schema::table('fangate_social_accounts', function(Blueprint $table)
        {
            $table->renameColumn('cp_engagement_action_id', 'cp_fangate_action_id');
        });

        // 外部キー設定
        Schema::table('fangate_social_accounts', function(Blueprint $table)
        {
            $table->foreign('cp_fangate_action_id')->references('id')->on('cp_fangate_actions');
        });
	}

}
