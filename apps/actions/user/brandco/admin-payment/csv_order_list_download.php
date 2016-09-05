<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
class csv_order_list_download extends BrandcoGETActionBase{

    public $NeedOption = array();

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

        $cpAction = $this->getModel(CpActions::class)->findOne($this->GET['exts'][0]);
        $cp = $cpAction->getCp();
        if($cp->brand_id != $this->brand->id) {
            return '404';
        }
        /** @var Product $product */
        $product = $this->getModel(Products::class)->findOne(array('cp_id'=>$cp->id));

        // Export csv
        $csv = new CSVParser();
        $dt = new DateTime();
        header("Content-type:" . $csv->getContentType());
        $csv->setCSVFileName($dt->format('YmdHis'));
        header($csv->getDisposition());

        $db = new aafwDataBuilder();
        $condition = array(
            '__NOFETCH__' => true,
            'brand_id' => $this->brand->id,
            'from_order_completion_date'     => $this->buildDateFormatParam('from_order_completion_date'),
            'to_order_completion_date'       => $this->buildDateFormatParam('to_order_completion_date'),
            'from_payment_completion_date'   => $this->buildDateFormatParam('from_payment_completion_date'),
            'to_payment_completion_date'     => $this->buildDateFormatParam('to_payment_completion_date'),
            'from_delivery_completion_date'   => $this->buildDateFormatParam('from_delivery_completion_date'),
            'to_delivery_completion_date'     => $this->buildDateFormatParam('to_delivery_completion_date'),
            'delivery_flg'                   => (isset($this->GET['delivery_flg']) && $this->GET['delivery_flg'] != 99) ? $this->GET['delivery_flg'] : null,
            'user_no' => $this->GET['user_no'] ? $this->GET['user_no'] : null,
            'gmo_payment_order_id' => $this->GET['gmo_payment_order_id'] ? $this->GET['gmo_payment_order_id'] : null,
            'product_id' => $product->id
        );
        $rs = aafwDataBuilder::newBuilder()->getOrderList($condition);


        $data_csv['header'] = [
            '注文ID',
            '会員NO',
            '伝票番号',
            '購入商品名',
            '購入個数',
            '郵便番号',
            '住所',
            '氏',
            '名',
            '氏(かな)',
            '名(かな)',
            '電話番号',
            '申込日時',
            '決済完了日時',
            '発送日',
            '発送ステータス'
        ];
        while ($order = $db->fetch($rs)) {
            $data_csv['list'][] = array(
                $order['gmo_payment_order_id'],
                $order['no'],
                $order['delivery_id'],
                $order['product_title'],
                $order['sales_count'],
                $order['zip_code1']."-".$order['zip_code2'],
                $order['pref_name'].$order['address1'].$order['address2'].$order['address3'],
                $order['last_name'],
                $order['first_name'],
                $order['last_name_kana'],
                $order['first_name_kana'],
                $order['tel_no1'].'-'.$order['tel_no2'].'-'.$order['tel_no3'],
                $order['order_completion_date'],
                $order['payment_completion_date'],
                $order['delivery_date'],
                $order['delivery_flg']
            );
        }
        print mb_convert_encoding($csv->out($data_csv, 0), "sjis-win", "UTF-8");
        exit();
    }

    public function buildDateFormatParam($queryKey){
        if(!$this->GET[$queryKey]){
            return null;
        }
        //yyyy-mm-dd H:i:sを作る
        return $this->GET[$queryKey]. " ".$this->GET["{$queryKey}_HH"].":".$this->GET["{$queryKey}_MM"].":".$this->GET["{$queryKey}_SS"];
    }

}
