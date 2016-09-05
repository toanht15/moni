<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaInfoToBrandPageSettings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('brand_page_settings', function (Blueprint $table) {
            $table->string('meta_title', 32)->after('top_page_url');
            $table->string('meta_description', 124)->after('meta_title');
            $table->string('meta_keyword', 511)->after('meta_description');
            $table->string('og_image_url', 511)->after('meta_keyword');
        });

        DB::statement('UPDATE brand_page_settings bps, brands b SET bps.meta_title = b.name, bps.meta_description = CONCAT("『", b.name, "』のブランドページです。SNSアカウントの記事や、どなたでも参加できるキャンペーン情報をお届けします"), bps.og_image_url = b.profile_img_url WHERE bps.brand_id = b.id;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('brand_page_settings', function (Blueprint $table) {
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
            $table->dropColumn('meta_keyword');
            $table->dropColumn('og_image_url');
        });
    }
}
