<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHtmlContentToInstantWinPrizes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('instant_win_prizes', function (Blueprint $table) {
            $table->text("html_content")->after("text");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('instant_win_prizes', function (Blueprint $table) {
            $table->dropColumn("html_content");
        });
    }

}
