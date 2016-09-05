<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class instagram_hashtags extends BrandcoGETActionBase {
    protected $ContainerName = 'instagram_hashtags';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data['action_id'] = $this->GET['exts'][0];
    }

    public function validate() {
        $validator = new CpDataManagerValidator($this->getBrand()->id, $this->Data['action_id'], CpAction::TYPE_INSTAGRAM_HASHTAG);

        if (!$validator->validate()) {
            return '404';
        } else {
            $this->Data['pageData'] = $validator->getCpActionInfo();
        }
        return true;
    }

    public function doAction() {
        $search_condition_session = $this->getSearchConditionSession($this->Data['pageData']['cp_id']);

        //Instagram投稿の更新だったら、絞り込みの状態をそのままにする
        if ($this->GET['mid'] && $search_condition_session['search_instagram_hashtag_campaign' . '/' . $this->Data['action_id']]) {
            $this->Data['pageData'] = $search_condition_session['search_instagram_hashtag_campaign' . '/' . $this->Data['action_id']];
        } else if ($search_condition_session['search_instagram_hashtag_campaign' . '/' . $this->Data['action_id']]) {
            unset($search_condition_session['search_instagram_hashtag_campaign' . '/' . $this->Data['action_id']]);
            $this->setSearchConditionSession($this->Data['pageData']['cp_id'], $search_condition_session);
        }

        return 'user/brandco/admin-cp/instagram_hashtags.php';
    }
}