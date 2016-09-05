<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCodeAuthenticationCodes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement('ALTER TABLE `code_authentication_codes` DROP INDEX `code_authentication_code_unique_key`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement('ALTER TABLE `code_authentication_codes` ADD CONSTRAINT `code_authentication_code_unique_key` UNIQUE (`code_auth_id`, `code`)');
    }

}
