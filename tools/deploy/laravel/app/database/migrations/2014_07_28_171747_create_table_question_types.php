<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestionTypes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_types', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->increments('id');

            $t->string('name',30)->default('');
            $t->tinyInteger('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $t->timestamps();
        });

        DB::table('question_types')->insert(
            array(
                'name' => 'Choice Answer',
            )
        );

        DB::table('question_types')->insert(
            array(
                'name' => 'Free Answer',
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
        Schema::drop('question_types');
    }

}
