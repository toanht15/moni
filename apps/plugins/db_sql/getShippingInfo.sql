SELECT users.name, relation.no, address.last_name, address.first_name,
address.last_name_kana, address.first_name_kana, address.zip_code1,
address.zip_code2, address.pref_id, address.address1, address.address2,
address.address3, address.tel_no1, address.tel_no2, address.tel_no3

FROM shipping_address_users as address
INNER JOIN cp_users ON cp_users.id = address.cp_user_id AND cp_users.del_flg = 0
INNER JOIN users ON users.id = cp_users.user_id AND users.del_flg = 0
INNER JOIN brands_users_relations relation ON relation.user_id = cp_users.user_id
INNER JOIN cp_user_action_statuses status ON status.cp_user_id = cp_users.id ?GET_ANNOUNCED_USER?
AND status.cp_action_id = ?cp_action_id? AND status.del_flg = 0              ?GET_ANNOUNCED_USER?
WHERE relation.brand_id = ?brand_id?
AND address.cp_shipping_address_action_id = ?cp_shipping_address_action_id?  ?GET_BY_SHIPPING_ACTION?
AND users.id IN (?user_ids?)      ?GET_BY_USER_FLG?
AND relation.del_flg = 0
AND relation.withdraw_flg = 0