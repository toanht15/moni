SELECT CASE WHEN I.birthday = '0000-00-00' THEN NULL
  WHEN (YEAR(CURDATE()) - YEAR(I.birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(I.birthday, 5)) < 20 THEN 1
  WHEN 20 <= (YEAR(CURDATE()) - YEAR(I.birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(I.birthday, 5)) AND (YEAR(CURDATE()) - YEAR(I.birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(I.birthday, 5)) < 30 THEN 2
  WHEN 30 <= (YEAR(CURDATE()) - YEAR(I.birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(I.birthday, 5)) AND (YEAR(CURDATE()) - YEAR(I.birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(I.birthday, 5)) < 40 THEN 3
  WHEN 40 <= (YEAR(CURDATE()) - YEAR(I.birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(I.birthday, 5)) AND (YEAR(CURDATE()) - YEAR(I.birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(I.birthday, 5)) < 50 THEN 4
  WHEN 50 <= (YEAR(CURDATE()) - YEAR(I.birthday)) - (RIGHT(CURDATE(), 5) < RIGHT(I.birthday, 5)) THEN 5 END age
  ,count(R.id) cnt
FROM user_search_info I
INNER JOIN brands_users_relations R ON I.user_id = R.user_id AND R.brand_id = ?brand_id? AND R.del_flg = 0
WHERE I.del_flg = 0
AND R.created_at BETWEEN ?from_date? AND ?to_date?
AND R.withdraw_flg = 0
GROUP BY age