<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpPhotoActionsShareRequired extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_photo_actions', function (Blueprint $table) {
            $table->boolean('fb_share_required')->default(0)->after('comment_required');
            $table->boolean('tw_share_required')->default(0)->after('fb_share_required');
            $table->string('share_placeholder', 511)->default('モニプラで投稿しました')->after('tw_share_required');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_photo_actions', function (Blueprint $table) {
            $table->dropColumn('fb_share_required');
            $table->dropColumn('tw_share_required');
            $table->dropColumn('share_placeholder');
        });
    }
}
