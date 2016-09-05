<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

abstract class CpInstagramHashtagManagerActionBase extends BrandcoPOSTActionBase {
    protected $ContainerName = 'instagram_hashtags';

    protected $instagram_hashtag_approval_status;

    protected $logger;
    /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
    protected $instagram_hashtag_user_service;
    protected $instagram_hashtag_transaction;

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->instagram_hashtag_user_service = $this->createService('InstagramHashtagUserService');
    }

    public function validate() {
        return true;
    }

    /**
     * 後にトップパネル処理とキャッシュ処理が追加される可能性あり
     * @param $instagram_hashtag_user_post_id
     */
    public function updateInstagramHashtagCampaign($instagram_hashtag_user_post_id) {
        $instagram_hashtag_user_post = $this->instagram_hashtag_user_service->getInstagramHashtagUserPostById($instagram_hashtag_user_post_id);

        if ($instagram_hashtag_user_post->approval_status != $this->instagram_hashtag_approval_status) {
            $instagram_hashtag_user_post->approval_status = $this->instagram_hashtag_approval_status;
            $this->instagram_hashtag_user_service->saveInstagramHashtagUserPost($instagram_hashtag_user_post);
        }
    }
}