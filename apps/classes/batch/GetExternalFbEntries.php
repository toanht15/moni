<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.ApplicationService');

class GetExternalFbEntries
{
    const LIMIT_EXECUTE_RECORD = 100;
    const FACEBOOK_API_LIMIT_RECORD = 100;

    protected $service_factory;
    protected $facebookApiClient;
    protected $logger;
    protected $facebookUserTest;

    public function __construct()
    {
        $config = aafwApplicationConfig::getInstance();
        $this->facebookUserTest = array(
            'userId' => $config->query('@facebook.AaIdFacebookTest.userId')
        );

        $this->service_factory = new aafwServiceFactory();
        $this->facebookApiClient = new FacebookApiClient(FacebookApiClient::BRANDCO_MODE_USER);
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function doProcess()
    {
        /** @var  $external_fb_stream_service */
        $external_fb_stream_service = $this->service_factory->create('ExternalFbStreamService');
        $listStreams = $external_fb_stream_service->getAllStreams();

        try {
            //テストユーザーのAccessTokenを取得する
            $accessToken = $this->getFbUserAccessToken($this->facebookUserTest['userId']);
            if(!$accessToken){
                throw new aafwException('GetExternalFbEntries Get Facebook Access Token failed! ');
            }

            $this->facebookApiClient->setToken($accessToken);
        } catch (Exception $e) {
            $this->logger->error('GetExternalFbEntries Set Facebook AccessToken Error!');
            $this->logger->error($e);
            return;
        }

        foreach ($listStreams as $stream) {
            if ($stream['url'] == null) {
                $nextUrl = null;
                while (1) {
                    $check = $this->getExistFacebookPosts($stream['social_media_account_id'], $nextUrl);
                    if (!$check) break;

                    list($listStreamPosts, $nextUrl) = $this->getExistFacebookPosts($stream['social_media_account_id'], $nextUrl);
                    if (count($listStreamPosts) > 0) {
                        $this->saveListPostsToDB($listStreamPosts, $stream);
                    }
                    if (count($listStreamPosts) < self::FACEBOOK_API_LIMIT_RECORD) {
                        $check = $this->getNewFacebookPostByPreviousUrl($stream['social_media_account_id'], $stream['url']);
                        if (!$check) break;

                        list($listStreamPosts, $previousUrl) = $this->getNewFacebookPostByPreviousUrl($stream['social_media_account_id'], $stream['url']);
                        $external_fb_stream_service->updateUrl($stream['id'], $previousUrl);
                        break;
                    }
                }
                continue;
            } else {
                $check = $this->getNewFacebookPostByPreviousUrl($stream['social_media_account_id'], $stream['url']);
                if (!$check) continue;

                list($listStreamPosts, $previousUrl) = $this->getNewFacebookPostByPreviousUrl($stream['social_media_account_id'], $stream['url']);

                if (count($listStreamPosts) > 0) {
                    $this->saveListPostsToDB($listStreamPosts, $stream);
                }
                if ($previousUrl != null) $external_fb_stream_service->updateUrl($stream['id'], $previousUrl);
            }
        }
    }

    /**
     * @param $user_id
     * @return null
     */
    private function getFbUserAccessToken($user_id)
    {
        $data = null;

        if (!$user_id) {
            return $data;
        }

        /** @var UserApplicationService $user_application_service */
        $user_application_service = $this->service_factory->create('UserApplicationService');
        $user_application = $user_application_service->getUserApplicationByUserIdAndAppId($user_id, ApplicationService::BRANDCO);

        if (!$user_application->access_token || !$user_application->refresh_token || !$user_application->client_id) {
            return $data;
        }

        /** @var BrandcoAuthService $brandco_auth_service */
        $brandco_auth_service = $this->service_factory->create('BrandcoAuthService');
        $refresh_token_result = $brandco_auth_service->refreshAccessToken($user_application->refresh_token, $user_application->client_id);

        if ($refresh_token_result->result->status === Thrift_APIStatus::SUCCESS) {
            $sns_access_token_result = $brandco_auth_service->getSNSAccessToken($refresh_token_result->accessToken);
            if ($sns_access_token_result->result->status === Thrift_APIStatus::SUCCESS) {
                $data = $sns_access_token_result->socialAccessToken->snsAccessToken;
            }
        }
        return $data;
    }

    /**
     * @param $pageId
     * @param $nextUrl
     * @return array
     */
    public function getExistFacebookPosts($pageId, $nextUrl)
    {
        $limit = self::FACEBOOK_API_LIMIT_RECORD;
        $listPosts = array();

        try {
            if (isset($nextUrl)) {
                $request = "/{$pageId}/posts?fields=type,status_type,id&limit={$limit}&{$nextUrl}";
            } else {
                $request = "/{$pageId}/posts?fields=type,status_type,id&limit={$limit}";
            }
            $response = $this->facebookApiClient->getResponse('GET', $request);
            if (isset($response['paging']->next)) $nextUrl = $this->getNextUrl($response['paging']->next);

            foreach ($response['data'] as $value) {
                $post = array();
                $post['post_id'] = $value->id;
                $post['type'] = $value->type;
                $post['status_type'] = $value->status_type;
                $listPosts[] = $post;
            }
            return array($listPosts, $nextUrl);

        } catch (Exception $e) {
            $this->logger->error('GetExternalFbEntries getFacebookPostsByPageId Error. $pageId = ' . $pageId);
            $this->logger->error($e);
            return false;
        }
    }

    /**
     * Facebookから取得したデータをNextUrlに変換する
     * @param $next
     * @return string
     */
    public function getNextUrl($next)
    {
        $result = '';
        $tmps = explode("&", $next);
        foreach ($tmps as $tmp) {
            if (strpos($tmp, "until") !== false) {
                $result .= $tmp . '&';
            }
            if (strpos($tmp, "paging_token") !== false) {
                $result .= $tmp . '&';
            }
        }
        return $result;
    }

    /**
     * 投稿リストをデータベースに保存する
     * @param $listPosts
     * @param $stream
     */
    public function saveListPostsToDB($listPosts, $stream)
    {
        /** @var  $external_fb_entry_service */
        $external_fb_entry_service = $this->service_factory->create('ExternalFbEntryService');

        foreach ($listPosts as $post) {
            $entry = array();
            $entry['stream_id'] = $stream['id'];
            $entry['post_id'] = $post['post_id'];
            $entry['type'] = $post['type'];
            $entry['status_type'] = $post['status_type'];
            if ($stream['social_media_account_id']) {
                $ids = explode($stream['social_media_account_id'] . "_", $entry['post_id']);
                if (count($ids) >= 2) {
                    $entry['object_id'] = $ids [1];
                }
            }
            $entry['link'] = "http://www.facebook.com/{$stream['social_media_account_id']}/posts/{$entry['object_id']}";

            $external_fb_entry_service->addEntry($entry);
        }
    }

    /**
     * 新しい投稿を更新する
     * previousUrlを更新する
     *
     * @param $pageId
     * @param $previousUrl
     * @return array
     */
    public function getNewFacebookPostByPreviousUrl($pageId, $previousUrl)
    {
        $limit = self::FACEBOOK_API_LIMIT_RECORD;
        $listPosts = array();
        try {
            if ($previousUrl !== null)
                $request = "/{$pageId}/posts?fields=id,type,status_type&limit={$limit}&{$previousUrl}";
            else
                $request = "/{$pageId}/posts?fields=id,type,status_type&limit={$limit}";

            $response = $this->facebookApiClient->getResponse('GET', $request);

            //If has response data
            if (isset($response['data'])) {
                foreach ($response['data'] as $value) {
                    $post = array();
                    $post['post_id'] = $value->id;
                    $post['type'] = $value->type;
                    $post['status_type'] = $value->status_type;
                    $listPosts[] = $post;
                }
                $previousUrl = $this->getPreviousUrl($response['paging']->previous);

                return array($listPosts, $previousUrl);
            } else {
                //Update new previous URL
                $response = $this->facebookApiClient->getResponse('GET', "/{$pageId}/posts?fields=id,type,status_type&limit={$limit}");
                $previousUrl = $this->getPreviousUrl($response['paging']->previous);

                return array($listPosts, $previousUrl);
            }
        } catch (Exception $e) {
            $this->logger->error('GetExternalFbEntries updateFacebookPostByPreviousPage Error. $pageId = ' . $pageId);
            $this->logger->error($e);
            return false;
        }
    }

    /**
     * Facebookから取得したデータをPreviousUrlに変換する
     * @param $previous
     * @return string
     */
    public function getPreviousUrl($previous)
    {
        $result = '';
        $tmps = explode("&", $previous);
        foreach ($tmps as $tmp) {
            if (strpos($tmp, "since=") !== false) {
                $result .= $tmp . '&';
            }
            if (strpos($tmp, "paging_token=") !== false) {
                $result .= $tmp . '&';
            }
            if (strpos($tmp, "previous=") !== false) {
                $result .= $tmp . '&';
            }
        }
        return $result;
    }
}