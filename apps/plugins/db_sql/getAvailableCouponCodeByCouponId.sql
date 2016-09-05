SELECT cc.id
FROM coupon_codes cc
WHERE
    cc.del_flg = 0
AND
    cc.coupon_id = ?coupon_id?
AND
    cc.max_num > cc.reserved_num