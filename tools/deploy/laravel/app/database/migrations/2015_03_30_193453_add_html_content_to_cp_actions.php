<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHtmlContentToCpActions extends Migration {

    public static $cp_action_tables = array(
        'cp_announce_actions',
        'cp_buttons_actions',
        'cp_coupon_actions',
        'cp_entry_actions',
        'cp_instant_win_actions',
        'cp_join_finish_actions',
        'cp_message_actions',
        'cp_movie_actions',
        'cp_photo_actions',
        'cp_questionnaire_actions',
        'cp_shipping_address_actions'
    );

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        foreach (self::$cp_action_tables as $cp_action_table) {
            Schema::table($cp_action_table, function (Blueprint $table) {
                $table->text("html_content")->after("text");
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        foreach (self::$cp_action_tables as $cp_action_table) {
            Schema::table($cp_action_table, function (Blueprint $table) {
                $table->dropColumn("html_content");
            });
        }
    }

}
