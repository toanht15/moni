<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSocialAccounts extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('social_accounts');

        Schema::create('social_accounts', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->integer('social_media_id')->unsigned();
            $t->string('social_media_account_id',255);
            $t->string('name',255);
            $t->string('mail_address',255)->default('');
            $t->string('profile_image_url',255)->default('');
            $t->string('profile_page_url',255)->default('');
            $t->bigInteger('user_id')->unsigned();
            $t->smallInteger('validated');
            $t->tinyInteger('del_flg')->default(0);
        
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('social_media_id','social_media_account_id');
            $t->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_accounts');

        Schema::create('social_accounts', function(Blueprint $t)
        {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->integer('social_media_id')->unsigned();
            $t->string('social_media_account_id',255);
            $t->string('name',255);
            $t->string('mail_address',255);
            $t->string('profile_image_url',255);
            $t->string('profile_page_url',255);
            $t->bigInteger('user_id')->unsigned();
            $t->smallInteger('validated');
            $t->tinyInteger('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('social_media_id','social_media_account_id');
            $t->foreign('user_id')->references('id')->on('users');
        });
    }
}
