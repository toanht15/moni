<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserAttributeMasters extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_attribute_masters', function(Blueprint $t)
        {
            $t->integer('id');
            $t->integer('category_id');
            $t->tinyInteger('required');
            $t->string('question',30);
            $t->string('description',30);
            $t->tinyInteger('data_type');
            $t->tinyInteger('choice_type');
            $t->string('choices',30);
            $t->tinyInteger('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique('id');
        });
        DB::table('user_attribute_masters')->insert(
            array(
                'id' => '-2',
                'category_id' => '1',
                'required' => '1',
                'question' => 'birth_day',
                'description' => 'birth_day',
                'data_type' => '2',
                'choice_type' => '3',
                'choices' => '',
            )
        );
        DB::table('user_attribute_masters')->insert(
            array(
                'id' => '-1',
                'category_id' => '1',
                'required' => '1',
                'question' => 'sex',
                'description' => 'sex',
                'data_type' => '2',
                'choice_type' => '1',
                'choices' => '[\"f\",\"m\"]',
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
        Schema::drop('user_attribute_masters');
    }

}
