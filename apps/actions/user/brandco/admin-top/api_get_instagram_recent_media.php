<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class api_get_instagram_recent_media extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_get_instagram_recent_media';
    protected $AllowContent = array('JSON');

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    private $brandSocialAccountId;
    private $logger;

    public function doThisFirst() {

        $this->brandSocialAccountId = $this->GET['exts'][0];

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->config = aafwApplicationConfig::getInstance();
    }

    public function validate () {

        $this->brand = $this->getBrand();
        if(!$this->brand) return false;

        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);
        if(!$idValidator->isCorrectEntryId($this->brandSocialAccountId)) return false;

        return true;
    }

    function doAction() {
        try {

            $crawler_service = $this->createService("CrawlerService");
            /** @var BrandSocialAccountService $brandService */
            $brandService = $this->createService('BrandSocialAccountService');
            $stream_service = $this->createService("InstagramStreamService");

            $brand_social_account = $brandService->getBrandSocialAccountById($this->brandSocialAccountId);
            $stream = $brand_social_account->getInstagramStream();

            $crawler_url = $crawler_service->getCrawlerUrlByTargetId("instagram_stream_".$stream->id);

            $instagram = new Instagram();

            $response = $instagram->getRecentMedia($brand_social_account->social_media_account_id, $brand_social_account->token);

            if (!$response || $err_mess = $brandService->getErrorMessage($brand_social_account, $response)) {
                $this->logger->error('api_get_instagram_recent_media@doAction err_message = ' . $err_mess . ' $brand_social_account_id=' . $brand_social_account->id);
                $this->logger->error($response);
                $json_data = $this->createAjaxResponse('ng', array(), array('message' => $err_mess));
                $this->assign('json_data', $json_data);
                return 'dummy.php';
            }

            $stream_service->doStore($stream, $crawler_url, $response, 'pub_date', true);

            $instagram_entry_count = $stream_service->getEntriesCountByStreamIds($stream->id);

            if ($instagram_entry_count < InstagramEntry::INIT_CRAWL_COUNT) {

                while ($response->pagination->next_url) {

                    $response = $instagram->executeGETRequest($response->pagination->next_url);

                    if (!$response || $err_mess = $brandService->getErrorMessage($brand_social_account, $response)) {
                        $this->logger->error('api_get_instagram_recent_media@doAction err_message = ' . $err_mess . ' $brand_social_account_id=' . $brand_social_account->id);
                        $this->logger->error($response);
                        $json_data = $this->createAjaxResponse('ng', array(), array('message' => $err_mess));
                        $this->assign('json_data', $json_data);
                        return 'dummy.php';
                    }

                    $stream_service->doStore($stream, $crawler_url, $response, 'pub_date', true);

                    $instagram_entry_count = $stream_service->getEntriesCountByStreamIds($stream->id);

                    if ($instagram_entry_count >= InstagramEntry::INIT_CRAWL_COUNT) {
                        break;
                    }
                }
            }

        } catch (Exception $e) {
            $this->logger->error("api_get_instagram_user_timeline#doAction() Exception crawler_url_id = " . $crawler_url->id);
            $this->logger->error($e);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}