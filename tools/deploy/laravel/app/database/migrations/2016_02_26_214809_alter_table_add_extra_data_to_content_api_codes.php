<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddExtraDataToContentApiCodes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('content_api_codes', function (Blueprint $table) {
            $table->unsignedInteger('cp_action_id')->after('cp_id');
            $table->text('extra_data')->after('code');
        });

        DB::statement('UPDATE content_api_codes cac,
	      (SELECT cac.id cac_id, ca.id ca_id FROM cps c
		    LEFT JOIN cp_action_groups cag ON cag.cp_id = c.id AND cag.del_flg = 0
		    LEFT JOIN cp_actions ca ON ca.cp_action_group_id = cag.id AND ca.del_flg = 0
		    LEFT JOIN content_api_codes cac ON cac.del_flg = 0
	      WHERE c.del_flg = 0 AND cac.cp_id = c.id AND ca.type = cac.cp_action_type
	      GROUP BY c.id) ca_tmp
          SET cac.cp_action_id = ca_tmp.ca_id
          WHERE cac.id = ca_tmp.cac_id
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('content_api_codes', function (Blueprint $table) {
            $table->dropColumn('cp_action_id');
            $table->dropColumn('extra_data');
        });
    }

}
