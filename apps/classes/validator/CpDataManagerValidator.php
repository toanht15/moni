<?php
AAFW::import('jp.aainc.classes.CpInfoContainer');

class CpDataManagerValidator extends CpValidator {
    private $cp;
    private $cur_type;
    private $cp_action_id;

    public function __construct($brand_id, $cp_action_id, $cur_type) {
        parent::__construct($brand_id);

        $this->cur_type = $cur_type;
        $this->cp_action_id = $cp_action_id;
    }

    /**
     * @return bool|mixed
     */
    public function getCpAction() {
        if ($this->cp_action) {
            return $this->cp_action;
        }

        if ($this->isEmpty($this->cp_action_id)) {
            return false;
        }

        $this->cp_action = $this->service->getCpActionById($this->cp_action_id);

        return $this->cp_action ? $this->cp_action : false;
    }

    /**
     * @return bool
     */
    public function getCp() {
        if ($this->cp) {
            return $this->cp;
        }

        if ($this->getCpAction() == false) {
            return false;
        }

        if (!$this->getCpAction()->cp_action_group_id) {
            return false;
        }

        $cp_action_group = $this->getCpAction()->getCpActionGroup();
        $this->cp = CpInfoContainer::getInstance()->getCpById($cp_action_group->cp_id);

        return $this->cp ? $this->cp : false;
    }

    /**
     * @return bool
     */
    public function isValidCp() {
        if ($this->getCp() == false) {
            return false;
        }

        return $this->getCp()->status != Cp::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isValidCpAction() {
        if ($this->getCpAction() == false) {
            return false;
        }

        return $this->getCpAction()->type == $this->cur_type;
    }

    /**
     * @return bool
     */
    public function isOwnerOfAction() {
        if ($this->getCp() == false) {
            return false;
        }

        return $this->getCp()->brand_id == $this->brandId;
    }

    /**
     * @return bool
     */
    public function validate() {
        if (!$this->isValidCp()) {
            return false;
        }

        if (!$this->isValidCpAction()) {
            return false;
        }

        if (!$this->isOwnerOfAction()) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getCpActionInfo() {
        return array(
            'cp_id' => $this->getCp()->id,
            'action_id' => $this->cp_action_id
        );
    }
}