SELECT
    u.optin
FROM
    monipla_account.users u
WHERE
    u.del_flg = 0
    AND u.id = ?platform_user_id?
