<?php
use Illuminate\Database\Migrations\Migration;

class InsertPageKpi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $value = array(
            'name' => 'SNS投稿総数',
            'import' => 'jp.aainc.classes.manager_kpi.SnsPostNum'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => 'SNS投稿TOPパネル表示数',
            'import' => 'jp.aainc.classes.manager_kpi.SnsPostPanelDisplayNum'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => '写真投稿総数',
            'import' => 'jp.aainc.classes.manager_kpi.PhotoPostNum'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => '写真投稿TOPパネル表示数',
            'import' => 'jp.aainc.classes.manager_kpi.PhotoPostPanelDisplayNum'
        );
        DB::table('manager_kpi_columns')->insert($value);

        $value = array(
            'name' => '公開中CMS投稿総数',
            'import' => 'jp.aainc.classes.manager_kpi.CmsPostNum'
        );
        DB::table('manager_kpi_columns')->insert($value);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.SnsPostNum')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.SnsPostPanelDisplayNum')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.PhotoPostNum')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.PhotoPostPanelDisplayNum')->delete();
        DB::table('manager_kpi_columns')->where('import', 'jp.aainc.classes.manager_kpi.CmsPostNum')->delete();
	}

}
