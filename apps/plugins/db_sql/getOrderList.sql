SELECT O.id,O.gmo_payment_order_id, O.user_id,OI.delivery_id, PI.title product_title, OI.sales_count, O.zip_code1, O.zip_code2, O.pref_name,
O.address1, O.address2, O.address3,
O.tel_no1,O.tel_no2,O.tel_no3,
O.first_name,O.last_name,O.first_name_kana,O.last_name_kana,
O.order_completion_date,O.payment_completion_date,OI.delivery_flg,
OI.delivery_date,BUR.no
FROM orders O
JOIN order_items OI ON O.id = OI.order_id
JOIN product_items PI ON OI.product_item_id = PI.id
JOIN brands_users_relations BUR ON BUR.user_id = O.user_id AND BUR.brand_id = ?brand_id?
WHERE (O.payment_status = 'CAPTURE' OR O.payment_status = 'PAYSUCCESS')
AND O.order_completion_date >= ?from_order_completion_date?
AND O.order_completion_date <= ?to_order_completion_date?
AND O.payment_completion_date >= ?from_payment_completion_date?
AND O.payment_completion_date <= ?to_payment_completion_date?
AND OI.delivery_date >= ?from_delivery_completion_date?
AND OI.delivery_date <= ?to_delivery_completion_date?
AND OI.delivery_flg = ?delivery_flg?
AND O.gmo_payment_order_id = ?gmo_payment_order_id?
AND O.product_id = ?product_id?
AND BUR.no = ?user_no?
