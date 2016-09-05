<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCodeAuthenticationCodesCodeType extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement('ALTER TABLE `code_authentication_codes` MODIFY `code` VARCHAR(255) BINARY');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement('ALTER TABLE `code_authentication_codes` MODIFY `code` VARCHAR(255)');
    }

}
