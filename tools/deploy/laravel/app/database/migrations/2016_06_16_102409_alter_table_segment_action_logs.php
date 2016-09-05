<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSegmentActionLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('segment_action_logs', function(Blueprint $table)
		{
            $table->dropForeign('segment_action_logs_segment_id_foreign');
			$table->dropColumn('segment_id');
            $table->integer('brand_id')->unsigned()->after('id');
            $table->integer('total')->after('type');
            $table->string('segment_provison_ids')->after('brand_id');

            $table->foreign('brand_id')->references('id')->on('brands');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('segment_action_logs', function(Blueprint $table)
		{
            $table->dropColumn('brand_id');
            $table->dropColumn('total');
            $table->dropColumn('segment_provison_ids');

            $table->unsignedBigInteger('segment_id');
            $table->foreign('segment_id')->references('id')->on('segments');
		});
	}

}
