SELECT
    br.id 'relation_id',
    br.no 'no',
    br.optin_flg 'optin_flg',
    br.last_login_date 'last_login_date',
    br.withdraw_flg 'withdraw_flg',
    br.created_at 'created_at',
    br.updated_at 'updated_at',
    b.id 'brand_id',
    b.name 'brand_name',
    b.directory_name 'directory_name',
    u.id 'user_id',
    u.monipla_user_id 'monipla_user_id'
FROM
    brands_users_relations br
INNER JOIN
    brands b
    ON b.id = br.brand_id
    AND b.del_flg = 0
INNER JOIN
    users u
    ON u.id = br.user_id
    AND u.monipla_user_id = ?platform_user_id?
WHERE
    b.test_page = 0
