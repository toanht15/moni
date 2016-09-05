<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBrandPageSettings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('brand_page_settings', function (Blueprint $table) {
            $table->string('top_page_url', 511)->default('')->after('restricted_age');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('brand_page_settings', function (Blueprint $table) {
            $table->dropColumn('top_page_url');
        });
    }

}
