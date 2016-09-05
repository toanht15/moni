<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class tweet_posts extends BrandcoGETActionBase {
    protected $ContainerName = 'tweet_posts';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    // TODO SUBWAYのハードコーディングを消す際に、復活させてください
    // public $NeedAdminLogin = true;
    public $NeedRedirect = true;

    public function doThisFirst() {
        $this->Data['action_id'] = $this->GET['exts'][0];
    }

    public function validate() {
        // ツイート管理がクライアントも利用できるハードコーディング（SUBWAY-14）
        if (!$this->isLoginManager() && $this->getBrand()->id != 496) {
            return '403';
        } else {
            if (!$this->isLoginAdmin()) {
                return 'redirect: ' . Util::rewriteUrl('my', 'login');
            }
        }

        $tweet_validator = new CpDataManagerValidator($this->getBrand()->id, $this->Data['action_id'], CpAction::TYPE_TWEET);

        if (!$tweet_validator->validate()) {
            return '404';
        } else {
            $this->Data['pageData'] = $tweet_validator->getCpActionInfo();
        }
        return true;
    }

    public function doAction() {
        $temp_session_data = $this->getBrandSession('tempTweetPostSession');
        if ($temp_session_data && is_array($temp_session_data)) {
            $this->Data['pageData'] = array_merge($this->Data['pageData'], $temp_session_data);
            $this->setBrandSession('tempTweetPostSession', null);
        }

        return 'user/brandco/admin-cp/tweet_posts.php';
    }
}