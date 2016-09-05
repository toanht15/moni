<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.CacheManager');

class CheckInquiryStatusClosed {

    const MAIL_ALERT_TO_CLIENT = 1;
    const MAIL_ALERT_TO_MANAGER = 2;
    const MAIL_ALERT_DANGER_TO_MANAGER = 3;

    public $logger;
    public $hipchat_logger;

    /** @var aafwServiceFactory $service_factory */
    private $service_factory;
    /** @var InquiryService $inquiry_service */
    private $inquiry_service;
    /** @var InquiryBrandService $inquiry_brand_service */
    private $inquiry_brand_service;
    /** @var InquiryMailService $inquiry_mail_service */
    private $inquiry_mail_service;
    /** @var BrandService $brand_service */
    private $brand_service;
    /** @var ConsultantsManagerService $consultants_manager_service */
    private $consultants_manager_service;
    /** @var SalesManagerService $sales_manager_service */
    private $sales_manager_service;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        $this->service_factory = new aafwServiceFactory();

        $this->inquiry_service = $this->service_factory->create('InquiryService');
        $this->inquiry_brand_service = $this->service_factory->create('InquiryBrandService');
        $this->inquiry_mail_service = $this->service_factory->create('InquiryMailService');
        $this->brand_service = $this->service_factory->create('BrandService');
        $this->consultants_manager_service = $this->service_factory->create('ConsultantsManagerService');
        $this->sales_manager_service = $this->service_factory->create('SalesManagerService');
    }

    public function doProcess($argv) {
        if (count($argv) !== 2) {
            $msg = "mail_type must be specified!";
            $this->hipchat_logger->error($msg);
            throw new aafwException($msg);
        }

        $mail_type = (int) $argv[1];
        $inquiry_list = $this->inquiry_service->getInquiryList(InquiryRoom::TYPE_ADMIN, -1, array(
            'status' => array(InquiryRoom::STATUS_OPEN),
            'period_flg' => 1,
            'date_end' => $this->getDateEnd($mail_type)
        ));

        if ($mail_type === self::MAIL_ALERT_TO_CLIENT) {
            $this->sendMailAlertToClient($inquiry_list);
        } else {
            $this->sendMailAlertToManager($inquiry_list, $mail_type);
        }
    }

    /**
     * @param $inquiry_list
     */
    private function sendMailAlertToClient($inquiry_list) {
        foreach ($inquiry_list as $inquiry) {
            $inquiry_room = $this->inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRY_ROOMS, array('id' => $inquiry['id']));
            $inquiry_brand = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('id' => $inquiry_room->inquiry_brand_id));

            $brand = $this->brand_service->getBrandById($inquiry_brand->brand_id);

            $this->inquiry_mail_service->sendLater($this->inquiry_mail_service->getAllToAddressList($inquiry_brand),
                InquiryMailService::TEMPLATE_ALERT_NOT_CLOSED,
                array(
                    'ENTERPRISE_NAME' => $brand->enterprise_name,
                    'URL' => $this->inquiry_mail_service->generateUrl(InquiryRoom::TYPE_ADMIN, $brand->directory_name, array(InquiryRoom::getDir(InquiryRoom::TYPE_ADMIN), 'show_inquiry_list'))
                )
            );
        }
    }

    /**
     * @param $inquiry_list
     * @param $mail_type
     * @return bool
     */
    private function sendMailAlertToManager($inquiry_list, $mail_type) {
        $subject = '';
        $message = '';
        if ($mail_type === self::MAIL_ALERT_TO_MANAGER) {
            $subject = '対応が必要な企業の問い合わせ状況報告です';
            $message = '本日' . date('H') . '時の問い合わせチェックが完了いたしました。' . PHP_EOL . 'ご確認の上、ご対応をお願いいたします。';
        } else if ($inquiry_list) {
            $subject = '先ほどのアラートメールの内容に未対応です';
            $message = '本日10時のチェックで報告した問い合わせに、未対応のものが残っています。' . PHP_EOL . '担当者は早急にご対応を、周りの方はお声がけをお願いいたします。';
        } else {
            return false;
        }

        // ブランド情報の作成
        $brand_info = $this->getBrandInfo($inquiry_list);

        // 送信先の設定
        $to_address_list = array();
        $to_address_list[] = $this->inquiry_mail_service->getProductToAddress();
        $to_address_list[] = $this->inquiry_mail_service->getAccountToAddress();

        $this->inquiry_mail_service->setFromAddress('問い合わせ管理システム「ジョニー」<info@monipla.com>');
        $this->inquiry_mail_service->sendLater($to_address_list,
            InquiryMailService::TEMPLATE_ALERT_TO_MANAGER,
            array(
                'DATETIME' => date('m/d H:i'),
                'SUBJECT' => $subject,
                'MESSAGE' => $message,
                'BRAND_INFO' => $brand_info ?: 'ご報告すべき未対応の問い合わせはありませんでした。',
            )
        );
    }

    /**
     * @param $inquiry_list
     * @return string
     */
    private function getBrandInfo($inquiry_list) {
        // ブランドリストの作成
        $brand_list = array();
        foreach ($inquiry_list as $inquiry) {
            $inquiry_room = $this->inquiry_service->getRecord(InquiryService::MODEL_TYPE_INQUIRY_ROOMS, array('id' => $inquiry['id']));
            $inquiry_brand = $this->inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('id' => $inquiry_room->inquiry_brand_id));

            $brand = $this->brand_service->getBrandById($inquiry_brand->brand_id);
            $brand_list[$brand->id] = array(
                'name' => $brand->name,
                'url' => $brand->getUrl() . 'admin-inquiry/show_inquiry_list',
            );
        }

        $brand_info = '';
        foreach ($brand_list as $brand_id => $brand) {
            $consultants_manager = $this->inquiry_mail_service->getConsultantsManager($brand_id);
            $sales_manager = $this->inquiry_mail_service->getSalesManager($brand_id);
            $brand_info .= <<<EOS
{$brand['name']}
{$brand['url']}
運用: {$consultants_manager->name} 営業: {$sales_manager->name}


EOS;
        }

        return $brand_info;
    }

    /**
     * @param $mail_type
     * @return bool|string
     * @throws aafwException
     */
    private function getDateEnd($mail_type) {
        switch ($mail_type) {
            case $mail_type === self::MAIL_ALERT_TO_CLIENT:
                $date = date('Y/m/d H:i:s', strtotime('-1 day'));
                break;
            case $mail_type === self::MAIL_ALERT_TO_MANAGER:
                $date = date('Y/m/d H:i:s');
                break;
            case $mail_type === self::MAIL_ALERT_DANGER_TO_MANAGER:
                $date = date('Y/m/d H:i:s', strtotime('-2 hours'));
                break;
            default:
                $msg = "Error: undefined mail_type = " . $mail_type;
                $this->hipchat_logger->error($msg);
                throw new aafwException($msg);
                break;
        }

        return $date;
    }
}
