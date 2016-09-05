<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePhotoEntriesAndPhotoStreams extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('photo_streams', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('brand_id');
            $table->integer('display_panel_limit')->default(0);
            $table->tinyInteger('panel_hidden_flg')->default(1);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->index('brand_id');
        });
        Schema::create('photo_entries', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stream_id');
            $table->bigInteger('photo_user_id')->unsigned();
            $table->tinyInteger('priority_flg')->default(0);
            $table->tinyInteger('hidden_flg')->default(0);
            $table->tinyInteger('display_type')->default(1);
            $table->dateTime('pub_date');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('photo_user_id')->references('id')->on('photo_users');
            $table->foreign('stream_id')->references('id')->on('photo_streams');
            $table->unique('photo_user_id');
            $table->index('stream_id');
        });

        DB::statement("INSERT INTO photo_streams(brand_id) SELECT id FROM brands WHERE del_flg = 0;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('photo_entries');
        Schema::drop('photo_streams');
    }

}
