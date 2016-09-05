<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminInviteTokens extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_invite_tokens', function(Blueprint $t)
        {
            $t->increments('id');
            $t->integer('brand_id')->unsigned();
            $t->string('mail_address',255)->default('');
            $t->string('token',255)->default('');
            $t->string('password',32)->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('admin_invite_tokens');
    }

}
