<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTableAddDistributionTypeToCoupons extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('coupons', function (Blueprint $table) {
            $table->tinyInteger('distribution_type')->after('description')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('distribution_type');
        });
    }

}
