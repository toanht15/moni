<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerKpiGroupsColumnsInManagerPages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('manager_kpi_groups', function(Blueprint $t)
        {
            $t->increments('id');
            $t->string('name',255)->default('');
            $t->tinyInteger('del_flg')->default(0);
            $t->timestamps();
        });

        Schema::create('manager_kpi_group_columns', function(Blueprint $t)
        {
            $t->increments('id');
            $t->integer('manager_kpi_group_id')->unsigned();
            $t->integer('manager_kpi_column_id')->unsigned();
            $t->tinyInteger('del_flg')->default(0);
            $t->timestamps();
            $t->foreign('manager_kpi_group_id')->references('id')->on('manager_kpi_groups');
            $t->foreign('manager_kpi_column_id')->references('id')->on('manager_kpi_columns');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('manager_kpi_group_columns');
        Schema::drop('manager_kpi_groups');
	}

}
