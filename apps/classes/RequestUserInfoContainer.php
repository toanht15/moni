<?php

class RequestUserInfoContainer {

    private $user;

    private $cp;

    private $cp_statuses = array();

    private static $instance;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new RequestUserInfoContainer();
        }
        return self::$instance;
    }

    public function getByMoniplaUserId($monipla_user_id) {
        if ($this->user !== null) {
            return $this->user;
        }

        /** @var UserService $user_service */
        $user_service = aafwServiceFactory::create('UserService');
        $this->user = $user_service->getUserByMoniplaUserId($monipla_user_id);
        return $this->user;
    }

    public function getCpById($cp_id = null) {
        if ($this->cp !== null) {
            return $this->cp;
        }
        if ($cp_id === null) {
            return null;
        }

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = aafwServiceFactory::create('CpFlowService');
        $this->cp = $cp_flow_service->getCpById($cp_id);

        return $this->cp;
    }

    public function getStatusByCp($cp) {
        $cp_id = $cp->id;
        if (!isset($this->cp_statuses[$cp_id])) {
            $this->cp_statuses[$cp_id] = $cp->getStatus();
        }
        return $this->cp_statuses[$cp_id];
    }
}