<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePageEntriesAndStreams extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('page_streams', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('brand_id');
            $table->integer('display_panel_limit')->default(0);
            $table->tinyInteger('panel_hidden_flg')->default(1);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->index('brand_id');
        });

        Schema::create('page_entries', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stream_id');
            $table->unsignedInteger('static_html_entry_id');
            $table->string('image_url', 512);
            $table->string('panel_text', 300);
            $table->tinyInteger('priority_flg')->default(0);
            $table->tinyInteger('top_hidden_flg')->default(1);
            $table->tinyInteger('hidden_flg')->default(1);
            $table->tinyInteger('display_type')->default(1);
            $table->dateTime('pub_date');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('stream_id')->references('id')->on('page_streams');
            $table->foreign('static_html_entry_id')->references('id')->on('static_html_entries');
            $table->unique('static_html_entry_id');
            $table->index('stream_id');
        });

        DB::statement('INSERT INTO page_streams(brand_id) SELECT id FROM brands WHERE del_flg = 0;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('page_entries');
        Schema::drop('page_streams');
    }

}
