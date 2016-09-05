SELECT cp_users.id user_id, address.address1, address.address2,address.address3
FROM cp_users
LEFT OUTER JOIN shipping_address_users as address ON cp_users.id = address.cp_user_id
WHERE cp_users.del_flg = 0
AND cp_users.cp_id = ?cp_id?