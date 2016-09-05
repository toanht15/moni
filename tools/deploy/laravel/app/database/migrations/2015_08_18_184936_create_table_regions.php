<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRegions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('regions', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
        });

        $values = array(
            array(
                'id' => '1',
                'name' => '北海道・東北',
            ),
            array(
                'id' => '2',
                'name' => '関東'
            ),
            array(
                'id' => '3',
                'name' => '中部'
            ),
            array(
                'id' => '4',
                'name' => '近畿'
            ),
            array(
                'id' => '5',
                'name' => '中国'
            ),
            array(
                'id' => '6',
                'name' => '四国'
            ),
            array(
                'id' => '7',
                'name' => '九州・沖縄'
            )
        );

        DB::table('regions')->insert($values);

        Schema::table('prefectures', function(Blueprint $table) {
            $table->integer('region_id')->after('id');
        });

        DB::statement("UPDATE prefectures SET `region_id` = 1 WHERE `id` IN (1, 2, 3, 4, 5, 6, 7);");
        DB::statement("UPDATE prefectures SET `region_id` = 2 WHERE `id` IN (8, 9, 10, 11, 12, 13, 14);");
        DB::statement("UPDATE prefectures SET `region_id` = 3 WHERE `id` IN (15, 16, 17, 18, 19, 20, 21, 22, 23);");
        DB::statement("UPDATE prefectures SET `region_id` = 4 WHERE `id` IN (24, 25, 26, 27, 28, 29, 30);");
        DB::statement("UPDATE prefectures SET `region_id` = 5 WHERE `id` IN (31, 32, 33, 34, 35);");
        DB::statement("UPDATE prefectures SET `region_id` = 6 WHERE `id` IN (36, 37, 38, 39);");
        DB::statement("UPDATE prefectures SET `region_id` = 7 WHERE `id` IN (40, 41, 42, 43, 44, 45, 46, 47);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('regions');

        Schema::table('prefectures', function(Blueprint $table) {
            $table->dropColumn('region_id');
        });
    }

}
