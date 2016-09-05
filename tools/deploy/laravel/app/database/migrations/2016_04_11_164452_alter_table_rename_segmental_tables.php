<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRenameSegmentalTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('clusters', function(Blueprint $table) {
            $table->rename('segments');
        });

        Schema::table('cluster_provisions', function(Blueprint $table) {
            $table->rename('segment_provisions');
        });

        Schema::table('cluster_action_logs', function(Blueprint $table) {
            $table->rename('segment_action_logs');
        });

        Schema::table('cluster_provision_users_count', function(Blueprint $table) {
            $table->rename('segment_provision_users_count');
        });

        Schema::table('cluster_provisions_users_relations', function(Blueprint $table) {
            $table->rename('segment_provisions_users_relations');
        });

        Schema::table('segments', function(Blueprint $table) {
            $table->dropForeign('clusters_brand_id_foreign');
            $table->dropIndex('clusters_brand_id_foreign');
            $table->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::table('segment_provisions', function(Blueprint $table) {
            $table->renameColumn('cluster_id', 'segment_id');

            $table->dropForeign('cluster_provisions_cluster_id_foreign');
            $table->dropIndex('cluster_provisions_cluster_id_foreign');
            $table->foreign('segment_id')->references('id')->on('segments');
        });

        Schema::table('segment_action_logs', function(Blueprint $table) {
            $table->renameColumn('cluster_id', 'segment_id');

            $table->dropForeign('cluster_action_logs_cluster_id_foreign');
            $table->dropIndex('cluster_action_logs_cluster_id_foreign');
            $table->foreign('segment_id')->references('id')->on('segments');
        });

        Schema::table('segment_provision_users_count', function(Blueprint $table) {
            $table->renameColumn('cluster_provision_id', 'segment_provision_id');

            $table->dropForeign('cluster_provision_users_count_cluster_provision_id_foreign');
            $table->dropIndex('cluster_provision_users_count_cp_id_created_date_index');

            $table->index(array('segment_provision_id', 'created_date'), 'segment_provision_users_count_sp_id_created_date_index');
            $table->foreign('segment_provision_id')->references('id')->on('segment_provisions');
        });

        Schema::table('segment_provisions_users_relations', function(Blueprint $table) {
            $table->renameColumn('cluster_provision_id', 'segment_provision_id');

            $table->dropForeign('cluster_provisions_users_relations_bur_id');
            $table->dropIndex('cluster_provisions_users_relations_bur_id');

            $table->dropForeign('cluster_provisions_users_relations_cluster_provision_id_foreign');
            $table->dropIndex('cluster_provisions_users_relations_cp_id_created_date_index');

            $table->index(array('segment_provision_id', 'created_date'), 'segment_provisions_users_relations_sp_id_created_date_index');
            $table->foreign('segment_provision_id')->references('id')->on('segment_provisions');
            $table->foreign('brands_users_relation_id', 'segment_provisions_users_relations_bur_id')->references('id')->on('brands_users_relations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('segments', function(Blueprint $table) {
            $table->rename('clusters');
        });

        Schema::table('segment_provisions', function(Blueprint $table) {
            $table->rename('cluster_provisions');
        });

        Schema::table('segment_action_logs', function(Blueprint $table) {
            $table->rename('cluster_action_logs');
        });

        Schema::table('segment_provision_users_count', function(Blueprint $table) {
            $table->rename('cluster_provision_users_count');
        });

        Schema::table('segment_provisions_users_relations', function(Blueprint $table) {
            $table->rename('cluster_provisions_users_relations');
        });

        Schema::table('clusters', function(Blueprint $table) {
            $table->dropForeign('segments_brand_id_foreign');
            $table->dropIndex('segments_brand_id_foreign');
            $table->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::table('cluster_provisions', function(Blueprint $table) {
            $table->renameColumn('segment_id', 'cluster_id');

            $table->dropForeign('segment_provisions_segment_id_foreign');
            $table->dropIndex('segment_provisions_segment_id_foreign');

            $table->foreign('cluster_id')->references('id')->on('clusters');
        });

        Schema::table('cluster_action_logs', function(Blueprint $table) {
            $table->renameColumn('segment_id', 'cluster_id');

            $table->dropForeign('segment_action_logs_segment_id_foreign');
            $table->dropIndex('segment_action_logs_segment_id_foreign');
            $table->foreign('cluster_id')->references('id')->on('clusters');
        });

        Schema::table('cluster_provision_users_count', function(Blueprint $table) {
            $table->renameColumn('segment_provision_id', 'cluster_provision_id');

            $table->dropForeign('segment_provision_users_count_segment_provision_id_foreign');
            $table->dropIndex('segment_provision_users_count_sp_id_created_date_index');

            $table->index(array('cluster_provision_id', 'created_date'), 'cluster_provision_users_count_cp_id_created_date_index');
            $table->foreign('cluster_provision_id')->references('id')->on('cluster_provisions');
        });

        Schema::table('cluster_provisions_users_relations', function(Blueprint $table) {
            $table->renameColumn('segment_provision_id', 'cluster_provision_id');

            $table->dropForeign('segment_provisions_users_relations_bur_id');
            $table->dropIndex('segment_provisions_users_relations_bur_id');

            $table->dropForeign('segment_provisions_users_relations_segment_provision_id_foreign');
            $table->dropIndex('segment_provisions_users_relations_sp_id_created_date_index');

            $table->index(array('cluster_provision_id', 'created_date'), 'cluster_provisions_users_relations_cp_id_created_date_index');
            $table->foreign('brands_users_relation_id', 'cluster_provisions_users_relations_bur_id')->references('id')->on('brands_users_relations');
            $table->foreign('cluster_provision_id')->references('id')->on('cluster_provisions');
        });
    }

}
