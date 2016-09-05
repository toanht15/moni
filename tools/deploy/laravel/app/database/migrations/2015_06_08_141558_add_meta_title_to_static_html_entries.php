<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaTitleToStaticHtmlEntries extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('static_html_entries', function (Blueprint $table) {
            $table->string('meta_title', 32)->before('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('static_html_entries', function (Blueprint $table) {
            $table->dropColumn('meta_title');
        });
    }

}
