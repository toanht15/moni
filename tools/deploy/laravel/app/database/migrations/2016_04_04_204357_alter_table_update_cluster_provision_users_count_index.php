<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUpdateClusterProvisionUsersCountIndex extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cluster_provision_users_count', function(Blueprint $table) {
            $table->dropIndex('cluster_provision_users_count_created_date_index');
            $table->index(array('cluster_provision_id', 'created_date'), 'cluster_provision_users_count_cp_id_created_date_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cluster_provision_users_count', function(Blueprint $table) {
            $table->dropIndex('cluster_provision_users_count_cp_id_created_date_index');
            $table->index('created_date');
        });
    }

}
