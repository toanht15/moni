<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class InquiryMailService extends aafwServiceBase {

    const TEMPLATE_OPEN = 'inquiry_open';
    const TEMPLATE_REPLY_FROM_ADMIN = 'inquiry_reply_from_admin';
    const TEMPLATE_REPLY_FROM_MANAGER = 'inquiry_reply_from_manager';
    const TEMPLATE_REPLY_FROM_FORWARDED = 'inquiry_reply_from_forwarded';
    const TEMPLATE_FORWARD_FROM_ADMIN = 'inquiry_forward_from_admin';
    const TEMPLATE_FORWARD_FROM_MANAGER = 'inquiry_forward_from_manager';
    const TEMPLATE_ALERT_CLOSED = 'inquiry_alert_closed';
    const TEMPLATE_ALERT_NOT_CLOSED = 'inquiry_alert_not_closed';
    const TEMPLATE_ALERT_TO_MANAGER = 'inquiry_alert_to_manager';

    /** @var aafwLog4phpLogger $logger */
    protected $logger;
    /** @var aafwLog4phpLogger $hipchat_logger */
    protected $hipchat_logger;

    private $mail_manager;

    public function __construct() {
        $this->mail_manager = new MailManager();

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    /**
     * @param $to_addresses
     * @param $template_name
     * @param array $params
     * @throws aafwException
     */
    public function send($to_addresses, $template_name, $params = array()) {
        if ($to_addresses && !is_array($to_addresses)) {
            $to_addresses = array($to_addresses);
        }

        $this->mail_manager->loadMailContent($template_name);
        foreach ($to_addresses as $to_address) {
            if ($to_address && $this->isMailAddress($to_address)) {
                $this->mail_manager->sendNow($to_address, $params);
            }
        }
    }

    /**
     * @param $to_addresses
     * @param $template_name
     * @param array $params
     * @throws aafwException
     */
    public function sendLater($to_addresses, $template_name, $params = array()) {
        if ($to_addresses && !is_array($to_addresses)) {
            $to_addresses = array($to_addresses);
        }

        $this->mail_manager->loadMailContent($template_name);
        foreach ($to_addresses as $to_address) {
            if ($to_address && $this->isMailAddress($to_address)) {
                $this->mail_manager->sendLater($to_address, $params);
            }
        }
    }

    /**
     * @param $from_address
     */
    public function setFromAddress($from_address) {
        $this->mail_manager->FromAddress = $from_address;
    }

    /**
     * @param $operator_type
     * @param string $directory_name
     * @param array $params
     * @return mixed|string
     */
    public function generateUrl($operator_type, $directory_name = '', $params = array()) {
        $url = config('Protocol.Secure') . '://' .
            (InquiryRoom::isManager($operator_type) ? config('Domain.brandco_manager') : (config('Domain.brandco') . '/' . $directory_name));

        foreach ($params as $param) {
            $url .= '/' . $param;
        }

        return $url;
    }

    /**
     * @param $inquiry_brand
     * @return array
     */
    public function getAllToAddressList($inquiry_brand) {
        $sales_manager = $this->getSalesManager($inquiry_brand->brand_id);
        $consultants_manager = $this->getConsultantsManager($inquiry_brand->brand_id);

        $to_addresses = array();
        $to_addresses[] = $sales_manager ? $sales_manager->mail_address : '';
        $to_addresses[] = $consultants_manager ? $consultants_manager->mail_address : '';
        $to_addresses[] = $this->getProductToAddress();

        $inquiry_brand_receiver_to_addresses = $this->getInquiryBrandReceiverToAddress($inquiry_brand->id);
        foreach ($inquiry_brand_receiver_to_addresses as $inquiry_brand_receiver_to_address) {
            $to_addresses[] = $inquiry_brand_receiver_to_address;
        }

        if (count($to_addresses) === 0) {
            $this->logger->error("InquiryMailService#getAllToAddressList to_address_list is empty (brand_id = " . $inquiry_brand->brand_id . ")");
            $this->hipchat_logger->error("InquiryMailService#getAllToAddressList to_address_list is empty (brand_id = " . $inquiry_brand->brand_id . ")");
        }

        return $to_addresses;
    }

    /**
     * @param $inquiry_brand
     * @return array
     */
    public function getAdminToAddressList($inquiry_brand) {
        $to_addresses = array();
        if ($inquiry_brand->aa_alert_flg) {
            $sales_manager = $this->getSalesManager($inquiry_brand->brand_id);
            $consultants_manager = $this->getConsultantsManager($inquiry_brand->brand_id);

            $to_addresses[] = $sales_manager ? $sales_manager->mail_address : '';
            $to_addresses[] = $consultants_manager ? $consultants_manager->mail_address : '';
        }

        $inquiry_brand_receiver_to_addresses = $this->getInquiryBrandReceiverToAddress($inquiry_brand->id);
        foreach ($inquiry_brand_receiver_to_addresses as $inquiry_brand_receiver_to_address) {
            $to_addresses[] = $inquiry_brand_receiver_to_address;
        }

        if (count($to_addresses) === 0) {
            $this->logger->error("InquiryMailService#getAdminToAddressList to_address_list is empty (brand_id = " . $inquiry_brand->brand_id . ")");
            $this->hipchat_logger->error("InquiryMailService#getAdminToAddressList to_address_list is empty (brand_id = " . $inquiry_brand->brand_id . ")");
        }

        return $to_addresses;
    }

    /**
     * @return array
     */
    public function getManagerToAddressList() {
        return array(Config('Mail.Support'));
    }

    /**
     * @param $brand_id
     * @return null
     */
    public function getSalesManager($brand_id) {
        /** @var SalesManagerService $sales_manager_service */
        $sales_manager_service = $this->getService('SalesManagerService');

        // 引数がnullの場合
        if (!$brand_id) {
            return null;
        }

        // SalesManagerの通知先を取得
        $sales_manager = $sales_manager_service->getSalesManagerInfoByBrandId($brand_id);
        if ($sales_manager->id) {
            $manager_service = $this->getService('ManagerService');
            $manager = $manager_service->getManagerById($sales_manager->sales_manager_id);
            return $manager;
        } else {
            $this->logger->warn("InquiryMailService#getSalesManager SalesManager is not found");
        }

        return null;
    }

    /**
     * @param $brand_id
     * @return null
     */
    public function getConsultantsManager($brand_id) {
        /** @var ConsultantsManagerService $sales_manager_service */
        $consultants_manager_service = $this->getService('ConsultantsManagerService');

        // 引数がnullの場合
        if (!$brand_id) {
            return null;
        }

        // ConsultantsManagerの通知先を取得
        $consultants_manager = $consultants_manager_service->getConsultantsManagerbyBrandId($brand_id);
        if ($consultants_manager->id) {
            $manager_service = $this->getService('ManagerService');
            $manager = $manager_service->getManagerById($consultants_manager->consultants_manager_id);
            return $manager;
        } else {
            $this->logger->warn("InquiryMailService#getConsultantsManager ConsultantsManager is not found");
        }

        return null;
    }

    /**
     * @param $inquiry_brand_id
     * @return array|null
     */
    public function getInquiryBrandReceiverToAddress($inquiry_brand_id) {
        /** @var InquiryBrandService $inquiry_brand_service */
        $inquiry_brand_service = $this->getService('InquiryBrandService');

        // 引数がnullの場合
        if (!$inquiry_brand_id) {
            return null;
        }

        // InquiryBrandReceiverの通知先を習得
        $inquiry_brand_receivers = $inquiry_brand_service->getRecords(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS, array('inquiry_brand_id' => $inquiry_brand_id));
        if ($inquiry_brand_receivers) {
            $to_addresses = array();
            foreach ($inquiry_brand_receivers as $inquiry_brand_receiver) {
                $to_addresses[] = $inquiry_brand_receiver->mail_address;
            }

            return $to_addresses;
        } else {
            $this->logger->error("InquiryMailService#getInquiryBrandReceiverToAddress InquiryBrandReceivers is not found");
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getProductToAddress() {
        return Config('Mail.Product');
    }

    public function getAccountToAddress() {
        return Config('Mail.Account');
    }
}
