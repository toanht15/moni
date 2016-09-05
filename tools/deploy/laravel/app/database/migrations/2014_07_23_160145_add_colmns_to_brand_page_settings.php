<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColmnsToBrandPageSettings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_page_settings', function(Blueprint $table)
		{
            $table->longText('agreement')->after('tag_text');

            $table->tinyInteger('privacy_required_name')->default(0)->after('agreement');
            $table->tinyInteger('privacy_required_sex')->default(0)->after('privacy_required_name');
            $table->tinyInteger('privacy_required_birthday')->default(0)->after('privacy_required_sex');
            $table->tinyInteger('privacy_required_address')->default(0)->after('privacy_required_birthday');
            $table->tinyInteger('privacy_required_tel')->default(0)->after('privacy_required_address');
            $table->tinyInteger('privacy_required_restricted')->default(0)->after('privacy_required_tel');

            $table->tinyInteger('restricted_age')->default(0)->unsigned()->after('privacy_required_restricted');
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
            $table->dropColumn('agreement');
            $table->dropColumn('privacy_required_name');
            $table->dropColumn('privacy_required_sex');
            $table->dropColumn('privacy_required_birthday');
            $table->dropColumn('privacy_required_address');
            $table->dropColumn('privacy_required_tel');
            $table->dropColumn('privacy_required_restricted');
            $table->dropColumn('restricted_age');
		});
	}

}
