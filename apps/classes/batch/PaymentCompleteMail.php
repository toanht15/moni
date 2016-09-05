<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.paymentStepMail');

/**
 * Class PaymentCompleteMail
 * コンビニ決済で決済が完了した人(PAYSUCCESS)に対してメールを送る
 * いつkillしても起動しても大丈夫
 */
class PaymentCompleteMail {

    private $db;

    public function __construct() {
        $this->db = aafwDataBuilder::newBuilder();
    }

    public function run() {
        $orders = $this->findCompleteOrders();
        foreach($orders as $order)
        {
            $paymentStepMail = new paymentStepMail();
            $paymentStepMail->applicationCompleted($order['id']);
        }
        
    }

    public function findCompleteOrders() {
        $sql = 'SELECT 
					id 
				FROM
					orders 
				WHERE 
					payment_status = "PAYSUCCESS" 
				AND 
					mail_complete_send_date is null
				';
        $result = $this->db->getBySQL($sql);
        return $result;
    }
}
