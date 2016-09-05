<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.payments.BasePayment');
AAFW::import('jp.aainc.classes.products.paymentStepMail');

/**
 * Class PaymentStatusUpdate
 * REQSUCCESSステータスのものに対して、現在のステータスに変更があるかどうかを
 * GMOに問い合わせて変化(決済完了、期限切れ)があればpayment_statusと
 * そのステータスに影響する値を更新する
 */
class PaymentStatusUpdate {
    /**
     * 送信間隔 分
     */
    const LastSendInterval = 10;
    /**
     * logger
     * @var Logger|null
     */
    private $logger = null;

    /**
     * limit
     * @var int
     */
    private $unitCount = 5;

    /**
     * db
     * @var aafwDataBuilder
     */
    public $db;

    public function __construct() {

        $this->db = new aafwDataBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * 実行
     */
    public function run() {
        $orders = $this->getOrder();
        $this->updateStatus($orders);
    }

    /**
     * 支払い状況を更新
     * @param array $orders
     * @return bool
     */
    private function updateStatus($orders = []) {
        if( !$orders ) {
            return false;
        }
        foreach ($orders as $order) {
            $paymentCompletionDate = '';
            $cancel = '';
            $status = $this->getStatus($order);
            $payment_status = $status['status'];
            $payment_term = date("Y-m-d H:i:s", strtotime($status['PaymentTerm']));
            if( $payment_status == 'PAYSUCCESS' ) {
                $paymentCompletionDate = ', payment_completion_date = NOW() ';
            } elseif( $payment_status == 'EXPIRED' || $payment_status == 'CANCEL' ) {
                $cancel = ', is_cancel = 1  ';
            }
            $sql = 'update orders set '
                . ' payment_status="' . $this->db->escape($payment_status) . '", '
                . ' payment_term_date="' . $this->db->escape($payment_term) . '", '
                . ' updated_at = NOW(), '
                . ' payment_status_updated_at = NOW() '
                . $paymentCompletionDate
                . $cancel
                . ' where '
                . ' id = ' . (int)$this->db->escape($order['id']) . '; ';
            $this->db->executeUpdate($sql);
            if( $payment_status == 'EXPIRED' || $payment_status == 'CANCEL' ) {
                $this->updateStock($order['id']);
            }
        }
    }

    private function updateStock($orderId) {
        $orderItems = $this->getOrderItems($orderId);
        foreach ($orderItems as $orderItem) {
            $sql = 'UPDATE
 						product_items 
 					SET 
 						stock = stock + ' . $orderItem["sales_count"] . ',
 						sale_count = sale_count - ' . $orderItem["sales_count"]  . '
 					WHERE  id = ' . $orderItem['product_item_id']
                . ' AND stock_limited = 1' //販売数上限ありの場合のみ
            ;
            $this->db->executeUpdate($sql);
        }
    }

    /**
     * gmoの状態を取得
     * @param $order
     * @return array|null
     */
    private function getStatus($order) {
        $obj = new BasePayment();
        $obj->setShopId($order['gmo_shop_id']);
        $obj->setShopPass($order['gmo_shop_pass']);
        $payType = $order['pay_type'];
        $GmoOrderId = $order['gmo_payment_order_id'];
        $result = $obj->getStatus($payType, $GmoOrderId);
        return $result;
    }

    /**
     * 注文情報の取得
     * @return array
     */
    public function getOrder() {
        $sql = 'SELECT
					orders.*,
					brand_shops.gmo_shop_id,
					brand_shops.gmo_shop_pass
				FROM
					orders
				inner JOIN 
					products on products.id = orders.product_id
				inner JOIN 
					brand_shops on brand_shops.id = products.brand_shop_id
				WHERE
					orders.pay_type = 3
				AND
					orders.is_cancel = 0 
				AND 
					orders.payment_status = "REQSUCCESS"
				AND 
					orders.payment_status_updated_at <= DATE_SUB(NOW(),INTERVAL ' . self::LastSendInterval . '  MINUTE) ;';
        $result = $this->db->getBySQL($sql);
        return $result;
    }

    public function getOrderItems($orderId){
        $sql = 'SELECT * FROM order_items WHERE order_id = '.$orderId;
        return $this->db->getBySQL($sql);
    }
}
