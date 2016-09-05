<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddIndexKeyToCommentPlugins extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('comment_plugins', function (Blueprint $table) {
            $table->index('plugin_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('comment_plugins', function (Blueprint $table) {
            $table->dropIndex('plugin_code');
        });
    }

}
