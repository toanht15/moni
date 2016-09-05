<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
require_once('vendor/codebird-php/src/codebird.php');
AAFW::import('jp.aainc.classes.CacheManager');
class api_renew_twitter_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_renew_twitter_entry';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

	public $NeedAdminLogin = true;
 	public $CsrfProtect = true;
    public $logger;

	public function beforeValidate () {
		$this->Data['service'] = $this->createService('TwitterStreamService');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

	public function validate () {

        $this->brand = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) {
            $this->logger->error("api_renew_twitter_entry@validate 不正brandSocialAccountId");
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => "other error"));
            $this->assign('json_data', $json_data);
            return false;
        }
        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_TWITTER, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->entryId)){
            $this->logger->error("api_renew_twitter_entry@validate 不正entryId");
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => "other error"));
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
		
		$client = $this->initClient();
		$client->setToken($brand_social_account->token, $brand_social_account->token_secret);
				
		$entry = $this->Data['service']->getEntryById($this->entryId);
		$params = $this->createParams($entry->object_id);
		
		try {
			$response = $client->statuses_show_ID($params);
			
		} catch (Exception $e) {
            $this->logger->error("client statuses_show_ID error");
			$this->logger->error($e);
            try {
                $client->setAccessToken(null);
                $response = $client->api($params, 'GET');
            }catch(Exception $ex) {
                $this->logger->error("client null accessToken error");
                $this->logger->error($ex);

                $json_data = $this->createAjaxResponse("ng", array(), array("message" => $e->getMessage()));
                $this->assign('json_data', $json_data);
                return 'dummy.php';
            }
		}
		if($response){
            $err_mess = $service->getErrorMessage($brand_social_account, $response);
            if ($err_mess) {
                $this->logger->error('api_renew_twitter_entry@doAction err_message = ' . $err_mess . ' $brand_social_account_id='.$brand_social_account->id);
                $json_data = $this->createAjaxResponse('ng', array(), array('message' => $err_mess));
                $this->assign('json_data', $json_data);
                return 'dummy.php';
            }

			try {
				$this->Data['service']->renewEntry($entry, $response);

			} catch (Exception $e) {
				$this->logger->error("api_renew_twitter_entry#doAction() error" . "entry_id=" . $entry->id);
				$this->logger->error($e);

                $json_data = $this->createAjaxResponse("ng", array(), array("message" => $e->getMessage()));
                $this->assign('json_data', $json_data);
                return 'dummy.php';
			}
		}
        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
	}
	
	private function initClient() {
		\Codebird\Codebird::setConsumerKey(
			$this->config->query('@twitter.Admin.ConsumerKey'),
			$this->config->query('@twitter.Admin.ConsumerSecret')
		);

		$client = \Codebird\Codebird::getInstance();
		$client->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
		return $client;
	}
	
	private function createParams($postId) {
		$params = array(
				'id' => $postId,
		);
		return $params;
	}
}