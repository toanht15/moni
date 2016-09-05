<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterManagerKpi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('CREATE INDEX manager_kpi_columns_import_index ON manager_kpi_columns(import(100));');
        DB::statement('CREATE INDEX manager_brand_kpi_columns_import_index ON manager_brand_kpi_columns(import(100));');

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manager_brand_kpi_columns', function(Blueprint $table)
		{
            $table->dropIndex('manager_brand_kpi_columns_import_index');
		});
        Schema::table('manager_kpi_columns', function(Blueprint $table)
        {
            $table->dropIndex('manager_kpi_columns_import_index');
        });
	}

}
