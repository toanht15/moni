<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

/**
 * キャンペーン決済に紐づくプロダクト情報
 * Class ProductsRepository
 */
class ProductsRepository
{

	public $db;

	public function __construct()
	{
		$this->db = new aafwDataBuilder();
	}

	public function getById($id)
	{
		if(!$id){
			return [];
		}
		$sql = 'SELECT 
 					brand_shops.gmo_shop_id as shopID,
 					brand_shops.gmo_shop_pass as shopPass,
 					brand_sites.gmo_site_id as siteID,
 					brand_sites.gmo_site_pass as sitePass,
 					products.id,
 					products.title,
 					products.image_url,
 					products.cp_id,
 					products.cp_action_id,
 					products.delivery_charge,
 					products.inquiry_name,
 					products.inquiry_time1,
 					products.inquiry_time2,
 					products.inquiry_phone,
 					products.created_at,
 					products.updated_at,
 					cps.status as cp_status,
 					cps.created_at as cp_created_at
				FROM products 
				INNER join 
					brand_shops on products.brand_shop_id = brand_shops.id
				INNER JOIN 
					  brand_sites on brand_shops.brand_site_id = brand_sites.id
				INNER JOIN
					cps on cps.id = products.cp_id
				WHERE 
					products.id = ?id?
				AND
					(cps.end_date > NOW() || cps.end_date =\'0000-00-00 00:00:00\')
				AND
				  cps.status IN ('.Cp::STATUS_DEMO.','.Cp::STATUS_FIX.') 
				AND 
					cps.del_flg = 0
				';
		$param = [['id' => $id]];
		$result = $this->sql($sql, $param);
		if (isset($result[0])) {
			return $result[0];
		}
		return [];
	}

	/**
	 * キャンペーンIDからproducts.idを取得する
	 * @param int $cpId
	 * @return int
	 */
	public function getIdFromCpId($cpId)
	{
		$sql = 'SELECT 
 					brand_shops.gmo_shop_id as shopID,
 					brand_shops.gmo_shop_pass as shopPass,
 					brand_sites.gmo_site_id as siteID,
 					brand_sites.gmo_site_pass as sitePass,
 					products.id,
 					products.title,
 					products.image_url,
 					products.cp_id,
 					products.delivery_charge,
 					products.created_at,
 					products.updated_at
				FROM products 
				inner join 
					brand_shops on products.brand_shop_id = brand_shops.id
				inner JOIN 
					  brand_sites on brand_shops.brand_site_id = brand_sites.id
					WHERE products.cp_id = ?cp_id?';
		$param = [['cp_id' => $cpId]];
		$result = $this->sql($sql, $param);
		if (isset($result[0])) {
			return $result[0];
		}
		return [];
	}

	/**
	 * 表示用データの取得
	 * @param int $id
	 * @return array
	 */
	public function getDetail($id = 0)
	{
		$products = $this->getById($id);
		if (!$products) {
			return [];
		}
		$items = $this->getItemList($id);
		$data = [
			'detail' => $products,
			'items' => $items,
		];
		return $data;

	}

	/**
	 * getBySQLのショートカット
	 * @param string $sql
	 * @param array $param
	 * @return array
	 */
	private function sql($sql = '', $param = [])
	{
		$result = $this->db->getBySQL($sql, $param);
		return $result;
	}

	/**
	 * アイテム一覧を取得
	 * @param int $id
	 * @return array
	 */
	public function getItemList($id = 0)
	{
		$sql = 'SELECT 
					* 
				FROM 
					product_items 
				WHERE 
					product_id=?id? 
				ORDER BY 
					display_order asc';
		$param = [['id' => $id]];
		$result = $this->db->getBySQL($sql, $param);
		if ($result) {
			return $result;
		}
		return [];
	}

	/**
	 * アイテム情報を1件取得する
	 * @param int $itemId
	 * @return array
	 */
	public function getItem($itemId = 0)
	{
		$sql = 'SELECT 
					* 
				FROM 
					product_items 
				WHERE 
					id=?id? 
				ORDER BY 
					display_order asc';
		$param = [['id' => $itemId]];
		$result = $this->db->getBySQL($sql, $param);
		if (isset($result[0])) {
			return $result[0];
		}
		return [];
	}

	public function getPrefList()
	{
		return [
			'1' => '北海道',
			'2' => '青森県',
			'3' => '岩手県',
			'4' => '宮城県',
			'5' => '秋田県',
			'6' => '山形県',
			'7' => '福島県',
			'8' => '茨城県',
			'9' => '栃木県',
			'10' => '群馬県',
			'11' => '埼玉県',
			'12' => '千葉県',
			'13' => '東京都',
			'14' => '神奈川県',
			'15' => '新潟県',
			'16' => '富山県',
			'17' => '石川県',
			'18' => '福井県',
			'19' => '山梨県',
			'20' => '長野県',
			'21' => '岐阜県',
			'22' => '静岡県',
			'23' => '愛知県',
			'24' => '三重県',
			'25' => '滋賀県',
			'26' => '京都府',
			'27' => '大阪府',
			'28' => '兵庫県',
			'29' => '奈良県',
			'30' => '和歌山県',
			'31' => '鳥取県',
			'32' => '島根県',
			'33' => '岡山県',
			'34' => '広島県',
			'35' => '山口県',
			'36' => '徳島県',
			'37' => '香川県',
			'38' => '愛媛県',
			'39' => '高知県',
			'40' => '福岡県',
			'41' => '佐賀県',
			'42' => '長崎県',
			'43' => '熊本県',
			'44' => '大分県',
			'45' => '宮崎県',
			'46' => '鹿児島県',
			'47' => '沖縄県'
		];
	}
}
