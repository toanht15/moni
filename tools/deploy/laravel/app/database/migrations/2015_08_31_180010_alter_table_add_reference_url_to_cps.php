<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddReferenceUrlToCps extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cps', function(Blueprint $table) {
            $table->string('reference_url', 255)->after('au_flg');
        });

        DB::statement('UPDATE cps c, brands b SET c.reference_url = CONCAT("/", b.directory_name, "/campaigns/", c.id) WHERE c.brand_id = b.id AND c.type = 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cps', function(Blueprint $table) {
            $table->dropColumn('reference_url');
        });
    }

}
