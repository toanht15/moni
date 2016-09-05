<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionTypePulldown extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $value = array(
            array(
                'name' => 'Choice Pulldown Answer',
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
        DB::table('question_types')->where('name','Choice Pulldown Answer')->delete();
    }
}
