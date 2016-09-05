<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CacheManager');
class api_renew_facebook_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_renew_facebook_entry';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    public $logger;

    public function beforeValidate () {
        $this->Data['service'] = $this->createService('FacebookStreamService');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function validate () {

        $this->brand = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) {
            $this->logger->error('api_get_facebook_user_posts@validate 不正brandSocialAccountId');
            $json_data = $this->createAjaxResponse('ng', array(), array('message' => 'other error'));
            $this->assign('json_data', $json_data);
            return false;
        }
        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_FACEBOOK, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->entryId)) {
            $this->logger->error('api_get_facebook_user_posts@validate 不正entryId');
            $json_data = $this->createAjaxResponse('ng', array(), array('message' => 'other error'));
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction() {

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($this->brand->id);

        /** @var BrandSocialAccountService $service */
        $service = $this->createService('BrandSocialAccountService');
        $brand_social_account = $service->getBrandSocialAccountById($this->brandSocialAccountId);

        $entry = $this->Data['service']->getEntryById($this->entryId);
        try {
            $facebook_client = $this->getFacebook();
            $facebook_client->setToken($brand_social_account->token);

            $params = $this->createParams( $entry->post_id );
            $response = $facebook_client->getPostDetail($params);

            if($entry->type == 'photo'){
                $image_params       = "/" . $entry->object_id. "?fields=images";
                $image_response     = $facebook_client->getPostDetail($image_params);
                $image              = $this->Data['service']->getImageUrl($image_response);
                if ($image && $image !== FacebookEntry::FACEBOOK_STAGING) {
                    $response['picture'] = $image;
                } elseif ($response['full_picture']) {
                    $response['picture'] = $response["full_picture"];
                }
            } else if($entry->type == FacebookEntry::ENTRY_TYPE_LINK && $this->Data['service']->getImageUrl($response) == FacebookEntry::FACEBOOK_STAGING) {
                $response['picture'] = $response["full_picture"];
            }

            $this->Data['service']->renewEntry($entry, $response);

        } catch (Exception $e) {
            $this->detailDataUpdateFailure($entry);

            $err_mess = $service->getErrorMessage($brand_social_account, $e);

            $this->logger->error('api_renew_facebook_entry @doAction '.$err_mess.' brand_social_account_id='.$brand_social_account->id);
            $this->logger->error($e);

            $json_data = $this->createAjaxResponse('ng', array(),array('message' => $err_mess));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
    
    private function createParams($postId) {
        $params = "/" . $postId. "?fields=picture,full_picture,from,id,link,message,description,name,created_time,updated_time,status_type";

        return $params;
    }
    
    private function detailDataUpdateFailure($entry) {
        $entry->detail_data_update_error_count = $entry->detail_data_update_error_count + 1;
        $this->Data['service']->updateEntry($entry);
    }
}