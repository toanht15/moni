<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
require_once('vendor/codebird-php/src/codebird.php');

define('CODEBIRD_RETURNFORMAT_OBJECT', 0);
define('CODEBIRD_RETURNFORMAT_ARRAY', 1);

class api_get_twitter_user_timeline extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_get_twitter_user_timeline';
    protected $AllowContent = array('JSON');
    public $NeedOption = array();

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    private $brandSocialAccountId;

    const GET_COUNT = 200;

    public function beforeValidate () {
    }

    public function validate () {

        $this->brandSocialAccountId = $this->GET['exts'][0];

        $this->brand = $this->getBrand();
        if(!$this->brand) {
            aafwLog4phpLogger::getDefaultLogger()->error("api_get_twitter_user_timeline@validate brandが取れなかったです。");
            $json_data = $this->createAjaxResponse("ng", array(), array('message' => 'other error'));
            $this->assign('json_data', $json_data);
            return false;
        }
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) {
            aafwLog4phpLogger::getDefaultLogger()->error("api_get_twitter_user_timeline@validate 不明操作");
            $json_data = $this->createAjaxResponse("ng", array(), array('message' => 'other error'));
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction() {
        try {
            $crawler_service = $this->createService("CrawlerService");
            /** @var BrandSocialAccountService $brandService */
            $brandService = $this->createService('BrandSocialAccountService');
            $stream_service = $this->createService("TwitterStreamService");

            $brand_social_account = $brandService->getBrandSocialAccountById($this->brandSocialAccountId);

            $stream = $brand_social_account->getTwitterStream();

            $crawler_url = $crawler_service->getCrawlerUrlByTargetId("twitter_stream_".$stream->id);

            $client = $this->initClient();

            $client->setToken($brand_social_account->token, $brand_social_account->token_secret);

            $params = $this->createParams($brand_social_account, $crawler_url);
            $response = $client->statuses_userTimeline($params);

            $err_mess = $brandService->getErrorMessage($brand_social_account, $response);
            if ($err_mess) {
                aafwLog4phpLogger::getDefaultLogger()->error('api_get_twitter_user_timeline@doAction err_message = ' . $err_mess . ' $brand_social_account_id='.$brand_social_account->id);
                $json_data = $this->createAjaxResponse('ng', array(), array('message' => $err_mess));
                $this->assign('json_data', $json_data);
                return 'dummy.php';
            }

            $stream_service->doStore($stream, $crawler_url, $response, 'pub_date');

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error("api_get_twitter_user_timeline#doAction() Exception crawler_url_id = " . $crawler_url->id);
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            $json_data = $this->createAjaxResponse("ng", array(), array("message" => $e->getMessage()));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    /**
     * @return \Codebird\Codebird
     */
    private function initClient() {
        \Codebird\Codebird::setConsumerKey(
            $this->config->query('@twitter.Admin.ConsumerKey'),
            $this->config->query('@twitter.Admin.ConsumerSecret')
        );

        $client = \Codebird\Codebird::getInstance();
        $client->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
        return $client;
    }

    /**
     * @param $brand_social_account
     * @param $crawler_url
     * @return array
     */
    private function createParams($brand_social_account, $crawler_url) {
        if (!$crawler_url->url) {
            $params = array(
                'user_id' => $brand_social_account->social_media_account_id,
                'count' => self::GET_COUNT
            );
        } else {
            $query = explode("?", $crawler_url->url);
            $tmp_params = array();
            parse_str($query[1], $tmp_params);

            $params = array(
                'user_id' => $brand_social_account->social_media_account_id,
                'count' => self::GET_COUNT
            );

            if (isset($tmp_params["since_id"])) {
                $params["since_id"] = $tmp_params["since_id"];
            }
        }
        return $params;
    }
}