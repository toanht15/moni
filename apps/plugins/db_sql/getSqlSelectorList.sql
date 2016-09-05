SELECT
*
FROM
sql_selectors
WHERE
del_flg = 0
AND status = 1
AND secure_only_flg = ?secure_only_flg?
AND hidden_flg = 0
AND title LIKE ?search_string?
