<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.core.UserManager');

class index extends BrandcoGETActionBase {


    protected $ContainerName = 'inquiry';
    protected $logger;
    protected $hipchat_logger;
    public $NeedOption = array();

    public $SkipAgeAuthenticate = true;

    private $inquiry_brand;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    public function validate() {
        $inquiry_validator = new InquiryValidator();
        if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array(
            'brand_id' => $this->getBrand()->id))
        ) {
            $this->logger->error("index#validate inquiry_brand isn't existed. (brand_id = " . $this->getBrand()->id . ", request uri = " . $_SERVER['REQUEST_URI'] . ")");

            return false;
        }

        $this->inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);

        return true;
    }

    public function doAction() {
        $this->Data['pageStatus']['og'] = array(
            'title' => 'お問い合わせ - ' . $this->getBrand()->name,
        );

        if ($this->Data['pageStatus']['userInfo'] && $this->Data['pageStatus']['userInfo']->id) {
            $user_manager = new UserManager($this->Data['pageStatus']['userInfo']);
            $user_info = $user_manager->getUserByQuery($this->Data['pageStatus']['userInfo']->id);
        }

        // フォーム値の設定
        $this->setFormData('operator_type', null);
        $this->setFormData('user_name', $user_info ? $user_info->name : '');
        $this->setFormData('mail_address', $user_info ? $user_info->mailAddress : '');
        $this->setFormData('category', Inquiry::TYPE_DEFAULT);
        $this->setFormData('content');
        $this->setFormData('cp_id', $this->GET['cp_id'] ?: 0);
        $this->setFormData('referer', $this->SERVER['HTTP_REFERER']);

        $this->Data['monipla_flg'] = (InquiryBrand::isMoniplaBrand($this->getBrand()->id)) ? 1 : 0;

        $this->Data['skip_age_authentication'] = $this->isSkipAgeAuthentication();

        return 'user/brandco/inquiry/index.php';
    }

    public function setFormData($key, $default = '') {
        $this->Data[$key] = $this->Data['ActionError'] ? $this->Data['ActionForm'][$key] : $default;
    }
}