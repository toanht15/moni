<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class SyncWithdrawWithAlliedId {

    private $service_factory;

    /** @var UserService $user_service */
    private $user_service;

    /** @var BrandsUsersRelationService $brands_users_relation_service */
    private $brands_users_relation_service;

    public function __construct() {
        $this->service_factory = new aafwServiceFactory();
        $this->user_service = $this->service_factory->create('UserService');
        $this->brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');
    }

    public function doProcess(){
        $start_date = date("Y-m-d 00:00:00", strtotime('-1 day'));
        $end_date = date("Y-m-d 23:59:59", strtotime('-1 day'));
        $platform_ids = $this->getWithdrawUserAlliedId($start_date, $end_date);

        if (!$platform_ids) return;

        $monipla_user_ids = [];
        foreach ($platform_ids as $platform_id) {
            $monipla_user_ids[] = $platform_id['id'];
        }

        $users = $this->user_service->getUsersByMoniplaUserIds($monipla_user_ids);
        if (!$users) return;

        $user_ids = [];
        foreach ($users as $user) {
            $user_ids[] = $user->id;
        }

        $brand_users_relations = $this->brands_users_relation_service->getAllRelationsByUserIds($user_ids);

        //AlliedIDが退会済みなので新モニも退会
        foreach ($brand_users_relations as $brand_users_relation) {
            $this->brands_users_relation_service->withdrawByBrandUserRelation($brand_users_relation, true);
        }
    }

    private function getWithdrawUserAlliedId($start_date, $end_date) {
        $mainte_db = new aafwDataBuilder("maintedb");

        $query = "SELECT u.id";
        $query .= " FROM users u";
        $query .= ' WHERE u.del_flg = 1 AND u.updated_at BETWEEN "' . $start_date . '" AND "' . $end_date . '"';

        return $platform_ids = $mainte_db->getBySQL($query);
    }
}