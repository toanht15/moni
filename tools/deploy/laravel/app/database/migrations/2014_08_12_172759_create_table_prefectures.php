<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePrefectures extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prefectures', function(Blueprint $t)
        {
            $t->increments('id');
            $t->string('name',16);
            $t->string('fb_name',10);
            $t->tinyInteger('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $t->timestamps();
        });
        DB::table('prefectures')->insert(
            array(
                'name' => '北海道',
                'fb_name' => 'Hokkaido',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '青森県',
                'fb_name' => 'Aomori',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '岩手県',
                'fb_name' => 'Iwate',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '宮城県',
                'fb_name' => 'Miyagi',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '秋田県',
                'fb_name' => 'Akita',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '山形県',
                'fb_name' => 'Yamagata',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '福島県',
                'fb_name' => 'Fukushima',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '茨城県',
                'fb_name' => 'Ibaraki',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '栃木県',
                'fb_name' => 'Tochigi',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '群馬県',
                'fb_name' => 'Gunma',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '埼玉県',
                'fb_name' => 'Saitama',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '千葉県',
                'fb_name' => 'Chiba',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '東京都',
                'fb_name' => 'Tokyo',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '神奈川県',
                'fb_name' => 'Kanagawa',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '新潟県',
                'fb_name' => 'Niigata',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '富山県',
                'fb_name' => 'Toyama',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '石川県',
                'fb_name' => 'Ishikawa',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '福井県',
                'fb_name' => 'Fukui',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '山梨県',
                'fb_name' => 'Yamanashi',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '長野県',
                'fb_name' => 'Nagano',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '岐阜県',
                'fb_name' => 'Gifu',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '静岡県',
                'fb_name' => 'Shizuoka',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '愛知県',
                'fb_name' => 'Aichi',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '三重県',
                'fb_name' => 'Mie',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '滋賀県',
                'fb_name' => 'Shiga',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '京都府',
                'fb_name' => 'Kyoto',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '大阪府',
                'fb_name' => 'Osaka',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '兵庫県',
                'fb_name' => 'Hyogo',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '奈良県',
                'fb_name' => 'Nara',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '和歌山県',
                'fb_name' => 'Wakayama',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '鳥取県',
                'fb_name' => 'Tottori',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '島根県',
                'fb_name' => 'Shimane',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '岡山県',
                'fb_name' => 'Okayama',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '広島県',
                'fb_name' => 'Hiroshima',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '山口県',
                'fb_name' => 'Yamaguchi',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '徳島県',
                'fb_name' => 'Tokushima',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '香川県',
                'fb_name' => 'Kagawa',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '愛媛県',
                'fb_name' => 'Ehime',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '高知県',
                'fb_name' => 'Kochi',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '福岡県',
                'fb_name' => 'Fukuoka',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '佐賀県',
                'fb_name' => 'Saga',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '長崎県',
                'fb_name' => 'Nagasaki',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '熊本県',
                'fb_name' => 'Kumamoto',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '大分県',
                'fb_name' => 'Oita',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '宮崎県',
                'fb_name' => 'Miyazaki',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '鹿児島県',
                'fb_name' => 'Kagoshima',
            )
        );
        DB::table('prefectures')->insert(
            array(
                'name' => '沖縄県',
                'fb_name' => 'Okinawa',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prefectures');
    }

}
