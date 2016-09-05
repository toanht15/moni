<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableContentApiCodes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('content_api_codes', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cp_id');
            $table->string('code', 255);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_id')->references('id')->on('cps');
            $table->unique('code');
            $table->index('cp_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('content_api_codes');
    }

}
