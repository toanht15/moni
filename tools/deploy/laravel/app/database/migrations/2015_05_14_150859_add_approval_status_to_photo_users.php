<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApprovalStatusToPhotoUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('photo_users', function(Blueprint $table) {
            $table->tinyInteger('approval_status')->default(0)->after('photo_comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('photo_users', function(Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }

}
