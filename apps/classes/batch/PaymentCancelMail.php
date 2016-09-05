<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.paymentStepMail');

/**
 * Class PaymentCancelMail
 * 決済がキャンセルされたりEXPIREになったものに対してメールを送る
 * いつkillしても起動してもいい
 */
class PaymentCancelMail{
    private $db;
    /**
     * PaymentRemindMail constructor.
     */
    public function __construct() {
        $this->db = aafwDataBuilder::newBuilder();
    }

    public function run(){
        $orders = $this->findCancelOrders();
        foreach($orders as $order)
        {
            $paymentStepMail = new paymentStepMail();
            $paymentStepMail->cancellationCompleted($order['id']);
        }
    }

    private function findCancelOrders()
    {
        $sql = 'SELECT 
					id 
				FROM
					orders 
				WHERE 
					is_cancel = 1
				AND 
					mail_cancel_send_date is null
				';
        $result = $this->db->getBySQL($sql);
        return $result;
    }
}
