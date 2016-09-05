<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTableAddMailNameToBrands extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('mail_name', 40)->after('name')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('mail_name');
        });
    }

}