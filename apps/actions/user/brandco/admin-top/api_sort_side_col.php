<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class api_sort_side_col extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_sort_side_col';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate () {
        if(!$this->order) {
            return false;
        }
        $this->Data['order'] = explode(',', trim($this->order, ','));
        $this->brand = $this->getBrand();

        $rss_stream_validator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_RSS, $this->brand->id);
        $brandco_social_validator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $this->brand->id);

        foreach ($this->Data['order'] as $social_account_id) {
            if ($this->isNumeric($social_account_id)) {
                if (!$brandco_social_validator->isCorrectEntryId($social_account_id)) {
                    $json_data = $this->createAjaxResponse("ng", array(), array('validator_error'));
                    $this->assign('json_data', $json_data);
                    return false;
                };
            } else if ($rss_stream_id = explode(':', $social_account_id)[1]) {
                if (!$rss_stream_validator->isOwner($rss_stream_id)) {
                    $json_data = $this->createAjaxResponse("ng", array(), array('validator_error'));
                    $this->assign('json_data', $json_data);
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    function doAction() {

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->createService('BrandSocialAccountService');
        /** @var RssStreamService $rss_stream_service */
        $rss_stream_service = $this->createService('RssStreamService');

        $i=1;
        foreach ($this->Data['order'] as $social_account_id) {
            if ($this->isNumeric($social_account_id)) {
                $brand_social_account_service->updateOrder($social_account_id, $i++);
            } else if ($rss_stream_id = explode(':', $social_account_id)[1]) {
                $rss_stream_service->updateOrder($rss_stream_id, $i++);
            }
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}