<?php
AAFW::import('jp.aainc.classes.CpInfoContainer');

class CpValidator extends aafwObject {

    const MAX_TEXT_LENGTH = 20000;
    const SHORT_TEXT_LENGTH = 2000;
    const CV_TAG_MAX_LENGTH = 8000;

    //URLのMAXが23のため、140-23。
    const MAX_TWITTER_SHARE_TEXT = 117;

    protected $brandId;
    /** @var  CpFlowService $service*/
    protected $service;

    public $cp_action;
    public $group;

    public $_cp;

    public function __construct($brandId) {
        parent::__construct();
        $this->service = $this->_ServiceFactory->create('CpFlowService');
        $this->setBrandId($brandId);
    }

    /**
     * @param $id
     */
    public function setBrandId($id){
        $this->brandId = $id;
    }

    public function isOwner($id) {
        if ($this->isEmpty($id)) {
            return false;
        }
        $cp = CpInfoContainer::getInstance()->getCpById($id);
        if (!$cp || ($cp->brand_id != $this->brandId)) {
            return false;
        }

        return true;
    }

    public function isOwnerOfAction($action_id)  {
        if ($this->isEmpty($action_id)) {
            return false;
        }

        $action = CpInfoContainer::getInstance()->getCpActionById($action_id);
        if (!$action->cp_action_group_id) {
            return false;
        }

        $this->cp_action = $action;
        $this->group = $group = $this->service->getCpActionGroupById($action->cp_action_group_id);
        $this->_cp = $cp = CpInfoContainer::getInstance()->getCpById($group->cp_id);

        if (!$cp || ($cp->brand_id != $this->brandId)) {
            return false;
        }

        return true;
    }

    //$date : Y/m/d H:i:s
    public function isCorrectDate($date) {
        if ($this->isEmpty($date)) {
            return false;
        }
        $now = date("Y/m/d H:i:s");
        $now = DateTime::createFromFormat("Y/m/d H:i:s", $now);
        $date = DateTime::createFromFormat("Y/m/d H:i:s", $date);
        if (!$now || !$date) {
            return false;
        }
        if ($now > $date) {
            return false;
        }
        return true;
    }

    public function getMax($dates = array()) {
        if (count($dates) < 1) {
            return null;
        }
        $maxString = $dates[0];
        $maxDateTime = DateTime::createFromFormat("Y/m/d H:i:s", $maxString);
        foreach ($dates as $date) {
            $tmp = DateTime::createFromFormat("Y/m/d H:i:s", $date);
            if ($maxDateTime < $tmp) {
                $maxDateTime = $tmp;
                $maxString = $date;
            }
        }
        return $maxString;
    }

    public function isFirstActionOfGroup($action_id) {
        $action = $this->service->getCpActionById($action_id);
        $first_action = $this->service->getFirstActionInGroupByAction($action);
        if ($first_action->id != $action_id) {
            return false;
        }
        return true;
    }

    public function isIncludedInCp($cp_id, $action_id) {
        $action_group = $this->service->getCpActionGroupByAction($action_id);
        if ($action_group->cp_id != $cp_id) {
            return false;
        }
        return true;
    }

}
