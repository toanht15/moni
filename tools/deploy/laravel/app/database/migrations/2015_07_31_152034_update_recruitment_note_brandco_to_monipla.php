<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRecruitmentNoteBrandcoToMonipla extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        DB::statement("UPDATE cps SET `recruitment_note` = replace(`recruitment_note`, 'BRANDCo', 'モニプラ');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE cps SET `recruitment_note` = replace(`recruitment_note`, 'モニプラ', 'BRANDCo');");
    }

}
