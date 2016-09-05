<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddIndexKeyToMultiPostSnsQueues extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('multi_post_sns_queues', function(Blueprint $table) {
            $table->index(array('callback_parameter', 'callback_function_type'), 'multi_post_sns_queues_callback_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('multi_post_sns_queues', function(Blueprint $table) {
            $table->dropIndex('multi_post_sns_queues_callback_index');
        });
    }

}
