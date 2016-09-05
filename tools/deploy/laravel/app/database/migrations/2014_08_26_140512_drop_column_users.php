<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnUsers extends Migration {

     /**
      * Run the migrations.
      *
      * @return void
      */
     public function up()
     {
          Schema::table('users', function(Blueprint $table)
          {
              $table->dropColumn('last_login_date');
              $table->dropColumn('login_count');
          });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
          Schema::table('users', function(Blueprint $table)
          {
              $table->dateTime('last_login_date')->default('0000-00-00 00:00:00')->after('mp_token_update_at');
              $table->integer('login_count')->unsigned()->default(0)->after('last_login_date');
          });
     }

}
