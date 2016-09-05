<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.segment.trait.SegmentActionTrait');

class segment_action_message extends BrandcoGETActionBase {

    use SegmentActionTrait;

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    public $NeedAdminLogin = true;

    private $sp_ids_array;

    public function validate() {

        if (!$_GET) {
            return '404';
        }

        $is_valid = false;
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'sp_ids_') !== false) {
                $is_valid = true;
                $temp_rs = explode('sp_ids_', $key);
                $this->sp_ids_array[$temp_rs[1]] = $value;
            }
        }

        if (!$is_valid) {
            return '404';
        }

        if($this->isContainInvalidSegment(array_keys($this->sp_ids_array), $this->getBrand()->id)) {
            return '404';
        }

        return true;
    }

    public function doAction() {

        $this->setBrandSession(SegmentService::SEGMENT_CONDITION_SESSION_KEY, $this->sp_ids_array);

        $this->saveSegmentActionLog(SegmentActionLog::TYPE_ACTION_MESSAGE);

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_customize_skeleton',array(),array('type' => 2));
    }
}