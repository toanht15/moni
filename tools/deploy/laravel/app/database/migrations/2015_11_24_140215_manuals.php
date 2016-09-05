<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Manuals extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $value = array(
            'id' => '1',
            'title' => '【モニプラ】ファンサイト構築マニュアル',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E5%88%9D%E6%9C%9F%E6%A7%8B%E7%AF%89%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/%E3%80%90%E3%83%A2%E3%83%8B%E3%83%97%E3%83%A9%E3%80%91%E3%83%95%E3%82%A1%E3%83%B3%E3%82%B5%E3%82%A4%E3%83%88%E6%A7%8B%E7%AF%89%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB.pdf',
            'type' => '0',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '2',
            'title' => '001_基本編：ファンサイトを5分で構築',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E5%88%9D%E6%9C%9F%E6%A7%8B%E7%AF%89%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/001_%E5%9F%BA%E6%9C%AC%E7%B7%A8%EF%BC%9A%E3%83%95%E3%82%A1%E3%83%B3%E3%82%B5%E3%82%A4%E3%83%88%E3%82%925%E5%88%86%E3%81%A7%E6%A7%8B%E7%AF%89.pdf',
            'type' => '0',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '3',
            'title' => '002_応用編：ファンサイトを自由にカスタマイス',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E5%88%9D%E6%9C%9F%E6%A7%8B%E7%AF%89%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/002_%E5%BF%9C%E7%94%A8%E7%B7%A8%EF%BC%9A%E3%83%95%E3%82%A1%E3%83%B3%E3%82%B5%E3%82%A4%E3%83%88%E3%82%92%E8%87%AA%E7%94%B1%E3%81%AB%E3%82%AB%E3%82%B9%E3%82%BF%E3%83%9E%E3%82%A4%E3%82%BA.pdf',
            'type' => '0',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '4',
            'title' => '001_キャンペーンを作成する',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/001_%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E3%82%92%E4%BD%9C%E6%88%90%E3%81%99%E3%82%8B.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '5',
            'title' => '002_キャンペーンの基本設計',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/002_%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E3%81%AE%E5%9F%BA%E6%9C%AC%E8%A8%AD%E8%A8%88.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '6',
            'title' => '003_キャンペーンの詳細設定',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/003_%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E3%81%AE%E8%A9%B3%E7%B4%B0%E8%A8%AD%E5%AE%9A.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '7',
            'title' => '004_キャンペーン公開',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/004_%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E5%85%AC%E9%96%8B.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '8',
            'title' => '005_下書き・過去のキャンペーンを活用する',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/005_%E4%B8%8B%E6%9B%B8%E3%81%8D%E3%83%BB%E9%81%8E%E5%8E%BB%E3%81%AE%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E3%82%92%E6%B4%BB%E7%94%A8%E3%81%99%E3%82%8B.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '9',
            'title' => '006_モジュールを使う',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/006_%E3%83%A2%E3%82%B8%E3%83%A5%E3%83%BC%E3%83%AB%E3%82%92%E4%BD%BF%E3%81%86.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '10',
            'title' => '007_メッセージ',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/007_%E3%83%A1%E3%83%83%E3%82%BB%E3%83%BC%E3%82%B8.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '11',
            'title' => '008_写真投稿',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/008_%E5%86%99%E7%9C%9F%E6%8A%95%E7%A8%BF.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '12',
            'title' => '009_アンケート',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/009_%E3%82%A2%E3%83%B3%E3%82%B1%E3%83%BC%E3%83%88.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '13',
            'title' => '010_人気投票',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/010_%E4%BA%BA%E6%B0%97%E6%8A%95%E7%A5%A8.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '14',
            'title' => '011_自由回答',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/011_%E8%87%AA%E7%94%B1%E5%9B%9E%E7%AD%94.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '15',
            'title' => '012_スピードくじ',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/012_%E3%82%B9%E3%83%94%E3%83%BC%E3%83%89%E3%81%8F%E3%81%98.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '16',
            'title' => '013_動画視聴',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/013_%E5%8B%95%E7%94%BB%E8%A6%96%E8%81%B4.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '17',
            'title' => '014_YouTubeチャンネル登録',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/014_YouTube%E3%83%81%E3%83%A3%E3%83%B3%E3%83%8D%E3%83%AB%E7%99%BB%E9%8C%B2.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '18',
            'title' => '015_クーポン',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/015_%E3%82%AF%E3%83%BC%E3%83%9D%E3%83%B3.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '19',
            'title' => '016_コード認証',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/016_%E3%82%B3%E3%83%BC%E3%83%89%E8%AA%8D%E8%A8%BC.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '20',
            'title' => '017_シェア',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/017_%E3%82%B7%E3%82%A7%E3%82%A2.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '21',
            'title' => '018_Facebookいいね！',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/018_Facebook%E3%81%84%E3%81%84%E3%81%AD%EF%BC%81.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '22',
            'title' => '019_Twitterフォロー',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/019_Twitter%E3%83%95%E3%82%A9%E3%83%AD%E3%83%BC.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '23',
            'title' => '020_Twitterツイート',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/020_Twitter%E3%83%84%E3%82%A4%E3%83%BC%E3%83%88.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '24',
            'title' => '021_Twitterリツイート',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/021_Twitter%E3%83%AA%E3%83%84%E3%82%A4%E3%83%BC%E3%83%88.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '25',
            'title' => '022_Instagramフォロー',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/022_Instagram%E3%83%95%E3%82%A9%E3%83%AD%E3%83%BC.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '26',
            'title' => '023_Instagram投稿',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/023_Instagram%E6%8A%95%E7%A8%BF.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '27',
            'title' => '024_当選発表を実施する',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/024_%E5%BD%93%E9%81%B8%E7%99%BA%E8%A1%A8%E3%82%92%E5%AE%9F%E6%96%BD%E3%81%99%E3%82%8B.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

        $value = array(
            'id' => '28',
            'title' => '025_モニプラの推奨環境',
            'url' => 'https://parts.brandco.jp.s3.amazonaws.com/static/manual/%E3%82%AD%E3%83%A3%E3%83%B3%E3%83%9A%E3%83%BC%E3%83%B3%E4%BD%9C%E6%88%90%E3%83%9E%E3%83%8B%E3%83%A5%E3%82%A2%E3%83%AB/025_%E3%83%A2%E3%83%8B%E3%83%97%E3%83%A9%E3%81%AE%E6%8E%A8%E5%A5%A8%E7%92%B0%E5%A2%83.pdf',
            'type' => '1',
            'del_flg' => '0',
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );
        DB::table('manuals')->insert($value);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('manuals')->where('id', '1')->delete();
        DB::table('manuals')->where('id', '2')->delete();
        DB::table('manuals')->where('id', '3')->delete();
        DB::table('manuals')->where('id', '4')->delete();
        DB::table('manuals')->where('id', '5')->delete();
        DB::table('manuals')->where('id', '6')->delete();
        DB::table('manuals')->where('id', '7')->delete();
        DB::table('manuals')->where('id', '8')->delete();
        DB::table('manuals')->where('id', '9')->delete();
        DB::table('manuals')->where('id', '10')->delete();
        DB::table('manuals')->where('id', '11')->delete();
        DB::table('manuals')->where('id', '12')->delete();
        DB::table('manuals')->where('id', '13')->delete();
        DB::table('manuals')->where('id', '14')->delete();
        DB::table('manuals')->where('id', '15')->delete();
        DB::table('manuals')->where('id', '16')->delete();
        DB::table('manuals')->where('id', '17')->delete();
        DB::table('manuals')->where('id', '18')->delete();
        DB::table('manuals')->where('id', '19')->delete();
        DB::table('manuals')->where('id', '20')->delete();
        DB::table('manuals')->where('id', '21')->delete();
        DB::table('manuals')->where('id', '22')->delete();
        DB::table('manuals')->where('id', '23')->delete();
        DB::table('manuals')->where('id', '24')->delete();
        DB::table('manuals')->where('id', '25')->delete();
        DB::table('manuals')->where('id', '26')->delete();
        DB::table('manuals')->where('id', '27')->delete();
        DB::table('manuals')->where('id', '28')->delete();
    }
}
