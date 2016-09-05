<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.preOrder');
AAFW::import('jp.aainc.classes.products.payments.BasePayment');
AAFW::import('jp.aainc.classes.products.payments.convenienceStore');
AAFW::import('jp.aainc.classes.products.payments.credit');
AAFW::import('jp.aainc.classes.products.payments.rakuten');
AAFW::import('jp.aainc.classes.products.payments.BasePayment');
AAFW::import('jp.aainc.classes.products.settlement');
AAFW::import('jp.aainc.classes.services.UserMailService');
AAFW::import('jp.aainc.classes.services.BrandGlobalSettingService');
AAFW::import('jp.aainc.classes.services.MailManager');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');

/**
 * Class paymentStepMail
 * memo: 1件の注文に対し、1件のメールを作るclassなので、毎回newで呼び出す。
 */
class paymentStepMail {
    /**
     * 支払い完了メール送信日保存カラム名
     */
    const Coloum_MAIL_ApplicationCompleted = 'mail_complete_send_date';

    /**
     * キャンセルメール送信日保存カラム名
     */
    const Coloum_MAIL_Cancel = 'mail_cancel_send_date';

    /**
     * リマインドメール
     */
    const Coloum_MAIL_Remind = 'mail_remind_send_date';

    /**
     * 受付完了
     */
    const Coloum_MAIL_Request = 'mail_request_send_date';

    /**
     * users.id
     * @var int
     */
    public $userId = 0;

    /**
     * ユーザのメールアドレス
     * @var string
     */
    private $userMail = '';

    /**
     * ブランドのFROM MAILアドレス
     * @var string
     */
    private $brandFromMail = '';

    /**
     * orders.id
     * @var int
     */
    public $orderId = 0;

    /**
     * orders 1row
     * @var array
     */
    public $order = [];

    /**
     * logger class obj
     * @var Logger|null
     */
    private $logger = null;

    /**
     * aafwDataBuilder obj
     * @var aafwDataBuilder|null
     */
    public $db = null;

    /**
     * config class obj
     * @var array|null
     */
    private $config = [];

    /**
     * mail template
     * @var string
     */
    private $mailTemplateType = '';

    /**
     * ユーザのnick name
     * @var string
     */
    private $userNickName = '';

    /**
     * brands.id
     * @var int
     */
    private $brandId = 0;

    /**
     * brand お問い合わせ先URL
     * @var string
     */
    private $brandInquiryUrl = '';

    /**
     * brands
     * @var array
     */
    private $brandData = [];

    /**
     * mailManager class object
     * @var null
     */
    private $mailManager = null;

    /**
     * メール置き換え用パラメータ
     * @var array
     */
    private $mailParams = [];

    /**
     * cps
     * @var array
     */
    private $cp = [];


    /**
     * paymentStepMail constructor.
     */
    public function __construct() {
        $this->db = new aafwDataBuilder();
        $this->config = aafwApplicationConfig::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->convenienceStoreList = $this->config->query('@gmo.GMO.convenienceStoreList');
        $this->mailManager = new MailManager();
    }

    /**
     * (1) 完了メール
     * @param $orderId
     * @return bool
     */
    public function applicationCompleted($orderId) {
        $this->orderId = $orderId;
        try {
            $this->order = $this->getOrder($orderId);
            $this->setCompletedTemplate();
            $this->settingMail();
            $this->sendNow();
            $this->updateSendMail($orderId, self::Coloum_MAIL_ApplicationCompleted);
            $this->sendOrderCompleteNotificationMail($orderId);
            return true;
        } catch (Exception $e) {
            $this->logger->error('orderId:' . $orderId . ' mail send error.');
            return false;
        }
    }

    /**
     * 完了メールのテンプレート設定。
     */
    private function setCompletedTemplate() {
        $this->mailTemplateType = 'payment/complete';
        if( $this->order['pay_type'] == preOrder::payType_Convenience ) {
            $this->mailTemplateType = 'payment/convenience/complete';
        }
    }

    /**
     * 送信日保存
     * @param int $orderId
     * @param string $column
     */
    private function updateSendMail($orderId = 0, $column = '') {
        $sql = 'UPDATE 
                  orders 
                SET  
                  ' . $column . ' = NOW() 
                WHERE id=' . (int)$orderId . ';';
        $this->db->executeUpdate($sql);

    }

    /**
     * パラメータ等メール送信に必要な処理を実行
     */
    private function settingMail() {
        $this->setUserId();
        $this->setUserData();
        $this->setFromMail();
        $this->getBrandData();
        $this->setBrandMail();
        $this->setbrandInquiryUrl();
        $this->setMailText();
        $this->setMailSubject();
        $this->setParams();
    }


    private function sendLater() {
        if( $this->userMail ) {
            $this->mailManager->sendLater($this->userMail, $this->mailParams);
            $this->userMail = null;
            return;
        }
        $this->logger->error('userMail is null. orderId:' . $this->orderId);
    }

    /**
     * メールの即時送信
     * @throws aafwException
     */
    private function sendNow() {
        if( $this->userMail ) {
            $this->mailManager->sendNow($this->userMail, $this->mailParams);
            $this->userMail = null;
            return;
        }
        $this->logger->error('userMail is null. orderId:' . $this->orderId);
    }

    /**
     * メール本文の設定
     */
    private function setMailText() {
        $this->mailManager->loadBodyPlain($this->mailTemplateType);
    }

    /**
     * メールタイトルの設定
     */
    private function setMailSubject() {
        $templatePath = $this->mailTemplateType;
        $this->mailManager->loadSubject($templatePath);
    }

    /**
     * 変換パラメータの設定
     */
    private function setParams() {
        $this->mailParams['ブランド名'] = $this->brandData['enterprise_name'];
        $this->mailParams['ブランド問い合わせ先'] = $this->brandInquiryUrl; //お問い合わせ先URL
        $this->mailParams['支払い方法'] = $this->getPaymentName($this->order); //支払い方法 コンビニの場合は店舗名 TODO:
        $this->mailParams['支払い日'] = date(
            'Y-m-d',
            strtotime($this->order['payment_completion_date'])
        ); //支払い日 2016/01/01 TODO:
        $this->mailParams['店舗名'] = $this->order['shop_name'];
        $this->mailParams['受付日時'] = $this->order['order_completion_date']; //受付日時 2016/06/27 16:12:53
        $this->mailParams['価格'] = number_format($this->order['total_cost']);//価格
        $this->mailParams['支払期限'] = $this->order['payment_term_date'];//お支払期限
        $this->mailParams['注文内容'] = $this->order['product_title'];//ご注文内容 TODO: products.titleで良いか確認
        $this->mailParams['キャンペーンURL'] = $this->order['cp_reference_url'];//ＵＲＬ***キャンペーンURL***
        $this->mailParams['ニックネーム'] = $this->userNickName; //<#ニックネーム>
        $this->mailParams['キャンペーン名'] = $this->getCpName($this->order);
        $this->mailParams['コンビニエンスストア'] = $this->order['convenience_name'];
        $this->mailParams['決済番号'] = $this->order['payment_receipt_no'];
        $this->mailParams['決済確認番号'] = $this->order['payment_conf_no'];
        $this->mailParams['注文番号'] = $this->order['gmo_payment_order_id'];
    }

    /**
     * キャンペーン名取得
     * @param array $order self::getOrderの値
     * @return string
     */
    public function getCpName($order) {
        $storeFactory = new aafwEntityStoreFactory ();
        $cpStore = $storeFactory->create(Cps::class);
        /** @var Cp $cp */
        $cp = $cpStore->findOne($order['cp_id']);
        if( $title = $cp->getTitle() ) {
            return $title;
        }
        return;
    }

    /**
     * 支払方法の名称を返す
     * @param array $order
     * @return string
     */
    private function getPaymentName($order) {
        if( $order['pay_type'] != settlement::payType_Convenience ) {
            return $order['pay_type_name'];
        }
        return $order['convenience_name'];
    }

    /**
     * FromMailの設定
     * @param array $order
     */
    private function setFromMail() {
        $this->setBrandMail($this->order);
        if( $this->brandFromMail ) {
            $this->mailManager->FromAddress = $this->brandFromMail;
        }
    }

    /**
     * user情報の設定
     * @return null
     */
    private function setUserData() {
        if( $this->userId ) {
            $userData = $this->getUserData();
            $this->userMail = $userData->mailAddress;
            $this->userNickName = $userData->name;
        }
        return null;
    }

    /**
     * ユーザ情報の取得
     * @return null|object UserManager class obj
     */
    public function getUserData() {
        if( !$this->userId ) {
            return null;
        }
        $sql = 'select monipla_user_id from users where id=?id?';
        $param = ['id' => $this->userId];
        $result = $this->db->getBySQL($sql, [$param]);
        if( isset($result[0]['monipla_user_id']) ) {
            $obj = new UserManager();
            return $obj->getUserByQuery($result[0]['monipla_user_id']);
        }
    }

    /**
     * 注文情報の取得
     * @param int $ordedelrId orders.id
     * @return array
     */
    public function getOrder($orderId) {
        $sql = 'SELECT '
            . ' orders.*,
                    products.title as product_title,
                    products.cp_id as cp_id,
                    cps.reference_url as cp_reference_url,
                    brand_shops.shop_name as shop_name
                FROM 
                    orders 
                INNER JOIN 
                    products on products.id = orders.product_id 
                INNER JOIN 
                    cps on cps.id = products.cp_id 
                INNER JOIN
                	brand_shops on brand_shops.id = products.brand_shop_id
                WHERE
                    orders.id = ' . $this->db->escape($orderId) . ' ; ';
        $result = $this->db->getBySQL($sql);
        if( $result[0] ) {
            $application_config = aafwApplicationConfig::getInstance();
            $result[0]['cp_reference_url'] = $application_config->query(
                    'Protocol.Secure'
                ) . "://" . $application_config->Domain['brandco'] . $result[0]['cp_reference_url'];
            return $result[0];
        }
        return [];
    }

    /**
     * users.idの取得
     * @param array $order
     * @return int|mixed
     */
    private function setUserId() {
        if( $this->order['user_id'] ) {
            $this->userId = $this->order['user_id'];
            return $this->userId;
        }
        return 0;
    }


    /**
     * ブランドの返信設定アドレスの取得
     * @param array $order
     * @return null | string
     */
    private function setBrandMail() {
        $brandId = $this->getBrandId($this->getProductId());
        if( $brandId ) {
            $obj = new BrandGlobalSettingService();
            $fromAddress = $obj->getBrandGlobalSetting($brandId, BrandGlobalSettingService::CAN_SET_MAIL_FROM_ADDRESS);
            if( Util::isNullOrEmpty($fromAddress) || $fromAddress->content == '' ) {
                return null;
            }
            $this->brandFromMail = $fromAddress->content;
            return $this->brandFromMail;
        }
        return null;
    }

    /**
     * brands
     * @return array
     */
    private function getBrandData() {
        $sql = 'select * from brands where id=?id?';
        $param = ['id' => $this->brandId];
        $result = $this->db->getBySQL($sql, [$param]);
        if( $result[0] ) {
            $this->brandData = $result[0];
        }
        return $this->brandData;
    }

    /**
     * brand お問い合わせURLの取得と設定
     * @return string
     */
    private function setbrandInquiryUrl() {
        $application_config = aafwApplicationConfig::getInstance();
        $domain = $application_config->Domain['brandco'];
        $this->brandInquiryUrl = 'http://' . $domain . '/' . $this->brandData['directory_name'] . '/inquiry';
        return $this->brandInquiryUrl;
    }


    /**
     * brands.idの取得
     * @param int $productId
     * @return int
     */
    private function getBrandId($productId = 0) {
        if( $productId ) {
            $sql = 'select '
                . 'brand_id '
                . ' from '
                . ' products '
                . ' inner join '
                . ' cps on cps.id = products.cp_id '
                . ' where '
                . ' products.id = ?product_id?';
            $param = ['product_id' => $productId];
            $result = $this->db->getBySQL($sql, [$param]);
            if( isset($result[0]['brand_id']) ) {
                $this->brandId = $result[0]['brand_id'];
                return $this->brandId;
            }
        }
        return 0;
    }

    /**
     * products.idの取得
     * @param array $order orders 1recode
     * @return int
     */
    private function getProductId() {
        if( isset($this->order['product_id']) ) {
            return $this->order['product_id'];
        }
        return 0;
    }

    /**
     * 受付完了
     * @param int $orderId
     * @return bool
     */
    public function paymentAcceptanceCompletion($orderId) {
        $this->orderId = $orderId;
        try {
            $this->order = $this->getOrder($orderId);
            if( !$this->order ) {
                new Exception('order data none. (' . $orderId . ')');
            }

            $this->setRequestTemplate();
            if( $this->mailTemplateType ) {
                $this->settingMail();
                $this->sendNow();
                $this->updateSendMail($orderId, self::Coloum_MAIL_Request);
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->logger->error('orderId:' . $orderId . ' mail send error.');
            $this->logger->error($e);
            return false;
        }
    }

    /**
     * 未払いリマインドメール送信
     * @param int $orderId
     * @return bool
     */
    public function unpaidRemind($orderId) {
        $this->orderId = $orderId;
        try {
            $this->order = $this->getOrder($orderId);
            if( !$this->order ) {
                new Exception('order data none. (' . $orderId . ')');
            }
            $this->setRemindTemplate();
            if( $this->mailTemplateType ) {
                $this->settingMail();
                $this->sendLater();
                $this->updateSendMail($orderId, self::Coloum_MAIL_Remind);
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->logger->error('orderId:' . $orderId . ' mail send error.');
            $this->logger->error($e);
            return false;
        }
    }

    /**
     * リマインドメールのテンプレート設定
     */
    private function setRemindTemplate() {
        $this->mailTemplateType = null;
        if( isset($this->order['pay_type']) && $this->order['pay_type'] == 3 ) {
            $this->mailTemplateType = 'payment/convenience/remind/' . $this->order['convenience_code'];
        }
    }

    /**
     * リクエスト受付メールのテンプレートの設定
     */
    private function setRequestTemplate() {
        $this->mailTemplateType = null;
        if( isset($this->order['pay_type']) && $this->order['pay_type'] == 3 ) {
            $this->mailTemplateType = 'payment/convenience/request/' . $this->order['convenience_code'];
        }
    }

    /**
     * キャンセルメール (有効期限切れ）
     * @param int $orderId
     * @return bool
     */
    public function cancellationCompleted($orderId) {
        $this->orderId = $orderId;
        $this->mailTemplateType = 'payment/cancel';
        try {
            $this->order = $this->getOrder($orderId);
            if( !$this->order ) {
                new Exception('order data none. (' . $orderId . ')');
            }
            $this->settingMail();
            $this->sendLater();
            $this->updateSendMail($orderId, self::Coloum_MAIL_Cancel);
            return true;
        } catch (Exception $e) {
            $this->logger->error('orderId:' . $orderId . ' mail send error.');
            $this->logger->error($e);
            return false;
        }
    }

    /**
     * 決済の支払いが完了した注文が発生したときに
     * 決済が完了したお知らせを企業などにする
     * @param Order $order
     */
    public function sendOrderCompleteNotificationMail($orderId){
        $order = aafwEntityStoreFactory::create(Orders::class)->findOne($orderId);
        $product = aafwEntityStoreFactory::create(Products::class)->findOne($order->product_id);
        $brandShop = aafwEntityStoreFactory::create(BrandShops::class)->findOne($product->brand_shop_id);
        $cp = aafwEntityStoreFactory::create(Cps::class)->findOne($product->cp_id);
        $brand = aafwEntityStoreFactory::create(Brands::class)->findOne($cp->brand_id);
        $cpPaymentAction = aafwEntityStoreFactory::create(CpPaymentActions::class)->findOne(['product_id'=>$product->id]);

        $orderNotificationToReceivers = aafwEntityStoreFactory::create(OrderNotificationToReceivers::class)->find(['product_id'=>$order->product_id]);
        $toMailAddress = "";
        foreach ($orderNotificationToReceivers as $orderNotificationToReceiver){
            $toMailAddress.= $orderNotificationToReceiver->mail_address.",";
        }

        $orderNotificationBccReceivers = aafwEntityStoreFactory::create(OrderNotificationBccReceivers::class)->find(['product_id'=>$order->product_id]);
        $bccMailAddress = "";
        foreach ($orderNotificationBccReceivers as $orderNotificationBccReceiver){
            $bccMailAddress.= $orderNotificationBccReceiver->mail_address.",";
        }

        $toMailAddress = rtrim($toMailAddress,",");
        $bccMailAddress = rtrim($bccMailAddress,",");

        if( !$toMailAddress ){
            return;
        }

        $application_config = aafwApplicationConfig::getInstance();
        $orderListUrl = $application_config->query(
                'Protocol.Secure'
            ) . "://" . $application_config->Domain['brandco'] ."/".$brand->directory_name."/admin-payment/order_list/".$cpPaymentAction->cp_action_id;

        $replaceParams = [
            "ブランド名"    =>$brand->enterprise_name,
            "注文番号"      =>$order->gmo_payment_order_id,
            "店舗名"        =>$brandShop->shop_name,
            "支払い日"      =>$order->mail_complete_send_date,
            "キャンペーン名" =>$cp->getTitle(),
            "商品名"        =>$product->title,
            "注文確認URL"   =>$orderListUrl,
        ];

        $mailManager = new MailManager();
        $mailManager->loadBodyPlain('payment/complete_notification');
        $mailManager->loadSubject('payment/complete_notification');
        $mailManager->BccAddress = $bccMailAddress;
        $mailManager->sendNow($toMailAddress,$replaceParams);
    }
}
