<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFixTargetFlgForCpMessageDeliveryTargets extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cp_message_delivery_targets', function(Blueprint $table)
        {
            $table->tinyInteger('fix_target_flg')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cp_message_delivery_targets', function(Blueprint $table)
        {
            $table->dropColumn('fix_target_flg');
        });
    }

}