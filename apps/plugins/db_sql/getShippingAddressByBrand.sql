SELECT relation.id user_id, address.address1, address.address2,address.address3
FROM brands_users_relations as relation
LEFT OUTER JOIN shipping_addresses as address ON relation.user_id = address.user_id
WHERE relation.del_flg = 0
AND relation.brand_id = ?brand_id?
AND relation.withdraw_flg = 0