<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddStatusesToTweetMessages extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tweet_messages', function(Blueprint $table) {
            $table->tinyInteger('tweet_status')->after('tweet_content_url')->default(0);
            $table->tinyInteger('approval_status')->after('tweet_status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('tweet_messages', function(Blueprint $table) {
            $table->dropColumn('tweet_status');
            $table->dropColumn('approval_status');
        });
    }

}
