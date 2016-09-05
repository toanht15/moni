<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.cp_instagram_hashtags.InstagramHashtagCrawlVerificater');

class VerifyCpInstagramHashtagPostRecentMedia extends BrandcoBatchBase {

    protected $data_builder;
    protected $total_api_call_count = 0;
    /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
    protected $instagram_hashtag_user_service;
    protected $instagram_hashtag_crawl_verificater;

    protected $access_token_queue;
    protected $current_token;

    const MAX_API_CALL = 4000;

    public function __construct($argv = null) {
        parent::__construct($argv);
        $this->data_builder = new aafwDataBuilder();
        $this->instagram_hashtag_user_service = $this->service_factory->create('InstagramHashtagUserService');
        $this->instagram_hashtag_crawl_verificater = new InstagramHashtagCrawlVerificater();
        $this->access_token_queue = $this->loadAccessTokens();
    }

    public function executeProcess() {

        if($this->argv['action_id'] && $this->argv['action_id'] != ''){
            $this->getIntagramHashtagByCpActionId($this->argv['action_id']);
        }else{
            $this->getIntagramHashtagByCpActions();
        }

        if ($this->total_api_call_count) {
            $this->logger->info('VerifyCpInstagramHashtagPostRecentMedia#executeProcess().Total api call count:' . $this->total_api_call_count);
        }
    }

    private function getIntagramHashtagByCpActionId($cpActionId){
        if(!$cpActionId) return;

        $cpFlowService = $this->service_factory->create('CpFlowService');

        $action = $cpFlowService->getCpActionById($cpActionId);
        if(!$action) return;
        
        $cp = $cpFlowService->getCpByCpAction($action);

        // 重複ユーザ名にフラグを立てる
        $this->instagram_hashtag_user_service->executeDuplicateInstagramHashtagUserByCpActionId($cpActionId);

        $this->instagram_hashtag_crawl_verificater->build($cpActionId, $cp, $this->getCurrentToken());

        $this->instagram_hashtag_crawl_verificater->verify();

        $this->total_api_call_count += $this->instagram_hashtag_crawl_verificater->getApiCallCount();

    }

    private function getIntagramHashtagByCpActions(){

        $conditions = array(
            'status' => array(Cp::STATUS_FIX, Cp::STATUS_DEMO),
            'announce_date' => date('Y/m/d H:i:s', strtotime('-3 month')), // 当選発表後3ヶ月後まで
            'module_type' => array(CpAction::TYPE_INSTAGRAM_HASHTAG),
            '__NOFETCH__' => true,
        );

        $rs = $this->data_builder->getCpActionsByCpModuleType($conditions, array(), array(), false, 'CpAction');

        while ($cp_action = $this->data_builder->fetch($rs)) {

            if (!$cp_action->id) return;

            try{

                $cp = aafwEntityStoreFactory::create('Cps')->findOne($cp_action->cp_id);

                // 重複ユーザ名にフラグを立てる
                $this->instagram_hashtag_user_service->executeDuplicateInstagramHashtagUserByCpActionId($cp_action->id);

                $this->instagram_hashtag_crawl_verificater->build($cp_action->id, $cp, $this->getCurrentToken());
                $this->instagram_hashtag_crawl_verificater->verify();

                $this->total_api_call_count += $this->instagram_hashtag_crawl_verificater->getApiCallCount();

                if($this->total_api_call_count > self::MAX_API_CALL) {
                    $this->logger->warn('監視用通知。Instagramのaccess_tokenがexchangeされました。');
                    $this->fetchNewAccessToken();
                }

            }catch (Exception $e) {
                // ここのエラーログは不要
                continue;
            }
        }
    }

    private function loadAccessTokens(){
        $accessTokens = aafwApplicationConfig::getInstance()->query('@instagram.AccessTokenForCrawl');
        return $accessTokens;
    }

    private function fetchNewAccessToken(){

        if(!$this->access_token_queue || !is_array($this->access_token_queue)){
            return null;
        }

        $token = array_shift($this->access_token_queue);
        array_push($this->access_token_queue, $token);

        // Reset Total API Call count
        $this->total_api_call_count = 0;
        $this->current_token = $token;
    }

    private function getCurrentToken() {
        if (!$this->current_token) {
            $this->fetchNewAccessToken();
        }

        return $this->current_token;
    }
}
