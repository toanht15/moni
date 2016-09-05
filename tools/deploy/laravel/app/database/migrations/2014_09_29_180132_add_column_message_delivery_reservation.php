<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnMessageDeliveryReservation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_message_delivery_reservations', function (Blueprint $table) {

            $table->tinyInteger('delivery_type')->default(1)->after('status');
            $table->tinyInteger('send_mail_flg')->default(1)->after('deliveryDate');
            $table->renameColumn('deliveryDate', 'delivery_date');
        });

        Schema::table('cp_message_delivery_targets', function (Blueprint $table) {
            $table->renameColumn('deliveredDate', 'delivered_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_message_delivery_reservations', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
            $table->dropColumn('send_mail_flg');
            $table->renameColumn('delivery_date', 'deliveryDate');
        });

        Schema::table('cp_message_delivery_targets', function (Blueprint $table) {
            $table->renameColumn('delivered_date', 'deliveredDate');
        });
    }

}
