<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClusteringDefaultTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // Clusters
        Schema::create('clusters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('brand_id');
            $table->string('name');
            $table->text('description');
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('archive_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
        });

        // Cluster action logs
        Schema::create('cluster_action_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cluster_id');
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cluster_id')->references('id')->on('clusters');
        });

        // Cluster Provisions
        Schema::create('cluster_provisions', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cluster_id');
            $table->string('name');
            $table->integer('order_no')->default(1);
            $table->text('provision');
            $table->tinyInteger('unclassified_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cluster_id')->references('id')->on('clusters');
        });

        // Cluster provisions users count
        Schema::create('cluster_provision_users_count', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cluster_provision_id');
            $table->integer('total')->default(0);
            $table->tinyInteger('del_flg');
            $table->integer('created_date');
            $table->timestamp('created_at');

            $table->foreign('cluster_provision_id')->references('id')->on('cluster_provisions');
            $table->index('created_date');
        });

        // Cluster provisions users relations
        Schema::create('cluster_provisions_users_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cluster_provision_id');
            $table->unsignedBigInteger('brands_users_relation_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->integer('created_date');
            $table->timestamp('created_at');

            $table->foreign('cluster_provision_id')->references('id')->on('cluster_provisions');
            $table->foreign('brands_users_relation_id', 'cluster_provisions_users_relations_bur_id')->references('id')->on('brands_users_relations');
            $table->index(array('cluster_provision_id', 'created_date'), 'cluster_provisions_users_relations_cp_id_created_date_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('cluster_provisions_users_relations');
        Schema::drop('cluster_provision_users_count');
        Schema::drop('cluster_provisions');
        Schema::drop('cluster_action_logs');
        Schema::drop('clusters');
    }

}
