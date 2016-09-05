<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_change_tweet_action_panel_hidden_flg extends BrandcoPOSTActionBase {
    protected $ContainerName = 'tweet_posts';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $cp_tweet_action_service;
    private $cp_tweet_action;

    public function validate() {
        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);

        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            return false;
        }

        $this->cp_tweet_action_service = $this->getService('CpTweetActionService');
        $this->cp_tweet_action = $this->cp_tweet_action_service->getCpTweetAction($this->POST['action_id']);

        if (!$this->cp_tweet_action) {
            return false;
        }

        return true;
    }

    public function doAction() {
        if ($this->cp_tweet_action->panel_hidden_flg != $this->POST['panel_hidden_flg']) {
            $this->cp_tweet_action->panel_hidden_flg = $this->POST['panel_hidden_flg'];
            $this->cp_tweet_action_service->updateCpTweetAction($this->cp_tweet_action);
        }

        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}