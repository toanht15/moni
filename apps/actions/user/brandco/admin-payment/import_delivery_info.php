<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class import_delivery_info extends BrandcoPOSTActionBase {
    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    protected $ContainerName = 'import_delivery_info';

    function doThisFirst() {
        ini_set('max_execution_time', 3600);
    }

    function validate() {
        // csvファイルチェック
        $fileValidator = new FileValidator($this->FILES['deliveryInfo'], FileValidator::FILE_TYPE_CSV);
        if (!$fileValidator->isValidFile()) {
            return "redirect :" . Util::rewriteUrl(
                "admin-payment",
                "order_list",
                [$this->GET['exts'][0]],
                ['error_code'=>$fileValidator->getErrorCode()]
            );
        }
        return true;
    }

    function doAction() {

        $cpActionId = $this->GET['exts'][0];
        /** @var CpAction $cpAction */
        $cpAction = $this->getModel(CpActions::class)->findOne($cpActionId);
        $cp = $cpAction->getCp();
        if( $cp->brand_id != $this->brand->id ) {
            return '404';
        }

        $cpPaymentAction = $this->getModel(CpPaymentActions::class)->findOne(['cp_action_id' => $cpActionId]);
        $orderStore = $this->getModel(Orders::class);
        $orderItemStore = $this->getModel(OrderItems::class);

        $updateSuccessCount = 0;
        $updateFailedGmoPaymentOrderIds = [];

        if (($handle = fopen($this->FILES['deliveryInfo']['name'], "r")) !== FALSE) {
            while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) {
                //空行らしきものはスキップ
                if(!$line[0]){
                    continue;
                }
                $gmoPaymentOrderId = $line[0];
                $deliveryId = $line[1];
                $order = $orderStore->findOne([
                    'gmo_payment_order_id' => $gmoPaymentOrderId,
                    'payment_status:IN' => ['CAPTURE','PAYSUCCESS'],
                    'product_id' => $cpPaymentAction->product_id
                ]);
                if( !$order ) {
                    $updateFailedGmoPaymentOrderIds[] = $gmoPaymentOrderId;
                    continue;
                }
                $orderItems = $orderItemStore->find(['order_id' => $order->id]);
                foreach ($orderItems as $orderItem) {
                    //一件ずつUPDATEしているが、パフォーマンスが気になったらUPDATE文生成して1クエリーでやるのもいい。
                    $orderItem->delivery_id = $deliveryId;
                    $orderItem->delivery_flg = 1;
                    $orderItem->delivery_date = date("Y-m-d H:i:s");
                    $orderItemStore->save($orderItem);
                    $updateSuccessCount++;
                }
            }
            fclose($handle);
        }

        if( count($updateFailedGmoPaymentOrderIds) > 0 ) {
            aafwLog4phpLogger::getDefaultLogger()->warn(
                "import_error_order_ids : " . implode($updateFailedGmoPaymentOrderIds, ",")
            );
        }
        return "redirect :" . Util::rewriteUrl(
            "admin-payment",
            "order_list",
            [$cpActionId],
            [
                'updated' => $updateSuccessCount,
                'failed' => count($updateFailedGmoPaymentOrderIds),
                //画面に表示するのに件数多すぎると微妙なのでとりあえず20件まで出す
                'failed_ids' => rtrim(implode(array_slice($updateFailedGmoPaymentOrderIds, 0, 20), ","),",")
            ]
        );
    }
}
