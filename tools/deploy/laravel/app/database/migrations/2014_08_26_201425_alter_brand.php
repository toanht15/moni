<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBrand extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brands', function(Blueprint $table)
		{
            $table->dropColumn('monipla_enterprise_token');
		});

        Schema::table('brands', function(Blueprint $table)
        {
            $table->string('monipla_enterprise_token')->default('')->after('enterprise_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brands', function(Blueprint $table)
		{
            $table->dropColumn('monipla_enterprise_token');
		});

        Schema::table('brands', function(Blueprint $table)
        {
            $table->string('monipla_enterprise_token')->after('enterprise_id');
        });
	}

}
