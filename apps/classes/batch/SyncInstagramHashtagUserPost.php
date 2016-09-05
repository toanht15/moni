<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class SyncInstagramHashtagUserPost extends BrandcoBatchBase {

    protected $data_builder;
    /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
    protected $instagram_hashtag_user_service;

    public function __construct($argv = null) {
        parent::__construct($argv);
        $this->data_builder = aafwDataBuilder::newBuilder();
        $this->instagram_hashtag_user_service = $this->service_factory->create('InstagramHashtagUserService');
    }

    public function executeProcess() {

        $conditions = array(
            'status' => array(Cp::STATUS_FIX, Cp::STATUS_DEMO),
            'announce_date' => date('Y/m/d H:i:s', strtotime('-3 month')), // 当選発表終了後3ヶ月まで
            'module_type' => array(CpAction::TYPE_INSTAGRAM_HASHTAG),
            '__NOFETCH__' => true,
        );

        $rs = $this->data_builder->getCpActionsByCpModuleType($conditions, array(), array(), false, 'CpAction');

        while ($cp_action = $this->data_builder->fetch($rs)) {

            if (!$cp_action->id) return;

            try{

                $instagram_hastag_user_posts = $this->instagram_hashtag_user_service->getInstagramHashtagUserPostsByCpActionId($cp_action->id);

                if (!$instagram_hastag_user_posts) continue;

                foreach ($instagram_hastag_user_posts as $instagram_hastag_user_post) {
                    $instagram = new Instagram();
                    $response = $instagram->getEmbedMedia($instagram_hastag_user_post->link);
                    if (!$response->html) {
                        $instagram_hastag_user_post->approval_status = InstagramHashtagUserPost::APPROVAL_STATUS_PRIVATE;
                        $this->instagram_hashtag_user_service->saveInstagramHashtagUserPost($instagram_hastag_user_post);
                    } else if ($instagram_hastag_user_post->approval_status == InstagramHashtagUserPost::APPROVAL_STATUS_PRIVATE) {
                        $instagram_hastag_user_post->approval_status = InstagramHashtagUserPost::APPROVAL_STATUS_REJECT;
                        $this->instagram_hashtag_user_service->saveInstagramHashtagUserPost($instagram_hastag_user_post);
                    }
                }

            }catch (Exception $e) {
                $this->logger->error('SyncInstagramHashtagUserPost#executeProcess error.' . $e);
            }
        }
    }
}
