<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.preOrder');
AAFW::import('jp.aainc.classes.products.payments.BasePayment');
AAFW::import('jp.aainc.classes.products.payments.convenienceStore');
AAFW::import('jp.aainc.classes.products.payments.credit');
AAFW::import('jp.aainc.classes.products.payments.rakuten');
AAFW::import('jp.aainc.classes.products.payments.BasePayment');
AAFW::import('jp.aainc.classes.products.productsRepository');

/**
 * 決済
 * Class Settlement
 */
class Settlement
{
    /**
     * credit払いのpay_type
     */
    const payType_Credit = 0;

    /**
     * コンビニ払いのPayType
     */
    const payType_Convenience = 3;

    /**
     * 楽天ID払いのPayType
     */
    const payType_Rakuten = 18;

    /**
     * エラー
     * @var array
     */
    public $errors = [];

    /**
     * db
     * @var aafwDataBuilder
     */
    public $db;

    /**
     * orders insert data
     * @var array
     */
    private $_insertData = [];

    /**
     * orders.id
     * @var array
     */
    private $_OrderId = 0;

    /**
     * payments/class obj
     * @var null
     */
    private $_paymentObj = null;

    /**
     * gmo payment 情報
     * @var array
     */
    private $_gmoData = [
        'siteId' => null,
        'sitePass' => null,
        'shopId' => null,
        'shopPass' => null
    ];

    /**
     * gmoに送る注文番号
     * @var null
     */
    private $_gmoOrderId = null;

    /**
     * GMOから受け取った取引情報
     * @var null
     */
    private $_gmoTradingCode = null;

    /**
     * order pre_order.access_code
     * @var string
     */
    private $_orderAccessCode = '';

    /**
     * 購入代金 小計金額
     * @var int
     */
    private $_subTotal = 0;

    /**
     * 配送料
     * @var int
     */
    private $_deliveryCharge = 0;

    /**
     * ログ class obj
     * @var Logger
     */
    private $logger = null;

    /**
     * 支払期限(日数)
     * @var int
     */
    private $payLimitDay = 6;


    /**
     * Settlement constructor.
     */
    public function __construct()
    {
        $obj = new aafwDataBuilder();
        $this->db = $obj->newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * 決済処理の実行
     * @param int $userId
     * @param string $accessCode
     * @param array $order
     * @return bool
     */
    public function execSettlement($userId = 0, $accessCode = '', $order)
    {
        try {
            $this->setGmoData($order);
            $this->setAccessCode($accessCode);
            $this->saveOrder($userId, $accessCode, $order);
            $this->_gmoOrderId = uniqid($this->_OrderId . '-');
            $this->updateGmoOrderId();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->errors['exec'] = '決済に失敗しました。';
            return false;
        }

        try {
            $this->getTradingCode(); //取引の登録
            $this->pay($order, $userId); //支払い処理
            if ($this->errors) {
                $this->logger->error($this->errors);
                return false;
            }
            $this->db->getBySQL('BEGIN;');
            $this->updateStatus($this->_OrderId, $userId);
            $this->updateStock($order); //stockの更新
            $this->saveOrderItems($this->_OrderId, $order); //orderItemsの登録
            $this->db->getBySQL('COMMIT;');
            return true;
        } catch (Exception $e) {
            $this->db->getBySQL('ROLLBACK;');
            $this->errors['exec'] = $e->getMessage();
            return false;
        }
    }

    /**
     * 支払い処理
     * @param array $order
     * @param int $userId
     */
    private function pay($order = [], $userId)
    {
        if ($this->errors) {
            return false;
        }
        if (
            $this->payConvenience($order, $userId)
            || $this->payCredit($order, $userId)
            || $this->payRakuten($order, $userId)
        ) {
            return true;
        }
        $this->error[__FUNCTION__] = '決済処理に失敗しました。';
        return false;
    }

    /**
     * クレジット払いの決済
     */
    private function payCredit($order, $userId)
    {
        if ($this->_insertData['pay_type'] == self::payType_Credit) {
            $cardNo = trim($order['cardNumber']);
            $cardExpire = $order['cardExpirationYear'].$order['cardExpirationMonth'];
            $securityCode = $order['securityCode'];
            $result = $this->_paymentObj->pay(
                $this->_gmoTradingCode,
                $cardNo,
                $cardExpire,
                $securityCode
            );
            if (!$result->errors) {
                $this->updateOrderPayment($order, $result);
                $this->deletePreOrder($this->_insertData['order_access_code']);
            }
            $this->db->getBySQL('ROLLBACK;');
            return false;
        }
        return false;
    }

    /**
     * 支払い結果の更新
     * @param $order
     * @param $result
     */
    private function updateOrderPayment($order, $result)
    {
        $payment_tran_date = null;
        if ($this->_paymentObj->payResult['tranDate']) {
            $payment_tran_date = sprintf("%04d-%02d-%02d %02d:%02d:%02d",
                substr($this->_paymentObj->payResult['tranDate'], 0, 4),
                substr($this->_paymentObj->payResult['tranDate'], 4, 2),
                substr($this->_paymentObj->payResult['tranDate'], 8, 2),
                substr($this->_paymentObj->payResult['tranDate'], 10, 2),
                substr($this->_paymentObj->payResult['tranDate'], 12, 2)
            );
        }

        $sql = 'update orders set '
            . ' access_id="' . $this->db->escape($this->_gmoTradingCode['AccessID']) . '", '
            . ' access_pass="' . $this->db->escape($this->_gmoTradingCode['AccessPass']) . '", '
            . ' payment_conf_no="' . $this->db->escape($this->_paymentObj->payResult['confNo']) . '", '
            . ' payment_receipt_no="' . $this->db->escape($this->_paymentObj->payResult['receiptNo']) . '", '
            . ' payment_tran_date="' . $this->db->escape($payment_tran_date) . '", '
            . ' payment_receipt_url="' . $this->db->escape($this->_paymentObj->payResult['receiptUrl']) . '", '
            . ' payment_check_string="' . $this->db->escape($this->_paymentObj->payResult['checkString']) . '", '
            . ' payment_client_field_1="' . $this->db->escape($this->_paymentObj->payResult['clientField1']) . '", '
            . ' payment_client_field_2="' . $this->db->escape($this->_paymentObj->payResult['clientField2']) . '", '
            . ' payment_client_field_3="' . $this->db->escape($this->_paymentObj->payResult['clientField3']) . '", '
            . ' updated_at = NOW() '
            . ' where gmo_payment_order_id = "' . $this->_gmoTradingCode['OrderID'] . '"';
        $this->db->executeUpdate($sql);
    }

    /**
     * 楽天ID払いの決済 TODO:
     */
    private function payRakuten()
    {
        return false;
    }

    /**
     * コンビニ払い支払い
     * @param array $order
     * @param int $userId
     * @return mixed
     */
    private function payConvenience($order, $userId)
    {
        if ($this->_insertData['pay_type'] == self::payType_Convenience) {
            $this->_paymentObj->setLimitDay($this->payLimitDay);
            $result = $this->_paymentObj->pay(
                $this->_gmoTradingCode,
                $this->_insertData['convenience_code'],
                $this->_insertData['last_name'] . $this->_insertData['first_name'],
                $this->_insertData['last_name_kana'] . $this->_insertData['first_name_kana'],
                $this->_insertData['tel_no1'] . $this->_insertData['tel_no2'] . $this->_insertData['tel_no3'],
                $order['product']['inquiry_name'],
                $order['product']['inquiry_phone'],
                substr($order['product']['inquiry_time1'], 0, 5) . '-' . substr($order['product']['inquiry_time2'],
                    0,
                    5)
            );
            if (!$result->errors) {
                $this->updateOrderPayment($order, $result);
                $this->deletePreOrder($this->_insertData['order_access_code']);
            }

            return $result;
        }
        return false;
    }

    /**
     * 支払いコードの取得
     */
    private function getTradingCode()
    {
        if ($this->errors) {
            return;
        }
        $obj = $this->_paymentObj;
        $amount = $this->_subTotal + $this->_deliveryCharge;
        $this->_gmoTradingCode = $obj->add($this->_gmoOrderId, $amount);
    }

    /**
     * GMO設定格納
     * @param $order
     */
    private function setGmoData($order)
    {
        $this->_gmoData['shopId'] = $order['product']['shopID'];
        $this->_gmoData['shopPass'] = $order['product']['shopPass'];
        $this->_gmoData['siteId'] = $order['product']['siteID'];
        $this->_gmoData['sitePass'] = $order['product']['sitePass'];
    }

    /**
     * 注文の登録
     * @param int $userId
     * @param string $accessCode
     * @param array $order
     * @return int 0:insert失敗
     */
    public function saveOrder($userId, $accessCode, $order)
    {
        if ($this->validateOrder($order)) {
            $this->initOrderInsertBaseData();
            $this->setUserId($userId);
            $this->setAccessCode($accessCode);
            $this->setAddress($order);
            $this->setTel($order);
            $this->setName($order);
            $this->setCreditData($order);
            $this->setConvenienceData($order);
            $this->setRakutenData($order);
            $this->setProductDetail($order);
            $this->insertData();
            return $this->_OrderId;
        }
        return 0;
    }

    /**
     * gmo用orderIDの登録とコストの登録
     */
    private function updateGmoOrderId()
    {
        $sql = 'UPDATE orders '
            . ' SET '
            . ' gmo_payment_order_id = "' . $this->db->escape($this->_gmoOrderId) . '" ,'
            . ' updated_at = NOW()'
            . ' where id = ' . $this->_OrderId . ';';;
        $this->db->executeUpdate($sql);
    }

    /**
     * データの登録
     * @return int orders.id
     */
    private function insertData()
    {
        if ($this->errors) {
            return false;
        }
        $keys = $this->_createKeys($this->_insertData);
        $values = $this->_createValues($this->_insertData);
        $sql = 'INSERT INTO
                 orders 
                    ( ' . $keys . ' )
                VALUES 
                    ( ' . $values . '); ';
        $this->db->executeUpdate($sql);
        $sql = 'select 
                  id 
                from 
                  orders 
                where
                  order_access_code = ?order_access_code?
                ORDER BY id desc
                ';
        $param = [['order_access_code' => $this->_insertData['order_access_code']]];
        $result = $this->db->getBySQL($sql, $param);
        if (isset($result[0]['id']) && $result[0]['id'] > 0) {
            $this->_OrderId = $result[0]['id'];
            return $result[0]['id'];
        }
        return 0;
    }

    /**
     * 名前を格納
     * @param $order
     */
    private function setName($order)
    {
        $this->_insertData['first_name'] = $order['firstName'];
        $this->_insertData['last_name'] = $order['lastName'];
        $this->_insertData['first_name_kana'] = $order['firstNameKana'];
        $this->_insertData['last_name_kana'] = $order['lastNameKana'];
    }

    /**
     * product detail
     * @param $order
     */
    private function setProductDetail($order)
    {
        $this->_insertData['product_id'] = $order['product']['id'];
        $this->_insertData['delivery_charge'] = $order['product']['delivery_charge'];
        $this->_deliveryCharge = $order['product']['delivery_charge'];
        $this->_insertData['sub_total_cost'] = $this->getSubTotalCost($order);
        $this->_insertData['total_cost'] = $order['product']['delivery_charge'] + $this->_insertData['sub_total_cost'];
    }

    /**
     * コンビニエンス情報をセット
     * @param $order
     */
    public function setConvenienceData($order)
    {
        if ($order['payType'] == self::payType_Convenience) {
            $this->_insertData['pay_type'] = $order['payType'];
            $this->_insertData['pay_type_name'] = 'コンビニ払';
            $this->_insertData['convenience_code'] = $order['convenienceCode'];
            $this->_insertData['convenience_name'] = $order['convenienceName'];
            $this->_paymentObj = new convenienceStore();
            $this->setDataPaymentObj();
        }
    }

    /**
     * 楽天決済情報をセット
     * @param $order
     */
    public function setRakutenData($order)
    {
        if ($order['payType'] == self::payType_Rakuten) {
            $this->_insertData['pay_type_name'] = '楽天';
            $this->_insertData['pay_type'] = $order['payType'];
            $this->_paymentObj = new Rakuten();
            $this->setDataPaymentObj();
        }
    }

    /**
     * クレジットカードの登録
     * @param $order
     */
    public function setCreditData($order)
    {
        if ($order['payType'] == self::payType_Credit) {
            $this->_insertData['pay_type'] = $order['payType'];
            $this->_insertData['pay_type_name'] = 'クレジット決済';
            $this->_paymentObj = new Credit();
            $this->setDataPaymentObj();
        }
    }

    /**
     * gmo payment 情報格納
     */
    private function setDataPaymentObj()
    {
        $this->_paymentObj->setShopId($this->_gmoData['shopId']);
        $this->_paymentObj->setShopPass($this->_gmoData['shopPass']);
        $this->_paymentObj->setSiteId($this->_gmoData['siteId']);
        $this->_paymentObj->setSitePass($this->_gmoData['sitePass']);
    }

    /**
     * 住所の格納
     * @param $order
     */
    private function setAddress($order)
    {
        $productRepository = new ProductsRepository();
        $this->_insertData['pref_name'] = $productRepository->getPrefList()[$order['prefId']];
        $this->_insertData['zip_code1'] = $order['zipCode1'];
        $this->_insertData['zip_code2'] = $order['zipCode2'];
        $this->_insertData['address1'] = $order['address1'];
        $this->_insertData['address2'] = $order['address2'];
        $this->_insertData['address3'] = $order['address3'];
    }

    /**
     * 電話番号登録
     * @param array $order
     */
    private function setTel($order)
    {
        $this->_insertData['tel_no1'] = $order['telNo1'];
        $this->_insertData['tel_no2'] = $order['telNo2'];
        $this->_insertData['tel_no3'] = $order['telNo3'];
    }

    /**
     * accessCodeのチェックと格納
     * @param string $accessCode
     */
    private function setAccessCode($accessCode = '')
    {
        //登録済みチェック
        if ($this->checkAccessCode($accessCode)) {
            $this->_insertData['order_access_code'] = $accessCode;
            $this->_orderAccessCode = $accessCode;
        }
    }

    /**
     * @param $accessCode
     */
    public function checkAccessCode($accessCode)
    {
        if (!$accessCode) {
            $this->errors['access_code'] = '注文情報が取得できません。';
            return false;
        }
        $sql = 'select 
					count(*) as count
				from 
					orders 
				where 
					order_access_code = ?access_code?';
        $param = [['access_code' => $accessCode]];
        $result = $this->db->getBySQL($sql, $param);
        if (isset($result[0]['count']) && $result[0]['count'] > 0) {
            //access_codeがあり、登録もあるので登録できない。
            $this->errors['access_code'] = 'すでに注文済みです。';
            return false;
        }
        //登録なしだから登録できる。
        return true;
    }

    /**
     * user_idの
     * @param int $userId
     */
    private function setUserId($userId)
    {
        $this->_insertData['user_id'] = $userId;
    }


    /**
     * バリデーションチェック TODO:
     * @param array $order
     * @return bool
     */
    private function validateOrder($order)
    {
        return true;
        $obj = new preOrder();
        $obj->validate($order);
        if ($obj->validateErrors) {
            $this->errors['validate'] = $obj->validateErrors;
            return false;
        }
        return true;
    }

    /**
     * 在庫更新
     * @param $order
     */
    private function updateStock($order)
    {
        foreach ($order['order'] as $key => $item) {
            $sql = 'UPDATE
 						product_items 
 					SET 
 						stock = stock - ' . $item . ',
 						sale_count = sale_count + ' . $item . '
 					WHERE  id = ' . $key
                . ' AND stock_limited = 1' //販売数上限ありの場合のみ
            ;
            $this->db->executeUpdate($sql);
        }
    }

    /**
     * $order['order']から、order_items insert data雛形作成
     * @param int $order_id
     * @param array $order
     * @return array
     */
    public function getBaseOrderItemsData($order_id, $order)
    {
        if (!$order_id) {
            return [];
        }
        $case = [];
        foreach ($order['order'] as $key => $item) {
            $case[] = ' WHEN id=' . $key . ' THEN ' . $item . ' ';
        }
        $case_text = join(' ', $case);
        $daytime = date('Y-m-d H:i:s');
        $ids = join(',', array_keys($order['order']));;
        $sql = 'SELECT '
            . ' NULL as id, '
            . ' ' . $order_id . ' as order_id, '
            . ' product_items.id as product_item_id ,'
            . ' unit_price as unit_price, '
            . ' title as product_item_title,'
            . '"' . $daytime . '" as created_at,'
            . ' (case '
            . $case_text
            . ' else 0 end) as sales_count '
            . ' FROM '
            . ' product_items '
            . ' WHERE id in (' . $ids . ')';
        $result = $this->db->getBySQL($sql);
        return $result;
    }

    /**
     * 注文商品の保存
     * @param $orderId
     * @param $order
     */
    public function saveOrderItems($orderId, $order)
    {
        if (!$orderId) {
            return false;
        }

        try {
            $insertData = $this->getBaseOrderItemsData($orderId, $order);
            if (!$insertData) {
                throw new Exception('注文の登録に失敗しました。');
            }

            foreach ($insertData as $item) {
                $this->_insert('order_items', $item);
            }
            return true;
        } catch (Exception $e) {
            $this->errors['saveOrderItems'] = $e->getMessage();
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * 小計金額の計算
     * array @order
     * @return int
     */
    public function getSubTotalCost($order = [])
    {
        if (!$order) {
            return 0;
        }
        $items_array = [];
        $ids_array = [];

        foreach ($order['order'] as $id => $count) {
            $items_array[] = ' when ' . $id . ' then (' . $count . ' * unit_price) ';
            $ids_array[] = $id;
        }
        $items = join(' ', $items_array);
        $ids = join(', ', $ids_array);

        $sql = 'SELECT 
          SUM((case id ' . $items . '  end )) as sub_total
          FROM product_items
          WHERE 
            id 
          in ( ' . $ids . ');';
        $result = $this->db->getBySQL($sql);
        if (isset($result[0]['sub_total']) && $result[0]['sub_total'] > 0) {
            $this->_subTotal = $result[0]['sub_total'];
            return $this->_subTotal;
        }
        return 0;
    }


    /**
     * order用insertデータ
     */
    private function initOrderInsertBaseData()
    {
        $this->_insertData = [
            'id' => null,
            'user_id' => '',
            'product_id' => 0,
            'gmo_payment_order_id' => null,
            'access_id' => null,
            'access_pass' => null,
            'payment_status' => null,
            'pay_type' => 0,
            'pay_type_name' => '',
            'convenience_code' => null,
            'convenience_name' => null,
            'delivery_charge' => 0,
            'sub_total_cost' => 0,
            'total_cost' => 0,
            'payment_completion_date' => "0000-00-00 00:00:00",
            'is_cancel' => 0,
            'first_name' => null,
            'last_name' => null,
            'first_name_kana' => null,
            'last_name_kana' => null,
            'zip_code1' => null,
            'zip_code2' => null,
            'pref_name' => null,
            'address1' => null,
            'address2' => null,
            'address3' => null,
            'tel_no1' => null,
            'tel_no2' => null,
            'tel_no3' => null,
            'order_access_code' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'order_completion_date' => date('Y-m-d H:i:s')
        ];
    }


    /**
     * keyから作成
     * @param array $data
     * @return string
     */
    public function _createKeys($data = [], $type = '`')
    {
        $keys = [];
        foreach ($data as $key => $item) {
            $keys[] = $type . $key . $type;
        }
        return join(',', $keys);
    }

    /**
     * insert等の valuesを作成する
     * @param array $data
     * @return string
     */
    public function _createValues($insertArray = [])
    {
        $data = [];
        foreach ($insertArray as $key => $item) {
            if ($item === null) {
                $data[$key] = 'NULL';
            } else {
                $data[$key] = '"' . $this->db->escape($item) . '"';
            }
        }
        return join(',', $data);
    }

    /**
     * insert
     * @param $table
     * @param $data
     */
    public function _insert($table, $data)
    {
        $keys = $this->_createKeys($data);
        $values = $this->_createValues($data);
        $sql = 'INSERT INTO
                  ' . $table . '
                    ( ' . $keys . ' )
                VALUES 
                    ( ' . $values . '); ';
        return $this->db->executeUpdate($sql);
    }

    /**
     * キャンセル
     * @param int $orderId
     * @return bool
     */
    public function cancel($orderId = 0, $userId = 0)
    {
        $order = $this->getOrder($orderId, $userId);
        if (!$order) {
            //注文がない
            return false;
        }
        $obj = new BasePayment();
        $obj->setShopId($order['gmo_shop_id']);
        $obj->setShopPass($order['gmo_shop_pass']);
        if ($obj->cancel(
            $order['access_id'],
            $order['access_pass'],
            $order['gmo_payment_order_id'],
            $order['total_cost'])
        ) {
            $this->updateCancel($orderId, $userId);
            return true;
        }
        return false;
    }

    /**
     * キャンセルの登録更新
     * @param int $orderId
     * @param int $userId
     */
    private function updateCancel($orderId, $userId)
    {
        $sql = 'select * from orders'
            . ' where id = ' . $this->db->escape($orderId)
            . ' AND '
            . ' user_id = ' . $this->db->escape($userId)
            . ' AND '
            . ' is_cancel = 0;';
        $result = $this->db->getBySQL($sql);
        if ($result[0]) {
            $sql = 'update orders '
                . ' set '
                . ' is_cancel = 1, '
                . ' canceled_at = NOW() '
                . ' where id = ' . $this->db->escape($orderId)
                . ' AND '
                . ' user_id = ' . $this->db->escape($userId)
                . ' AND '
                . ' is_cancel = 0;';
            $this->db->executeUpdate($sql);

            //在庫を戻す
            $sql = 'select * from order_items where id=' . $this->db->escape($orderId);
            $result = $this->db->getBySQL($sql);
            foreach ($result as $item) {
                $sql = 'update product_items set '
                    . ' stock = stock + ' . $this->db->escape($item['sales_count']) . ', '
                    . ' sale_count = sale_count - ' . $this->db->escape($item['sales_count']) . ', '
                    . ' updated_at = NOW() '
                    . ' where id=' . $item['product_item_id'] . ';';
                $this->db->executeUpdate($sql);
            }
        }
    }

    /**
     * 注文情報の取得
     * @param $orderId
     * @param $userId
     * @return bool
     */
    public function getOrder($orderId, $userId)
    {
        $sql = 'SELECT '
            . ' orders.*, '
            . ' brand_shops.gmo_shop_id, '
            . ' brand_shops.gmo_shop_pass '
            . ' FROM '
            . ' orders '
            . ' INNER JOIN '
            . ' products on (products.id = orders.product_id) '
            . ' INNER JOIN '
            . ' brand_shops on (brand_shops.id = products.brand_shop_id) '
            . ' WHERE '
            . ' orders.id=?id? '
            . ' AND '
            . ' orders.user_id=?user_id? ;';
        $param = [
            'id' => $orderId,
            'user_id' => $userId
        ];
        $result = $this->db->getBySQL($sql, [$param]);
        if (!isset($result[0]) && !$result[0]) {
            return false;
        }
        return $result[0];
    }

    /**
     * GMO 取引情報の取得
     * @param int $orderId
     * @param int $userId
     * @return array|null
     */
    public function getStatus($orderId, $userId)
    {
        $order = $this->getOrder($orderId, $userId);
        if (!$order) {
            return null;
        }
        $obj = new BasePayment();
        $obj->setShopId($order['gmo_shop_id']);
        $obj->setShopPass($order['gmo_shop_pass']);
        $payType = $order['pay_type'];
        $GmoOrderId = $order['gmo_payment_order_id'];
        $result = $obj->getStatus($payType, $GmoOrderId);
        return $result;
    }

    /**
     * 更新
     * @param $orderId
     * @param $userId
     * @return bool
     */
    public function updateStatus($orderId, $userId)
    {
        if ($this->errors) {
            return false;
        }
        $status = $this->getStatus($orderId, $userId);
        if (!isset($status['status'])) {
            return false;
        }

        try {
            $payment_tran_date = '';
            $payment_term_date = '';
            $payment_status = $status['status'];
            if (isset($status['PaymentTerm']) && $status['PaymentTerm']) {
                $payment_term_date = ' payment_term_date = "'
                    . date("Y-m-d H:i:s", strtotime($status['PaymentTerm']))
                    . '", ';
            }
            if (isset($status['process_date']) && $status['process_date']) {
                $payment_tran_date = ' payment_tran_date = "'
                    . date("Y-m-d H:i:s", strtotime($status['process_date']))
                    . '" , ';
            }

            if (!$status['card_no']) {
                $status['card_no'] = '';
            }
            if ($payment_status == 'CAPTURE') {
                $payment_completion_date = ' payment_completion_date = NOW(), ';
            } elseif ($payment_status == 'PAYFAIL' || $payment_status == 'CANCEL') {
                //決済失敗 //決済受付直後のステータス更新のため、ここで失敗してるなら例外処理で良い
                $this->logger->error('userId:' . $userId . ' orderId:' . $orderId);
                throw new Exception('決済に失敗しました。');
            } else {
                $payment_completion_date = ' payment_completion_date = "0000-00-00 00:00:00", ';
            }

            $sql = 'update orders set '
                . ' payment_status="' . $this->db->escape($payment_status) . '", '
                . ' updated_at = NOW(), '
                . ' payment_status_updated_at = NOW(), '
                . $payment_completion_date
                . $payment_tran_date
                . $payment_term_date
                . ' payment_credit = "' . $this->db->escape($status['card_no']) . '" '
                . ' where '
                . ' id = "' . $this->db->escape($orderId) . '" '
                . ' AND '
                . ' user_id = ' . $this->db->escape($userId) . ';';
            $this->db->executeUpdate($sql);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * 仮保存を削除
     * @param string $accessCode
     */
    private function deletePreOrder($accessCode)
    {
        $obj = new preOrder();
        $obj->removeOrder($accessCode);
    }
}
