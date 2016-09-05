SELECT
    count(brands_users_relations.id) as mau
FROM
    brands_users_relations
INNER JOIN users ON brands_users_relations.user_id = users.id
WHERE
 brands_users_relations.del_flg = 0
 AND brands_users_relations.last_login_date >= ?created_at_start?
 AND brands_users_relations.last_login_date <= ?created_at_end?
 AND brands_users_relations.brand_id = ?brand_id?                  ?BRAND_KPI?