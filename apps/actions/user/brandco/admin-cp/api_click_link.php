<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class api_click_link extends BrandcoGETActionBase{

    public $NeedOption = array();

    private $msg_token;
    private $apiClickLinkValidator;

    public function validate() {
        if (!$this->params) {
            return false;
        }

        $this->msg_token = $this->params;
        $this->params = json_decode(base64_decode($this->params), true);

        $this->apiClickLinkValidator = new CpValidator($this->getBrand()->id);
        if (!$this->apiClickLinkValidator->isOwnerOfAction($this->params['cp_action_id'])) {
            return false;
        }

        return true;
    }

    public function doAction() {
        /** @var aafwEntityStoreBase $click_log_model */
        $click_log_model = $this->getModel('ClickEmailLinkLogs');
        try {
            $cp_action = $this->apiClickLinkValidator->cp_action;
            $cp_action_group = $this->apiClickLinkValidator->group;
            $cp = $this->apiClickLinkValidator->_cp;

            $log = $click_log_model->findOne(array('cp_action_id' => $this->params['cp_action_id'], 'user_id' => $this->params['user_id']));
            if ($log) {
                $log->click_count += 1;
            } else {
                $log = $click_log_model->createEmptyObject();
                $log->user_id = $this->params['user_id'];
                $log->cp_action_id = $this->params['cp_action_id'];
                $log->click_count = 1;
                $log->brand_id = $this->brand->id;
                $log->user_agent = $this->SERVER['HTTP_USER_AGENT'];
                $log->remote_ip = Util::getClientIP();
                $log->referer_url = $this->SERVER['HTTP_REFERER'];
                $log->device = Util::isSmartPhone();
                $log->language = $this->SERVER['HTTP_ACCEPT_LANGUAGE'];
            }

            $click_log_model->save($log);

        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }

        if($cp->type == Cp::TYPE_MESSAGE && $cp_action->type == CpAction::TYPE_MESSAGE && $cp_action_group->order_no == 1 && $cp_action->order_no == 1) {
            return 'redirect: ' . Util::rewriteUrl('', 'message', array($cp->id), array('fid' => 'mpetpmsg', 'msg_token' => $this->msg_token));
        } else if ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON || $cp_action_group->order_no > 1) {
            return 'redirect: ' . Util::rewriteUrl('messages', 'thread', array($cp->id), array('fid' => 'mpetpmsg'));
        } else {
            return 'redirect: '.Util::rewriteUrl('', 'campaigns', array($cp->id), array('fid' => 'mpetpmsg'));
        }
    }
}