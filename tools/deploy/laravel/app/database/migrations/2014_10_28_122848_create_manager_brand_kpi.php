<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerBrandKpi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('manager_brand_kpi_columns', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name',255)->default('');
            $table->text('import');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
        });
        Schema::create('manager_brand_kpi_values', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('column_id')->unsigned();
            $table->integer('brand_id')->unsigned();
            $table->date('summed_date');
            $table->string('value', 255);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->unique(array('column_id', 'brand_id', 'summed_date'));
            $table->foreign('column_id')->references('id')->on('manager_brand_kpi_columns');
        });
        DB::table('manager_brand_kpi_columns')->insert(
            array(
                'name' => 'BC経由ファン',
                'import' => 'jp.aainc.classes.manager_kpi.JoinBrandcoCpRelationNumKPI',
            )
        );
        DB::table('manager_brand_kpi_columns')->insert(
            array(
                'name' => 'MPFB経由ファン',
                'import' => 'jp.aainc.classes.manager_kpi.JoinMoniplaCpRelationNumKPI',
            )
        );
        DB::table('manager_brand_kpi_columns')->insert(
            array(
                'name' => 'CP参加数',
                'import' => 'jp.aainc.classes.manager_kpi.JoinCpUserNumKPI',
            )
        );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('manager_brand_kpi_values');
        Schema::drop('manager_brand_kpi_columns');
	}

}
