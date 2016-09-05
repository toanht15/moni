<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddShareUrlToCommentPlugins extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('comment_plugins', function (Blueprint $table) {
            $table->text('footer_text')->after('free_text');
            $table->string('share_url')->after('footer_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('comment_plugins', function (Blueprint $table) {
            $table->dropColumn('footer_text');
            $table->string('share_url');
        });
    }

}
