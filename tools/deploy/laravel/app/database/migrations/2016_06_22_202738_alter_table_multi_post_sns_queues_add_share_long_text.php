<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMultiPostSnsQueuesAddShareLongText extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('multi_post_sns_queues', function(Blueprint $table) {
            $table->text('share_long_text')->default('')->after('share_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('multi_post_sns_queues', function(Blueprint $table) {
            $table->dropColumn('share_long_text');
        });
    }

}
