<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableExternalTwEntries extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('external_tw_entries', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('stream_id')->unsigned();
            $table->bigInteger('object_id');
            $table->tinyInteger('target_type');
            $table->string('link');
            $table->longText('extra_data')->default('');
            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $table->timestamps();

            $table->index('stream_id');
            $table->index('object_id');
            $table->foreign('stream_id')->references('id')->on('external_tw_streams');
            $table->unique('object_id', 'external_tw_entries_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('external_tw_entries');
    }
}

?>