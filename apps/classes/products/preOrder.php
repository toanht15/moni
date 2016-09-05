<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.productsRepository');
AAFW::import('jp.aainc.classes.products.cipher');


/**
 * 注文の仮保存
 * memo: table:  pre_orders cps
 * Class preOrde
 */
class preOrder
{
    const SESSION_KEY_ACCESS_CODE = "Products.order.access_code";

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
     * DB
     * @var aafwDataBuilder
     */
    public $db;

    /**
     * 注文情報の有効期限(分）
     * @var int
     */
    public $expiration = 300;

    /**
     * validation errors
     * @var array
     */
    public $validateErrors = [];

    /**
     * 支払いタイプ
     * @var array
     */
    public $payTypeList = [
        0 => 'クレジットカード決済',
        3 => 'コンビニ支払い',
        18 => '楽天ID決済'
    ];

    /**
     * 利用可能なコンビニリスト
     * @var array
     */
    public $convenienceStoreList = [
        '00001' => 'ローソン',
        '00002' => 'ファミリーマート',
        '00003' => 'サンクス',
        '00004' => 'サークルＫ',
        '00005' => 'ミニストップ',
        '00006' => 'デイリーヤマザキ',
        '00007' => 'セブンイレブン',
        '00008' => 'セイコーマート',
        '00009' => 'スリーエフ'
    ];

    public function __construct()
    {
        $this->db = new aafwDataBuilder();
        $this->config = aafwApplicationConfig::getInstance();
        //コンビニリスト
        $this->convenienceStoreList = $this->config->query('@gmo.GMO.convenienceStoreList');
    }

    /**
     * insert用の配列を作成
     * @param int $productId
     * @return array
     */
    private function _createInsertBaseData($productId)
    {
        return [
            //'id' => NULL,
            'product_id' => $productId,
            'salt' => $this->createSaltKey(),
            'access_code' => sha1(uniqid($productId)),
            'data' => null,
            'created_at' => date("Y-m-d H:i:s"),
            'expiration_at' => date("Y-m-d H:i:s", time() + (60 * $this->expiration))
        ];
    }

    /**
     * saltKeyの作成
     * @return string
     */
    public function createSaltKey()
    {
        return uniqid();
    }


    /**
     * 注文情報の保存
     * @param int $userId
     * @param array $data
     * @return string|null
     */
    public function save($productId = 0, $data = [])
    {
        try {
            $this->validate($data);
            if ($this->validateErrors) {
                return null;
            }
            $baseData = $this->_createInsertBaseData($productId);
            $baseData['data'] = $this->_encode($data, $baseData['salt']);
            $sql = 'INSERT INTO pre_orders ('
                . $this->_createKeys($baseData)
                . ') values (' . $this->_createKeys($baseData, '?') . ') ';
            $this->db->getBySQL($sql, [$baseData]);
            return $baseData['access_code'];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * エラーの格納
     * @param $key
     * @param $message
     */
    private function _setvalidateErrors($key, $message)
    {
        if (isset($this->validateErrors[$key])) {
            $this->validateErrors[$key] .= "\n" . $message;
            return;
        }
        $this->validateErrors[$key] = $message;
    }

    /**
     * validation
     * エラーは $this->validateErrorsに格納
     * @param array $data
     */
    public function validate($data = [])
    {
        //cpId
        $this->_checkDataRequired($data, 'product_id', '商品情報');
        //注文情報
        $this->_checkDataRequired($data, 'order', '注文情報');
        foreach ($data['order'] as $key => $item) {
            if (!is_numeric($key) || !is_numeric($item)) {
                $this->_setvalidateErrors('order', '注文内容のフォーマットが違います。再度選択してください。');
            }
        }
        $this->_checkDataRequired($data, 'zipCode1', '郵便番号');
        $this->_checkDataRequired($data, 'zipCode2', '郵便番号');
        $this->_checkDataisNumeric($data, 'zipCode1', '郵便番号');
        $this->_checkDataisNumeric($data, 'zipCode2', '郵便番号');
        $this->_checkPref($data,'prefId','都道府県');
        $this->_checkDataRequired($data, 'address1', '市区町村');
        $this->_checkDataRequired($data, 'address2', '番地');
        $this->_checkDataRequired($data, 'telNo1', '電話番号');
        $this->_checkDataisNumeric($data, 'telNo1', '電話番号');
        $this->_checkDataRequired($data, 'telNo2', '電話番号');
        $this->_checkDataisNumeric($data, 'telNo2', '電話番号');
        $this->_checkDataRequired($data, 'telNo3', '電話番号');
        $this->_checkDataisNumeric($data, 'telNo3', '電話番号');
        $this->_checkDataRequired($data, 'firstName', '名');
        $this->_checkDataRequired($data, 'lastName', '姓');
        $this->_checkDataRequired($data, 'firstNameKana', '名(かな)');
        $this->_checkDataRequired($data, 'lastNameKana', '姓(かな)');
        $this->_checkDataKana($data, 'firstNameKana', '名(かな)');
        $this->_checkDataKana($data, 'lastNameKana', '姓(かな)');
        $this->_checkDataRequired($data, 'payType', '支払い方法');
        //決済
        $this->_checkPayType($data);
        //コンビニ決済
        $this->_checkDataPayTypeConvenienceStore($data);
        //クレジット決済
        $this->_checkDataPayTypeCredit($data);
        //楽天
        $this->_checkDataPayTypeRakuten($data);
        //DBにアクセスして確認する系処理
        if (!$this->validateErrors) {
            //キャンペーンの存在チェックと公開期間
            $this->_checkDataCP($data);
            //アイテムの売り上げ可能チェック(上限設定確認）
            $this->_checkDataOrder($data);
        }
    }

    /**
     * payTypeの指定
     * @param $data
     * @return bool
     */
    private function _checkPayType($data)
    {
        if(isset($this->payTypeList[$data['payType']]))
        {
            return true;
        }
        $this->validateErrors['payType'] = '支払い方法の設定がありません';
        return false;
    }

    /**
     * かなちぇっく
     * @param array $data
     * @param string $key
     * @param string $name
     */
    private function _checkDataKana($data = [], $key = '', $name = '')
    {
        if (!isset($data[$key]) || preg_match("/^[ぁ-ゞ]+$/u", $data[$key])) {
            return true;
        }
        $this->_setvalidateErrors($key, $name . 'は、ひらがなのみご入力ください。');
        return false;
    }

    /**
     * キャンペーンの死活チェック
     * @param array $data
     */
    private function _checkDataCP($data)
    {
        if (!isset($data['cp_id']) || !$data['cp_id']) {
            $this->validateErrors['cp_id'] = '存在しないキャンペーンです。';
        }
        $sql = 'select 
					count(*) as count  
				from
					cps
				where
					id = ?id?
				AND
					(end_date = "0000-00-00 00:00:00" OR end_date > NOW())
			  AND 
				  status IN ('.Cp::STATUS_DEMO.','.Cp::STATUS_FIX.') 
				AND
					del_flg = 0;
			 	';
        $param = [['id' => $data['cp_id']]];
        $result = $this->db->getBySQL($sql, $param);
        if (isset($result[0]['count']) && $result[0]['count'] > 0) {
            return true;
        }
        $this->validateErrors['cp_id'] = 'このキャンペーンは終了したか、存在しません。';
        return false;
    }

    /**
     * 注文可能チェック
     * @param array $data
     * @return bool
     */
    private function _checkDataOrder($data)
    {
        if (!isset($data['order']) || count($data['order']) < 1) {
            //そもそも注文がない
            $this->_setvalidateErrors('order', '商品を指定してください。');
            return false;
        }
        foreach ($data['order'] as $key => $item) {
            if($item < 1 || $item > 10){
//                $this->_setvalidateErrors('selectCount', [$key => '数量を選択してください']);
                $this->_setvalidateErrors('selectCount', '数量を選択してください');
                return false;
            }
            if ($this->checkItemAvailable($key, $item) === false) {
//                $this->_setvalidateErrors('selectCount', [$key => '指定された商品は販売できません。']);
                $this->_setvalidateErrors('selectCount', '指定された商品は販売できません。');
                return false;
            }
        }
        return true;
    }

    /**
     * product_itemsが販売可能か確認する
     * @param int $productsItemId
     * @return bool
     */
    public function checkItemAvailable($productsItemId, $number)
    {
        $sql = 'SELECT
					count(*) as count 
				FROM 
					product_items 
				WHERE 
					id = ?id? 
				AND
				( 
					(stock_limited = 0)
					OR
					(stock_limited = 1 AND stock >= ?itemCount?)
				)
				';
        $param = [
            [
                'id' => $productsItemId,
                'itemCount' => $number
            ]
        ];
        $result = $this->db->getBySQL($sql, $param);
        if (isset($result[0]['count']) && $result[0]['count'] > 0) {
            return true;
        }
        return false;
    }


    /**
     * クレジット決済のデータチェック
     * @param array $data
     */
    private function _checkDataPayTypeCredit($data = [])
    {
        if(! isset($data['payType']) || $data['payType'] != self::payType_Credit)
        {
            //クレジット払い以外が指定されている。
            return true;
        }
        //必須、数字チェック
        $this->_checkDataRequired($data,'cardNumber','クレジット番号');
        $this->_checkDataisNumeric($data,'cardNumber','クレジット番号');
        $this->_checkDataRequired($data,'cardName','カード名義人');
        $this->_checkDataisAlphaNumeric($data, 'cardName', 'カード名義人');
        $this->_checkDataRequired($data,'cardExpirationMonth','有効期限(月）');
        $this->_checkDataisNumeric($data,'cardExpirationMonth','有効期限(月）');
        $this->_checkDataRequired($data,'cardExpirationYear','有効期限(年）');
        $this->_checkDataisNumeric($data,'cardExpirationYear','有効期限(年）');
        $this->_checkDataRequired($data,'securityCode','セキュリティコード');
        $this->_checkDataisNumeric($data,'securityCode','セキュリティコード');
        $this->_checkMaxLength($data, 'securityCode', 'セキュリティコード', 4);
        return true;
    }

    /**
     * コンビニ払いのデータチェック
     * @param $data
     */
    private function _checkDataPayTypeConvenienceStore($data = [])
    {
        if(! isset($data['payType']) || $data['payType'] != self::payType_Convenience)
        {
            //コンビニ払い以外が指定されている。
            return true;
        }
        $this->_checkDataRequired($data,'convenienceCode','コンビニ');
        $this->_checkDataisNumeric($data,'convenienceCode','コンビニ');
        return true;
    }

    /**
     * 楽天決済のデータチェック
     * TODO: 実装
     * @param array $data
     */
    private function _checkDataPayTypeRakuten($data = [])
    {
        if(! isset($data['payType']) || $data['payType'] != self::payType_Rakuten)
        {
            //楽天以外が指定されている。
            return true;
        }
        return true;
    }

    private function _checkDataisAlphaNumeric($data, $key, $name){
        if (!isset($data[$key]) || !$data[$key]) {
            //チェックしない
            return true;
        }
        if (preg_match("/^[a-zA-Z0-9\s]+$/", $data[$key])) {
            return true;
        }else{
            $this->_setvalidateErrors($key, $name . 'は半角英字以外入力できません。');
        }
        return false;
    }

    /**
     * 数字チェック
     * @param array $data
     * @param string $key
     * @param string $name
     * @return bool
     */
    private function _checkDataisNumeric($data, $key, $name)
    {
        if (isset($data[$key]) && is_numeric($data[$key])) {
            return true;
        }
        if (!isset($data[$key]) || !$data[$key]) {
            //チェックしない
            return true;
        }
        $this->_setvalidateErrors($key, $name . 'は半角数字以外入力できません。');
        return false;
    }

    /**
     * 必須項目かどうか確認
     * @param array $data
     * @param string $key チェックする項目のkey
     * @param string $name 項目名
     * @return bool
     */
    private function _checkDataRequired($data, $key, $name)
    {
        if (isset($data[$key]) && $data[$key]) {
            return true;
        }
        if ($key == 'payType' && (int)$data[$key] === 0) {
            //credit払いの場合0
            return true;
        }
        $this->_setvalidateErrors($key, $name . 'は必須項目です。入力がありません。');
    }

    /**
     * 注文データを取得
     * @param int $productId
     * @param string $accessCode
     * @return array
     */
    public function getDetail($productId, $accessCode)
    {
        $data = $this->getOne($productId, $accessCode);
        if (!$data) {
            return [];
        }
        $result = $this->_decode($data['data'], $data['salt']);
        if ($result) {
            return $result;
        }
        return [];
    }

    /**
     * 画面表示用に取得する
     * @param int $productId
     * @param string $accessCode
     * @return array
     */
    public function getPageViewDetail($productId, $accessCode)
    {
        $data = $this->getDetail($productId, $accessCode);
        //支払い方法
        $data['payTypeName'] = '';
        if (isset($this->payTypeList[$data['payType']])) {
            $data['payTypeName'] = $this->payTypeList[$data['payType']];
        }
        $data['convenienceName'] = '';
        if (isset($this->convenienceStoreList[$data['convenienceCode']])) {
            $data['convenienceName'] = $this->convenienceStoreList[$data['convenienceCode']];
        }
        $productsRepository = new ProductsRepository();
        $data['prefName'] = $productsRepository->getPrefList()[$data['prefId']];
        //プロダクト情報の取得
        $data['product'] = $this->_getProduct($data['product_id']);
        $data['order_items'] = $this->_getOrderItems($data);
        return $data;
    }

    /**
     * //注文情報の一覧
     * @param $data
     * @return array
     */
    private function _getOrderItems($data)
    {
        //販売ができない商品が混ざっていた場合は、エラーに追加される。
        $this->_checkDataOrder($data);
        $order = $data['order'];
        $item_ids = join(',', array_keys($order));
        if(!$item_ids){
            return [];
        }
        //アイテム一覧の取得
        $sql = 'SELECT 
					*
				FROM 
					product_items 
				WHERE 
					id in (' . $item_ids . ') 
				ORDER BY 
					display_order asc';
        $result = $this->db->getBySQL($sql);
        return $result;
    }

    /**
     * products情報の取得
     * @param int $id
     * @return mixed
     */
    private function _getProduct($id = 0)
    {
        $obj = new ProductsRepository();
        return $obj->getById($id);
    }

    /**
     * 取得したproductのitemsから注文商品を拾い出す
     * memo: 1キャンペーンに大量の商品が並ぶことは考えにくいため、配列で対応
     * 件数が増えるようであれば注文用にデータ取得
     * @param array $items
     * @param $id
     * @return array
     */
    private function _getProductItemDataByArray($items = [], $id)
    {
        foreach ($items as $key => $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
    }

    /**
     * 注文情報の復号
     * @param string $text
     * @param string $salt
     * @return array|string
     */
    private function _decode($text, $salt)
    {
        $obj = new Cipher();
        return $obj->decode($text, $salt);
    }

    /**
     * 注文の復号
     * @param array $data
     * @param string $salt
     * @return string
     */
    private function _encode($data, $salt)
    {
        $obj = new Cipher();
        return $obj->encode($data, $salt);
    }

    /**
     * 1件取得
     * @param int $productId
     * @param string $accessCode
     * @return array
     */
    public function getOne($productId, $accessCode)
    {
        if(!$accessCode){
            return [];
        }

        $this->clearOldData();
        $sql = 'SELECT 
					* 
				FROM
					pre_orders
				where 
					access_code = ?access_code? 
				AND 
					product_id = ?product_id? 
				AND 
					expiration_at > NOW() 
				ORDER BY pre_orders.id desc 
				limit 1;';
        $param = [
            [
                'access_code' => $accessCode,
                'product_id' => $productId
            ]
        ];
        $result = $this->db->getBySQL($sql, $param);
        if (isset($result[0]) && $result[0]) {
            return $result[0];
        }
        return [];
    }

    /**
     * 有効期限が切れたデータを削除処理
     * MEMO: クレジットカード情報および個人情報が保存されているため保持できない。
     * 問い合わせ対応について考える必要あるかも。
     */
    public function clearOldData()
    {
        $sql = 'delete from pre_orders where pre_orders.expiration_at <  NOW();';
        $this->db->getBySQL($sql);
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
     * 仮注文情報を削除
     * @param string $accessCode
     */
    public function removeOrder($accessCode = '')
    {
        $sql = 'delete from pre_orders '
            . ' where '
            . ' access_code="' . $this->db->escape($accessCode) . '";';
        $this->db->executeUpdate($sql);
    }

    private function _checkPref($data, $key, $name)
    {
        if($key != 'prefId'){
            return true;
        }
        if( !is_numeric($data[$key]) ){
            $this->_setvalidateErrors($key, $name . 'を選択してください');
            return false;
        }
        $prefId = (int)$data[$key];
        //都道府県が範囲外かどうか
        if( $prefId <= 0 || $prefId >= 48 ){
            $this->_setvalidateErrors($key, $name . 'を選択してください');
        }
        return true;
    }
    private function _checkMaxLength($data,$key,$name,$allowMaxLength){
        if(mb_strlen($data[$key]) > $allowMaxLength){
            $this->_setvalidateErrors($key, $name . $allowMaxLength.'は文字以下で入力してください');
        }
        return true;
    }
}
