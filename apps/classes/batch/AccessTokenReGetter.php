<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.services.ApplicationService');

class AccessTokenReGetter extends BrandcoBatchBase {

    private $service;

    private $brandco_auth_service;

    public function executeProcess() {
        // queueから値取得
        $this->service = new aafwServiceFactory();

        /** @var BrandcoAuthService $brandco_auth_service */
        $this->brandco_auth_service = $this->service->create('BrandcoAuthService');

        /** @var MultiPostSnsQueueService $multi_post_sns_queue_service */
        $multi_post_sns_queue_service = $this->service->create('MultiPostSnsQueueService');

        $multi_post_sns_queues = $multi_post_sns_queue_service->getErrorMultiPostSnsQueues();

        /** @var SocialAccounts $social_accounts_store */
        $social_accounts_store = aafwEntityStoreFactory::create('SocialAccounts');

        $users_store = aafwEntityStoreFactory::create('Users');

        /** @var UserApplicationService $user_application_service */
        $user_application_service = $this->service->create('UserApplicationService');

        try{
            foreach ($multi_post_sns_queues as $multi_post_sns_queue) {

                // social_account取得
                $filter = array(
                    'conditions' => array(
                        'social_media_account_id' => $multi_post_sns_queue->social_account_id
                    ),
                    'order' => array(
                        'name' => 'id',
                        'direction' => 'desc',
                    ),
                );
                $social_account = $social_accounts_store->findOne($filter);

                // user_id取得
                $user = $users_store->findOne(array('id' => $social_account->user_id));

                // user_application取得
                $user_application = $user_application_service->getUserApplicationByUserIdAndAppId($user->id, ApplicationService::DOMAIN_MAPPING_KOSE);
                $refresh_token_result = $this->getRefreshToken($user_application, ApplicationService::DOMAIN_MAPPING_KOSE);

                if (!$refresh_token_result) {

                    $user_application = $user_application_service->getUserApplicationByUserIdAndAppId($user->id, ApplicationService::BRANDCO);
                    $refresh_token_result = $this->getRefreshToken($user_application, ApplicationService::BRANDCO);

                    if (!$refresh_token_result) {
                        $user_application = $user_application_service->getUserApplicationByUserIdAndAppId($user->id, ApplicationService::MONIPLA);
                        $refresh_token_result = $this->getRefreshToken($user_application, ApplicationService::MONIPLA);
                    }
                }

                if ($refresh_token_result) {

                    // アクセストークン取得
                    $sns_access_token_result = $this->brandco_auth_service->getSNSAccessToken($refresh_token_result, SocialAccount::$socialMediaTypeName[$multi_post_sns_queue->social_media_type]);

                    if ($sns_access_token_result->result->status === Thrift_APIStatus::SUCCESS) {
                        if ($sns_access_token_result->socialAccessToken->snsAccessToken) {
                            $multi_post_sns_queue->access_token = $sns_access_token_result->socialAccessToken->snsAccessToken;
                        }

                        if ($sns_access_token_result->socialAccessToken->snsRefreshToken) {
                            $multi_post_sns_queue->access_refresh_token = $sns_access_token_result->socialAccessToken->snsRefreshToken;
                        }
                    }else {
                        $this->logger->error('get access token error.');
                        $this->logger->error($refresh_token_result->result->errors);
                    }
                } else {
                    $this->logger->error('get refresh token error.');
                    $this->logger->error($refresh_token_result->result->errors);
                }

                $multi_post_sns_queue_service->update($multi_post_sns_queue);
            }
        }catch(Exception $e) {
            $this->logger->error('AccessTokenReGetter error.');
            $this->logger->error($e);
        }

    }

    private function getRefreshToken($user_application, $application_service_type) {
        try {
            // refresh_token取得
            $master = ApplicationService::$ApplicationMaster[$application_service_type];
            $refresh_token_result = $this->brandco_auth_service->refreshAccessToken($user_application->refresh_token, $master['client_id']);
            if ($refresh_token_result->accessToken) {
                return $refresh_token_result->accessToken;
            }else{
                return false;
            }
        }catch (Exception $e) {
            $this->logger->error('getRefreshToken error.');
            $this->logger->error($e);
            return false;
        }
    }
}
