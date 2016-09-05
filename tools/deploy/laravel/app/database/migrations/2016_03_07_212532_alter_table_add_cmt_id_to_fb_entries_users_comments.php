<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddCmtIdToFbEntriesUsersComments extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('fb_entries_users_comments', function (Blueprint $table) {
            $table->bigInteger('cmt_object_id')->after('post_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('fb_entries_users_comments', function (Blueprint $table) {
            $table->dropColumn('cmt_object_id');
        });
    }

}
