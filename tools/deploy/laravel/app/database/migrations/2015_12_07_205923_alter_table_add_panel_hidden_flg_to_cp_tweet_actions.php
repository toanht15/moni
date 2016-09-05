<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddPanelHiddenFlgToCpTweetActions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_tweet_actions', function(Blueprint $table) {
            $table->tinyInteger('panel_hidden_flg')->after('skip_flg')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_tweet_actions', function(Blueprint $table) {
            $table->dropColumn('panel_hidden_flg');
        });
    }

}
