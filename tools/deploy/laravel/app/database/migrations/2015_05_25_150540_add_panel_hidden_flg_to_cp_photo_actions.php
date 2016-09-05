<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPanelHiddenFlgToCpPhotoActions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_photo_actions', function(Blueprint $table) {
            $table->tinyInteger('panel_hidden_flg')->default(1)->after('button_label_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_photo_actions', function(Blueprint $table) {
            $table->dropColumn('panel_hidden_flg');
        });
    }

}
