<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertBrandTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement("
INSERT INTO brand_transactions(brand_id, del_flg, created_at, updated_at)
SELECT id, 0, now(), now()
FROM brands
WHERE del_flg = 0
        ");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement("DELETE FROM brand_transactions");
	}

}
