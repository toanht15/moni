<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCodeAuthUserTrackingLogs extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('code_auth_user_tracking_logs', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('cp_action_id');
            $table->integer('auth_error_count')->default(0);
            $table->dateTime('acc_locking_expire_date');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('user_id', 'cp_action_id'), 'tracking_log_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('code_auth_user_tracking_logs');
    }

}
