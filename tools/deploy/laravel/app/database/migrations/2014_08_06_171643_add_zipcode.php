<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZipcode extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_page_settings', function(Blueprint $table)
        {
            $table->tinyInteger('privacy_required_zipcode')->default(0)->after('privacy_required_birthday');
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
            $table->dropColumn('privacy_required_zipcode');
        });
    }

}
