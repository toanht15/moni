<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class photo_campaign extends BrandcoGETActionBase {
    protected $ContainerName = 'photo_campaign';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data['action_id'] = $this->GET['exts'][0];
    }

    public function validate() {
        $photo_cp_validator = new CpDataManagerValidator($this->getBrand()->id, $this->Data['action_id'], CpAction::TYPE_PHOTO);

        if (!$photo_cp_validator->validate()) {
            return '404';
        } else {
            $page_data = $photo_cp_validator->getCpActionInfo();
            $page_data['brand_id'] = $this->brand->_Values['id'];
            $this->Data['pageData'] = $page_data;
        }
        return true;
    }

    public function doAction() {
        $search_condition_session = $this->getSearchConditionSession($this->Data['pageData']['cp_id']);

        //投稿写真の更新だったら、絞り込みの状態をそのままにする
        if ($this->GET['mid'] && $search_condition_session['search_photo_campaign' . '/' . $this->Data['action_id']]) {
            $this->Data['pageData'] = $search_condition_session['search_photo_campaign' . '/' . $this->Data['action_id']];
        } else if ($search_condition_session['search_photo_campaign' . '/' . $this->Data['action_id']]) {
            unset($search_condition_session['search_photo_campaign' . '/' . $this->Data['action_id']]);
            $this->setSearchConditionSession($this->Data['pageData']['cp_id'], $search_condition_session);
        }

        return 'user/brandco/admin-cp/photo_campaign.php';
    }
}