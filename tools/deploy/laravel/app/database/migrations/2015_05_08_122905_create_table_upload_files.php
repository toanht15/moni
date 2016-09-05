<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUploadFiles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('upload_files', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('name', 50);
            $table->string('description', 300);
            $table->string('type', 50);
            $table->integer('size');
            $table->string('url');
            $table->longText('extra_data');
            $table->tinyInteger('hidden_flg')->default(1);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('upload_files');
    }

}
