<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Settlement extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//GMO site情報格納テーブル 1ブランドに対して1件
		Schema::create('brand_sites', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('brand_id')->unsigned();
			$table->string('gmo_site_id');
			$table->string('gmo_site_pass');
			$table->timestamps();

			$table->foreign('brand_id')->references('id')->on('brands');
		});
		//GMO shop情報格納テーブル 1サイトに対し複数
		Schema::create('brand_shops', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('brand_site_id')->unsigned();
			$table->string('shop_name');
			$table->string('gmo_shop_id');
			$table->string('gmo_shop_pass');
			$table->timestamps();

			$table->foreign('brand_site_id')->references('id')->on('brand_sites');

		});

		//キャンペーンと1:1
		Schema::create('products', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title');
			$table->text('image_url');
			$table->integer('cp_id')->unsigned();
			$table->integer('cp_action_id')->unsigned();
			$table->integer('brand_shop_id')->unsigned();
			$table->integer('delivery_charge');
			$table->string('inquiry_name');
			$table->time('inquiry_time1');
			$table->time('inquiry_time2');
			$table->string('inquiry_phone');
			$table->timestamps();

			$table->foreign('cp_id')->references('id')->on('cps');
			$table->foreign('cp_action_id')->references('id')->on('cp_actions');
			$table->foreign('brand_shop_id')->references('id')->on('brand_shops');
		});

		//商品アイテム情報
		Schema::create('product_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('product_id')->unsigned();
			$table->string('title');
			$table->string('description');
			$table->text('image_url');
			$table->integer('display_order');
			$table->integer('stock');
			$table->integer('sale_count');
			$table->boolean('stock_limited');
			$table->integer('unit_price');
			$table->timestamps();

			$table->index('display_order');
			$table->foreign('product_id')->references('id')->on('products');

		});
		//注文情報仮登録
		Schema::create('pre_orders', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('product_id')->unsigned();
			$table->text('salt');
			$table->string('access_code')->unique();
			$table->longText('data');
			$table->timestamp('expiration_at');
			$table->timestamps();

			$table->index('access_code');
			$table->index('expiration_at');
			$table->foreign('product_id')->references('id')->on('products');

		});
		//注文情報
		Schema::create('orders', function (Blueprint $table) {
			$table->increments('id');
			$table->bigInteger('user_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->string('order_access_code')->unique()->nullable();
			$table->string('gmo_payment_order_id')->unique()->nullable();
			$table->string('access_id')->nullable();
			$table->string('access_pass')->nullable();
			$table->string('rakuten_token')->nullable();
			$table->string('payment_status')->nullable();
			$table->integer('pay_type');
			$table->string('pay_type_name');
			$table->string('convenience_code')->nullable();
			$table->string('convenience_name')->nullable();
			$table->integer('delivery_charge');
			$table->integer('sub_total_cost');
			$table->integer('total_cost');
			$table->timestamp('payment_completion_date');
			$table->timestamp('payment_term_date');
			$table->timestamp('order_completion_date');
			$table->boolean('is_cancel')->default(false);
			$table->timestamp('canceled_at')->nullable();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('first_name_kana');
			$table->string('last_name_kana');
			$table->string('zip_code1');
			$table->string('zip_code2');
			$table->string('pref_name');
			$table->text('address1');
			$table->text('address2');
			$table->text('address3');
			$table->string('tel_no1');
			$table->string('tel_no2');
			$table->string('tel_no3');
			$table->string('payment_conf_no')->nullable();
			$table->string('payment_receipt_no')->nullable();
			$table->string('payment_tran_date')->nullable(); //決済日付 gmo上での決済 即時決済のため、申し込み完了日と同日になるはず。
			$table->string('payment_receipt_url')->nullable();
			$table->string('payment_check_string')->nullable();
			$table->string('payment_client_field_1')->nullable();
			$table->string('payment_client_field_2')->nullable();
			$table->string('payment_client_field_3')->nullable();
			$table->string('payment_credit')->nullable();
			$table->timestamp('payment_status_updated_at')->nullable();
			$table->timestamp('mail_remind_send_date')->nullable();
			$table->timestamp('mail_request_send_date')->nullable();
			$table->timestamp('mail_cancel_send_date')->nullable();
			$table->timestamp('mail_complete_send_date')->nullable();
			$table->timestamps();

			$table->index('order_access_code');
			$table->index('gmo_payment_order_id');
			$table->index('payment_status');
			$table->index('pay_type');
			$table->index('payment_completion_date');
			$table->index('order_completion_date');
			$table->index('payment_tran_date');
			$table->index('payment_status_updated_at');
			$table->index('mail_remind_send_date');
			$table->index('mail_cancel_send_date');
			$table->index('mail_complete_send_date');

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('product_id')->references('id')->on('products');

		});
		//注文商品
		Schema::create('order_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_id')->unsigned();
			$table->integer('product_item_id')->unsigned();
			$table->integer('sales_count'); //販売数
			$table->integer('unit_price');
			$table->string('product_item_title');
			$table->timestamp('delivery_date');
			$table->boolean('delivery_flg')->default(false);
			$table->timestamps();

			$table->index('delivery_flg');
			$table->foreign('order_id')->references('id')->on('orders');
			$table->foreign('product_item_id')->references('id')->on('product_items');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_items');
		Schema::drop('pre_orders');
		Schema::drop('product_items');
		Schema::drop('orders');
		Schema::drop('products');
		Schema::drop('brand_shops');
		Schema::drop('brand_sites');
	}

}
