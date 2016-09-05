<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFaviconUrlToBrandPageSettings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_page_settings', function(Blueprint $table)
        {
            $table->string('favicon_url', 511)->default('')->after('og_image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brand_page_settings', function(Blueprint $table)
        {
            $table->dropColumn('favicon_url');
        });
    }
}
