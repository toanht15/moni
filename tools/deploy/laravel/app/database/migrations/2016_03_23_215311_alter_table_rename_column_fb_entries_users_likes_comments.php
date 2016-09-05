<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTableRenameColumnFbEntriesUsersLikesComments extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('fb_entries_users_likes', function (Blueprint $table) {
            $table->renameColumn('post_id', 'object_id');
        });

        Schema::table('fb_entries_users_comments', function (Blueprint $table) {
            $table->renameColumn('post_id', 'object_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('fb_entries_users_likes', function (Blueprint $table) {
            $table->renameColumn('object_id', 'post_id');
        });

        Schema::table('fb_entries_users_comments', function (Blueprint $table) {
            $table->renameColumn('object_id', 'post_id');
        });
    }

}
