<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMailQueueCulumn extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_queues', function(Blueprint $table)
        {
            $table->bigInteger('user_id')->after('envelope')->nullable();
            $table->integer('cp_message_delivery_reservation_id')->after('user_id')->nullable();
            $table->unique(array('user_id', 'cp_message_delivery_reservation_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_queues', function(Blueprint $table)
        {
            $table->dropUnique('mail_queues_user_id_cp_message_delivery_reservation_id_unique');
            $table->dropColumn('user_id');
            $table->dropColumn('cp_message_delivery_reservation_id');
        });
    }

}
