<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoniplaUpdateFlgToCpMessageDeliveryReservations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_message_delivery_reservations', function(Blueprint $table) {
            $table->tinyInteger('monipla_update_status')->default(0)->after('send_mail_flg');
        });

        DB::statement('UPDATE cp_message_delivery_reservations SET monipla_update_status = 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_message_delivery_reservations', function(Blueprint $table) {
            $table->dropColumn('monipla_update_status');
        });
    }

}
