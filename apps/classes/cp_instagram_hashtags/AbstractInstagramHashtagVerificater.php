<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.cp_instagram_hashtags.InstagramHashtagVeriticateDao');
AAFW::import('jp.aainc.vendor.instagram.Instagram');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

abstract class AbstractInstagramHashtagVerificater extends aafwServiceBase {

    /** @var \InstagramHashtagVeriticateDao $dao */
    protected $dao;

    /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
    protected $cp_instagram_hashtag_action_service;

    /** @var CpInstagramHashtagService $cp_instagram_hashtag_service */
    protected $cp_instagram_hashtag_service;

    /** @var InstagramHashtagUserPostService instagram_hashtag_user_post_service */
    protected $instagram_hashtag_user_post_service;

    /** @var InstagramHashtagUserService instagram_hashtag_user_service */
    protected $instagram_hashtag_user_service;

    /** @var CpInstagramHashtagActions $cp_instagram_transaction */
    protected $cp_instagram_transaction;

    /** @var CpInstagramHashtagStreamService $cp_instagram_hashtag_stream_service */
    protected $cp_instagram_hashtag_stream_service;

    protected $hipchat_logger;

    protected $logger;

    protected $api_call_count = 0;

    public function __construct() {
        $this->instagram = new Instagram();
        $this->data_builder = new aafwDataBuilder();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        $this->cp_instagram_hashtag_action_service = $this->getService('CpInstagramHashtagActionService');
        $this->cp_instagram_hashtag_service = $this->getService('CpInstagramHashtagService');

        $this->instagram_hashtag_user_service = $this->getService('InstagramHashtagUserService');
        $this->instagram_hashtag_user_post_service = $this->getService('InstagramHashtagUserPostService');

        $this->cp_instagram_transaction = aafwEntityStoreFactory::create('CpInstagramHashtagActions');
        $this->cp_instagram_hashtag_stream_service = $this->getService('CpInstagramHashtagStreamService');
    }

    public function build($cp_action_id, $cp, $access_token) {
        if (!$cp_action_id || !$cp) throw new InvalidArgumentException();

        $this->dao = new InstagramHashtagVeriticateDao();

        $this->dao->setCp($cp);

        $this->dao->setCpInstagramHashtagAction($this->cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($cp_action_id));
        if (!$this->dao->getCpInstagramHashtagAction()) throw new Exception();

        $this->dao->setCpInstagramHashtags($this->cp_instagram_hashtag_service->getCpInstagramHashtagsOrderById($this->dao->getCpInstagramHashtagAction()->id));

        $this->dao->setHashtags($this->cp_instagram_hashtag_service->getCpInstagramHashtagsByCpInstagramHashtagAction($this->dao->getCpInstagramHashtagAction()));

        $this->dao->setAccessToken($access_token);

        if (!$this->validate()) throw new Exception();
    }

    public function validate() {
        if (!$this->dao->getCpInstagramHashtagAction()) return false;
        if (!$this->dao->getCpInstagramHashtags()) return false;
        if (!$this->dao->getHashtags()) return false;
        if (!$this->dao->getAccessToken()) return false;
        if (!$this->dao->getCp()) return false;
        return true;
    }

    /**
     * CpInstagramHashtag毎に照合する
     */
    public function verify() {

        foreach ($this->dao->getCpInstagramHashtags() as $cp_instagram_hashtag) {

            try {

                // 更新処理はコミットの直前のみなのでここでトランザクション開始
                $this->cp_instagram_transaction->begin();

                $this->initialize($cp_instagram_hashtag);

                $this->verifyAll();

                $this->saveCpInstagramHashtag($cp_instagram_hashtag);

                $this->cp_instagram_transaction->commit();

            } catch (Exception $e) {
                $this->logger->error('AbstractInstagramHashtagVerificater#verify() Exception.' . $e);
                $this->cp_instagram_transaction->rollback();
            }
        }
    }

    abstract function initialize($cp_instagram_hashtag);

    abstract function verifyAll();

    abstract function saveCpInstagramHashtag($cp_instagram_hashtag);

    /**
     * @return int
     */
    public function getApiCallCount() {
        return $this->api_call_count;
    }

    protected function getTagInfo($cp_instagram_hashtag) {
        if (!$cp_instagram_hashtag) return '';

        $tag_info = $this->instagram->getTagInfo($cp_instagram_hashtag->hashtag, $this->dao->getAccessToken());
        $this->api_call_count++;

        if ($tag_info->meta->code != Instagram::LEGAL_ACCESS_CODE) {
            $this->hipchat_logger->error('CpInstagramHashtagActionService#verify() getTagInfo() Illegal access code:' . $tag_info->meta->code . " cp_instagram_hashtag_id: " . $cp_instagram_hashtag->id);
        }
        return $tag_info;
    }

    public function setAccessToken($access_token){
        $this->dao->setAccessToken($access_token);
    }
}
