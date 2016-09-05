<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLbcRequests extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('lbc_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('request_body');
            $table->integer("status")->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('lbc_requests');
    }

}