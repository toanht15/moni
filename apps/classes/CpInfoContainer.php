<?php

AAFW::import('jp.aainc.classes.CacheManager');
/**
 * 1リクエスト中に有効なキャンペーン情報のコンテナ。
 * Class CpInfoContainer
 */
class CpInfoContainer {

    const KEY_CP = "cp";

    private static $instance;

    private $cp_info = array();

    private $cp_actions = array();

    /** @var CacheManager cache_manager */
    private $cache_manager;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CpInfoContainer();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->cache_manager = new CacheManager();
    }

    public function getCpById($cp_id) {
        if (Util::isNullOrEmpty($cp_id)) {
            return null;
        }

        $cp = $this->cp_info[self::KEY_CP];
        if ($cp === null || $cp->id != $cp_id) {
            $cached_info = $this->cache_manager->getCpInfo($cp_id);
            if (isset($cached_info[self::KEY_CP])) {
                $cp = aafwEntityStoreBase::newEntity("Cps", $cached_info[self::KEY_CP]);
                $this->cp_info[self::KEY_CP] = $cp;
                return $cp;
            }

            /** @var CpFLowService $cp_flow_service */
            $cp_flow_service = aafwServiceFactory::create('CpFlowService');
            $cp = $cp_flow_service->getCpById($cp_id);
            if (!$cp) {
                return null;
            }
            $this->cp_info[self::KEY_CP] = $cp;
            $this->cache_manager->setCpInfo($cp_id, array(self::KEY_CP => $cp->toArray()));
        }

        return $cp;
    }

    public function getCpActionById($cp_action_id) {
        if (!isset($this->cp_actions[$cp_action_id])) {
            $cp_action_info = $this->cache_manager->getCpActionInfo($cp_action_id);
            if (!$cp_action_info) {
                $store = aafwEntityStoreFactory::create("CpActions");
                $cp_action = $store->findOne(array("id" => $cp_action_id));
                if (!$cp_action) {
                    return null;
                }
                $this->cp_actions[$cp_action_id] = $cp_action;
                $this->cache_manager->setCpActionInfo($cp_action_id, $cp_action->toArray());
                return $cp_action;
            }
            $cp_action = aafwEntityStoreBase::newEntity("CpActions", $cp_action_info);
            $this->cp_actions[$cp_action_id] = $cp_action;
            return $cp_action;
        }
        return $this->cp_actions[$cp_action_id];
    }

    public function clearCpActionById($cp_action_id) {
        $this->cache_manager->clearCpActionInfo($cp_action_id);
    }

    public function clear() {
        $this->cp_info = array();
        $this->cp_actions = array();
    }
}