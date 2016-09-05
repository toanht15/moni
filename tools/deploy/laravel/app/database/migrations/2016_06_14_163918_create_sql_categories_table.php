<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSqlCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sql_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
		});
        DB::table('sql_categories')->insert(
            array(
                'name' => 'ブランド関連',
            )        
        );
        DB::table('sql_categories')->insert(
            array(
                'name' => 'キャンペーン関連',
            )
        );
        DB::table('sql_categories')->insert(
            array(
                'name' => 'ユーザ情報',
            )
        );
        DB::table('sql_categories')->insert(
            array(
                'name' => 'SNS関連',
            )
        );
        DB::table('sql_categories')->insert(
            array(
                'name' => 'その他',
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
		Schema::drop('sql_categories');
	}

}