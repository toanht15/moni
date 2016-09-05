<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class order_list extends BrandcoGETActionBase{

    public $NeedOption = array();
    private $limit = 20;

    function validate() {

        if(!$this->GET['exts'][0]){
            return '404';
        }

        if (!$this->isLoginAdmin()) {
            return 'redirect: '.Util::rewriteUrl('admin-payment', 'login',array($this->GET['exts'][0]));
        }

        switch (true) {
            case !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']):
            case $_SERVER['PHP_AUTH_USER'] != "kenken":
            case $_SERVER['PHP_AUTH_PW'] != "W2im7eqP":
                header('WWW-Authenticate: Basic realm="Please log in with brand\'s account"');
                header('Content-Type: text/plain; charset=utf-8');
                die('このページを見るにはログインが必要です');
        }

        return true;
    }

    function doAction() {

        $cpActionId = $this->GET['exts'][0];
        /** @var CpAction $cpAction */
        $cpAction = $this->getModel(CpActions::class)->findOne($cpActionId);
        $cp = $cpAction->getCp();
        if($cp->brand_id != $this->brand->id) {
            return '404';
        }
        /** @var Product $product */
        $product = $this->getModel(Products::class)->findOne(array('cp_id'=>$cp->id));
        
        $pager = array(
            'page' => $this->GET['p'] ? $this->GET['p'] : 1,
            'count' => $this->limit,
        );
        $condition = array(
            'brand_id' => $this->brand->id,
            'from_order_completion_date'     => $this->buildDateFormatParam('from_order_completion_date'),
            'to_order_completion_date'       => $this->buildDateFormatParam('to_order_completion_date'),
            'from_payment_completion_date'   => $this->buildDateFormatParam('from_payment_completion_date'),
            'to_payment_completion_date'     => $this->buildDateFormatParam('to_payment_completion_date'),
            'from_delivery_completion_date'   => $this->buildDateFormatParam('from_delivery_completion_date'),
            'to_delivery_completion_date'     => $this->buildDateFormatParam('to_delivery_completion_date'),
            'delivery_flg'                   => (isset($this->GET['delivery_flg']) && $this->GET['delivery_flg'] != 99) ? $this->GET['delivery_flg'] : null,
            'user_no'                        => $this->GET['user_no'] ? $this->GET['user_no'] : null,
            'gmo_payment_order_id'           => $this->GET['gmo_payment_order_id'] ? $this->GET['gmo_payment_order_id'] : null,
            'product_id'                     => $product->id
        );
        $result = aafwDataBuilder::newBuilder()->getOrderList($condition, null, $pager, true);
        $this->Data['orders'] = $result['list'];
        $this->Data['page'] = $this->GET['p'] ? $this->GET['p'] : 1;
        $this->Data['total_count'] = $result['pager']['count'];
        $this->Data['count'] = $this->limit;
        $this->Data['cp_action_id'] = $cpActionId;
        //配送管理業者が色んなページ移動しないようにヘッダーの余計な項目消したいがためにフラグ追加しました
        $this->Data['pageStatus']['isOrderList'] = true;
        if($this->GET['error_code']){
            $this->Data['error_message'] =  FileValidator::$error_messages_dict[$this->GET['error_code']];
        }
        return "user/brandco/admin-payment/order_list.php";
    }

    public function buildDateFormatParam($queryKey){
        if(!$this->GET[$queryKey]){
            return null;
        }
        //yyyy-mm-dd H:i:sを作る
        return $this->GET[$queryKey]. " ".$this->GET["{$queryKey}_HH"].":".$this->GET["{$queryKey}_MM"].":".$this->GET["{$queryKey}_SS"];
    }

}
