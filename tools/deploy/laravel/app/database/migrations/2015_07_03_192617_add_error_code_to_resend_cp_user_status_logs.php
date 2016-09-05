<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddErrorCodeToResendCpUserStatusLogs extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('resend_cp_user_status_logs', function(Blueprint $table) {
            $table->string('error_code')->after('cp_id');
            $table->text('error_message')->after('error_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('resend_cp_user_status_logs', function(Blueprint $table) {
            $table->dropColumn('error_code');
            $table->dropColumn('error_message');
        });
    }

}
