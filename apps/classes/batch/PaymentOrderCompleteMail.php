<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.paymentStepMail');

/**
 * Class PaymentOrderCompleteMail
 * 注文が完了したorderに対してメールを送る
 * 
 */
class PaymentOrderCompleteMail{
    private $db;
    /**
     * PaymentRemindMail constructor.
     */
    public function __construct() {
        $this->db = aafwDataBuilder::newBuilder();
    }

    public function run(){
        $payCreditOrders = $this->findOrdersPayCredit();
        foreach($payCreditOrders as $payCreditOrder)
        {
            $paymentStepMail = new paymentStepMail();
            $paymentStepMail->applicationCompleted($payCreditOrder['id']);
        }

        $payConvenienceOrders = $this->findOrdersPayConvenience();
        foreach ($payConvenienceOrders as $payConvenienceOrder) {
            $paymentStepMail = new paymentStepMail();
            $paymentStepMail->paymentAcceptanceCompletion($payConvenienceOrder['id']);
        }
    }

    private function findOrdersPayCredit()
    {
        $sql = 'SELECT 
					id 
				FROM
					orders 
				WHERE 
					payment_status = "CAPTURE" 
					AND 
					pay_type = 0 
					AND 
					mail_complete_send_date IS NULL 
				';
        $result = $this->db->getBySQL($sql);
        return $result;
    }

    private function findOrdersPayConvenience(){
        $sql = 'SELECT 
					id 
				FROM
					orders 
				WHERE 
					payment_status = "REQSUCCESS" 
					AND 
					pay_type = 3 
					AND 
					mail_request_send_date IS NULL 
				';
        $result = $this->db->getBySQL($sql);
        return $result;
    }
}
