<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.paymentStepMail');

/**
 * Class PaymentRemindMail
 * コンビニ決済で期限日2日前になってもまだ未払いの人に対してリマインドメールを送る
 * 対象は期限日が7/31 23:59としたら7/29 0:00で集計する
 * このバッチは途中でkillしてもいいし、いつ起動しても起動したときにメールが送られる
 */
class PaymentRemindMail {


    private $db;
    /**
     * PaymentRemindMail constructor.
     */
    public function __construct() {
        $this->db = aafwDataBuilder::newBuilder();
    }

    public function execute(){
        $orders = $this->findRemindTargetOrders();
        foreach($orders as $order)
        {
            $paymentStepMail = new paymentStepMail();
            $paymentStepMail->unpaidRemind($order['id']);
        }
    }

    public function findRemindTargetOrders(){
        $sql = 'SELECT 
					id 
				FROM 
					orders 
				WHERE 
					payment_status = "REQSUCCESS" 
				AND 
					payment_term_date < DATE_SUB(CURRENT_DATE(),interval -3 day) 
				AND 
					mail_remind_send_date is null 
				 ';
        $result = $this->db->getBySQL($sql);
        return $result;
    }
}
