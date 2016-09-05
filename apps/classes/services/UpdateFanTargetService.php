<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');
AAFW::import('jp.aainc.classes.util.AddressChecker');
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class UpdateFanTargetService extends aafwServiceBase {

    public function __construct() {
        $this->service_factory = new aafwServiceFactory();
        $this->data_builder = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * 自動抽選対象ユーザの住所重複をチェック
     *
     * @return array 住所重複しているユーザIDの配列
     */
    public function getUserIdsWithDuplicatedAddress($reservationId, $cpId, $actionId, $searchQuery) {
        // 対象ユーザIDを取得
        $userIds = $this->getUserIdsForRandom($searchQuery);
        if (!is_array($userIds)) {
            $userIds = [];
        }
        // 登録済みのユーザIDを取得
        $registerdUserIds = $this->getResigterdUserIds($reservationId, $cpId, $actionId);
        if (!is_array($registerdUserIds)) {
            $registerdUserIds = [];
        }

        $targetUserIds = array_unique(array_merge($userIds, $registerdUserIds));

        /** @var ShippingAddressUserService $shipping_address_user_service */
        $shipping_address_user_service = $this->service_factory->create('ShippingAddressUserService');
        $addresses = $shipping_address_user_service->getLatestAddressesInCp($cpId, $targetUserIds);

        // 住所の重複チェック
        $addressChecker = new AddressChecker();
        $dupli = $addressChecker->checkDuplicate($addresses);
        $dupliUserIds = [];
        foreach ($dupli as $address => $value) {
            $dupliUserIds = array_merge($dupliUserIds, $value);
        }

        // 登録済みのユーザIDを除外して返却する
        return array_filter($dupliUserIds, function ($value) {
            return !in_array($value, $registerdUserIds);
        });
    }

    public function getUserIdsForRandom($searchQuery) {
        $fans = "SELECT u.user_id FROM (${searchQuery}) u";

        $result = $this->data_builder->getBySQL($fans, ['__NOFETCH__']);
        $userIds = [];
        while ($data = $this->data_builder->fetch($result)) {
            $userIds[] = $data['user_id'];
        }

        return $userIds;
    }

    public function getResigterdUserIds($reservationId, $cpId, $actionId) {
        $fans = "
            SELECT c1.user_id FROM cp_user_action_messages m
            INNER JOIN cp_users c1 ON m.cp_user_id = c1.id
            WHERE m.cp_action_id = ${actionId} AND c1.cp_id = ${cpId}  AND m.del_flg = 0 AND c1.del_flg = 0
            UNION
            SELECT t.user_id FROM cp_message_delivery_targets t
            WHERE t.cp_message_delivery_reservation_id = ${reservationId}  AND t.del_flg = 0
        ";

        $result = $this->data_builder->getBySQL($fans, ['__NOFETCH__']);
        while ($data = $this->data_builder->fetch($result)) {
            $userIds[] = $data['user_id'];
        }

        return $userIds;
    }
}
