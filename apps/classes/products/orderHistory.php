<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.products.preOrder');
AAFW::import('jp.aainc.classes.products.payments.BasePayment');
AAFW::import('jp.aainc.classes.products.payments.convenienceStore');
AAFW::import('jp.aainc.classes.products.payments.credit');
AAFW::import('jp.aainc.classes.products.payments.rakuten');

class orderHistory
{
    /**
     * logger class obj
     * @var null
     */
    private $logger = null;

    /**
     * products.id
     * @var int
     */
    private $productId = 0;
    /**
     * users.id
     * @var int
     */
    private $userId = 0;

    /**
     * db object
     * @var null
     */
    public $db = null;

    public function __construct()
    {
        $this->db = new aafwDataBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * プロダクトの注文履歴　最新の1件を取得
     * @param int $userId
     * @param int $productId
     * @return array
     */
    public function getLastOrder($userId = 0, $productId = 0)
    {
        if(!$userId || !$productId){
            return [];
        }
        $sql = 'SELECT '
            . ' 
                orders.id,
                orders.user_id,
                orders.product_id,
                orders.order_access_code,
                orders.gmo_payment_order_id,
                orders.access_id,
                orders.access_pass,
                orders.rakuten_token,
                orders.payment_status,
                orders.pay_type,
                orders.pay_type_name,
                orders.convenience_code,
                orders.convenience_name,
                orders.delivery_charge,
                orders.sub_total_cost,
                orders.total_cost,
                orders.payment_completion_date,
                orders.is_cancel,
                orders.canceled_at,
                orders.first_name,
                orders.last_name,
                orders.first_name_kana,
                orders.last_name_kana,
                orders.zip_code1,
                orders.zip_code2,
                orders.pref_name,
                orders.address1,
                orders.address2,
                orders.address3,
                orders.tel_no1,
                orders.tel_no2,
                orders.tel_no3,
                orders.created_at,
                orders.updated_at,
                products.id as product_id,
                products.title as title,
                products.image_url,
                products.cp_id,
                products.cp_action_id,
                products.brand_shop_id,
                products.delivery_charge,
                products.inquiry_name,
                products.inquiry_time1,
                products.inquiry_time2,
                products.inquiry_phone
              '
            . ' FROM '
            . ' orders '
            . ' INNER JOIN '
            . ' products on (products.id = orders.product_id) '
            . ' WHERE '
            . ' orders.user_id = ?user_id? '
            . ' AND '
            . ' orders.product_id = ?product_id? '
            . ' AND '
            . ' orders.is_cancel != 1 '
            . ' AND '
            . ' orders.payment_status IS NOT NULL '
            . ' order by '
            . ' orders.id DESC '
            . ' LIMIT 1';
        $param = [
            'user_id' => $userId,
            'product_id' => $productId
        ];
        $result = $this->db->getBySQL($sql, [$param]);
        if(isset($result[0]['id']))
        {
            $items = $this->getOrderItem($result[0]['id']);
            return [
                'detail' => $result[0],
                'items' => $items
            ];
        }
       return [];
    }

    /**
     * 注文商品を取得する。
     * @param int $orderId
     * @return array
     */
    public function getOrderItem($orderId = 0)
    {
        if(!$orderId){
            return [];
        }
        $sql = 'SELECT '
            . ' * '
            . ' FROM '
            . ' order_items '
            . 'INNER JOIN '
            . ' product_items on (product_items.id = order_items.product_item_id)'
            . ' WHERE '
            . ' order_id = ?order_id? '
            . ' ORDER BY '
            . ' product_items.display_order ASC';
        $param = ['order_id' => $orderId];
        $result = $this->db->getBySQL($sql, [$param]);
        if($result)
        {
            return $result;
        }
        return [];
    }


}