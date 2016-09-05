<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrefillFlgToEntryAction extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cp_entry_actions', function(Blueprint $table)
        {
            $table->boolean('prefill_flg')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cp_entry_actions', function(Blueprint $table)
        {
            $table->dropColumn('prefill_flg');
        });
    }

}