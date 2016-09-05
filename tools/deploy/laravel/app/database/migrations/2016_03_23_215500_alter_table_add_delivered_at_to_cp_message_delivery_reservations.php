<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTableAddDeliveredAtToCpMessageDeliveryReservations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_message_delivery_reservations', function (Blueprint $table) {
            $table->dateTime('delivered_at')->default('0000-00-00 00:00:00')->after('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_message_delivery_reservations', function (Blueprint $table) {
            $table->dropColumn('delivered_at');
        });
    }

}
