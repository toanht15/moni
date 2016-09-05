<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryRoomsMessagesRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('inquiry_rooms_messages_relations', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('inquiry_room_id');
			$table->unsignedInteger('inquiry_message_id');
			$table->boolean('forward_flg')->default(0);
			$table->boolean('forwarded_flg')->default(0);
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('inquiry_room_id')->references('id')->on('inquiry_rooms');
			$table->foreign('inquiry_message_id')->references('id')->on('inquiry_messages');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('inquiry_rooms_messages_relations');
	}

}
