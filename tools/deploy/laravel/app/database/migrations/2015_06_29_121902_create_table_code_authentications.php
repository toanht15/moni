<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCodeAuthentications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('code_authentications', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('brand_id');
            $table->string('name', 255);
            $table->text('description');
            $table->dateTime('expire_date');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->index('brand_id');
        });

        Schema::create('code_authentication_codes', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('code_auth_id');
            $table->string('code', 255);
            $table->integer('max_num')->default(0);
            $table->integer('reserved_num')->default(0);
            $table->dateTime('expire_date');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('code_auth_id')->references('id')->on('code_authentications');
            $table->index('code_auth_id');
            $table->unique(array('code_auth_id', 'code'), 'code_authentication_code_unique_key');
        });

        Schema::create('cp_code_authentication_actions', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cp_action_id');
            $table->string('title');
            $table->string('image_url', 512);
            $table->text('text');
            $table->text('html_content');
            $table->unsignedBigInteger('code_auth_id')->nullable();
            $table->integer('min_code_count')->default(1);
            $table->tinyInteger('min_code_flg')->default(1);
            $table->integer('max_code_count')->default(1);
            $table->tinyInteger('max_code_flg')->default(1);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->foreign('code_auth_id')->references('id')->on('code_authentications');
            $table->index('cp_action_id');
            $table->index('code_auth_id');
        });

        Schema::create('code_authentication_users', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('code_auth_code_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('cp_action_id');
            $table->tinyInteger('used_flg')->default(0);
            $table->dateTime('used_date');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('code_auth_code_id')->references('id')->on('code_authentication_codes');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->index('code_auth_code_id');
            $table->index('user_id');
            $table->index('cp_action_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('code_authentication_users');
        Schema::drop('cp_code_authentication_actions');
        Schema::drop('code_authentication_codes');
        Schema::drop('code_authentications');
    }

}
