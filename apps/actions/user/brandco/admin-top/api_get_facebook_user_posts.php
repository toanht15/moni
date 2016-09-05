<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_get_facebook_user_posts extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_get_facebook_user_posts';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    private $brandSocialAccountId;

    public function beforeValidate() {
        ini_set('max_execution_time', 3600);
    }

    public function validate() {
        $this->brandSocialAccountId = $this->GET['exts'][0];

        $this->brand = $this->getBrand();
        if (!$this->brand) {
            aafwLog4phpLogger::getDefaultLogger()->error("api_get_facebook_user_posts@validate 不正BrandId");
            $json_data = $this->createAjaxResponse('ng', array(), array('message' => 'other error'));
            $this->assign('json_data', $json_data);
            return false;
        }
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if (!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) {
            aafwLog4phpLogger::getDefaultLogger()->error('api_get_facebook_user_posts@validate 不正brandSocialAccountId');
            $json_data = $this->createAjaxResponse('ng', array(), array('message' => 'other error'));
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction() {

        $crawler_service = $this->createService("CrawlerService");
        /** @var BrandSocialAccountService $brandService */
        $brandService = $this->createService('BrandSocialAccountService');
        $stream_service = $this->createService("FacebookStreamService");

        $brand_social_account = $brandService->getBrandSocialAccountById($this->brandSocialAccountId);

        try {

            $stream = $brand_social_account->getFacebookStream();

            $crawler_url = $crawler_service->getCrawlerUrlByTargetId("facebook_stream_" . $stream->id);

            $facebook_client = $this->getFacebook();
            $facebook_client->setToken($brand_social_account->token);

            $params = $this->createParams($brand_social_account);

            $response = $facebook_client->getUserFeed($params);

            //create entry & crawler url
            $stream_service->doStore($stream, $crawler_url, $response, 'pub_date');

            //update detail entry
            $batch_request_array = $facebook_client->createParamForUpdateEntry($stream);
            for ($i = 0; $i < count($batch_request_array); $i++) {
                $requestParams = array();
                $requestParams ['batch'] = $batch_request_array [$i];

                $responses = $facebook_client->getPostsDetail($requestParams);

                $facebook_client->updateFacebookEntries($responses, $stream);
            }
        } catch (Exception $e) {

            $err_mess = $brandService->getErrorMessage($brand_social_account, $e);

            aafwLog4phpLogger::getDefaultLogger()->error("api_get_facebook_user_posts#doAction() Exception crawler_url_id = " . $crawler_url->id);
            aafwLog4phpLogger::getDefaultLogger()->error($e);

            $json_data = $this->createAjaxResponse('ng', array(),array('message' => $err_mess));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    /**
     * @param $brand_social_account
     * @return string
     */
    private function createParams($brand_social_account) {
        return "/" . $brand_social_account->social_media_account_id . "/feed?fields=id,from,link,message,type,picture,status_type,object_id,updated_time,created_time,description,story,name,full_picture,actions,privacy,shares,is_hidden,subscribed,is_expired,likes,comments,story_tags,icon";
    }
}