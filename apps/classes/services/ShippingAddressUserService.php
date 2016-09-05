<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.services.UserService');

class ShippingAddressUserService extends aafwServiceBase {

    protected $shipping_address_user;
    
    public static $AddressParams = array(
        'lastName' => 'last_name',
        'firstName' => 'first_name',
        'lastNameKana' => 'last_name_kana',
        'firstNameKana' => 'first_name_kana',
        'zipCode1' => 'zip_code1',
        'zipCode2' => 'zip_code2',
        'prefId' => 'pref_id',
        'address1' => 'address1',
        'address2' => 'address2',
        'address3' => 'address3',
        'telNo1' => 'tel_no1',
        'telNo2' => 'tel_no2',
        'telNo3' => 'tel_no3'
    );

    /** @var aafwDataBuilder $data_builder  */
    private $dataBuilder;

    public function __construct() {
        $this->shipping_address_user = $this->getModel('ShippingAddressUsers');
        $this->dataBuilder = aafwDataBuilder::newBuilder();
    }

    /**
     * ユーザ情報の保存
     * @param $user_id
     * @param $shipping_address_id
     */
    public function setShippingAddressUser($cp_user_id, $cp_shipping_address_action_id, $shipping_address_info) {
        $shippingAddressUser = $this->getShippingAddressUserByCpUserId($cp_user_id);
        if($shippingAddressUser) {
            $shippingAddressUser->cp_shipping_address_action_id = $cp_shipping_address_action_id;
        }else{
            $shippingAddressUser = $this->createEmptysetShippingAddressUser();
            $shippingAddressUser->cp_user_id = $cp_user_id;
            $shippingAddressUser->cp_shipping_address_action_id = $cp_shipping_address_action_id;
        }
        return $this->updateShippingAddressUser($shippingAddressUser, $shipping_address_info);
    }

    public function updateShippingAddressUser($shippingAddressUser, $shipping_address_info) {
        foreach (self::$AddressParams as $key => $val) {
            $shippingAddressUser->$val = $shipping_address_info[$key];
        }
        return $this->shipping_address_user->save($shippingAddressUser);
    }

    public function createEmptysetShippingAddressUser() {
        return $this->shipping_address_user->createEmptyObject();
    }

    public function getShippingAddressUserByCpUserIdAndShippingAddressActionId($cp_user_id, $cp_shipping_address_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
                'cp_shipping_address_action_id' => $cp_shipping_address_action_id,
            ),
        );
        $shippingAddressUser = $this->shipping_address_user->findOne($filter);

        return $shippingAddressUser;
    }

    public function getShippingAddressUserByCpUserId($cp_user_id) {
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id
            )
        );
        $shippingAddressUser = $this->shipping_address_user->findOne($filter);
        return $shippingAddressUser;
    }

    /**
     * @param $cp_shipping_address_action_id
     * @return mixed
     */
    public function getShippingAddressUserByShippingAddressActionId($cp_shipping_address_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_shipping_address_action_id' => $cp_shipping_address_action_id,
            ),
            'order' => array(
                'name' => 'created_at'
            )
        );
        $shippingAddressUser = $this->shipping_address_user->find($filter);

        return $shippingAddressUser;
    }

    public function deleteShippingAddressUserByCpUserIdAndShippingAddressActionId($cp_user_id, $cp_shipping_address_action_id) {

        if ($cp_shipping_address_action_id || !$cp_user_id) {
            return;
        }

        $shipping_user = $this->getShippingAddressUserByCpUserIdAndShippingAddressActionId($cp_user_id, $cp_shipping_address_action_id);
        if ($shipping_user) {
            $this->shipping_address_user->deletePhysical($shipping_user);
        }
    }

    public function deletePhysicalShippingAddressUserByCpShippingAddressActionId($cp_shipping_address_action_id) {

        if (!$cp_shipping_address_action_id) {
            return;
        }

        $shipping_address_users = $this->shipping_address_user->find(array("cp_shipping_address_action_id" => $cp_shipping_address_action_id));
        if (!$shipping_address_users) {
            return;
        }

        foreach ($shipping_address_users as $shipping_address_user) {
            $this->shipping_address_user->deletePhysical($shipping_address_user);
        }
    }

    public function deletePhysicalShippingAddressUserByCpShippingAddressActionIdAndCpUserId($cp_shipping_address_action_id, $cp_user_id) {

        if (!$cp_shipping_address_action_id || !$cp_user_id) {
            return;
        }

        $shipping_address_users = $this->shipping_address_user->find(array("cp_shipping_address_action_id" => $cp_shipping_address_action_id, 'cp_user_id' => $cp_user_id));
        if (!$shipping_address_users) {
            return;
        }

        foreach ($shipping_address_users as $shipping_address_user) {
            if (!$shipping_address_user) {
                continue;
            }
            $this->shipping_address_user->deletePhysical($shipping_address_user);
        }
    }

    /**
     * ユーザIDから特定キャンペーンにて登録した住所を取得する
     * 退会している場合でも、取得を行う
     *
     * @param $cp_id
     * @param array $user_ids
     * @return array
     */
    public function getLatestAddressesInCp($cp_id, $user_ids = []) {
        if (!$cp_id || !is_array($user_ids)) return [];

        $user_id_string = '';
        if ($user_ids) {
            $user_id_string = ' and cu.user_id in (' . implode(',', $user_ids) . ') ';
        }

        $sql = "
            select
                cu.user_id,
                su.address1,
                su.address2,
                su.address3
            from
	            shipping_address_users su
            inner join
	            cp_users cu
	            on cu.id = su.cp_user_id
	            and cu.del_flg = 0
	            and cu.cp_id = ${cp_id}
	            ${user_id_string}
            where
	            su.del_flg = 0
        ";

        return $this->dataBuilder->getBySQL($sql, []);
    }
}
