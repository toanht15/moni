<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterQuestionnaireTableToImage extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->string('image_url',512)->default('')->after("choice");
        });

        $value = array(
            array(
                'name' => 'Choice Image Answer',
                'del_flg' => 0,
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00'
            )
        );
        DB::table('question_types')->insert($value);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_choices', function(Blueprint $table)
        {
            $table->dropColumn('image_url');
        });
        DB::table('question_types')->where('name','Choice Image Answer')->delete();
    }

}
