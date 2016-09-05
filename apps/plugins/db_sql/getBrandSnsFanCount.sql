SELECT ifnull(tmp.social_media_id, -1) AS social_media_id, count(tmp.user_id) cnt
FROM (SELECT S.social_media_id, R.user_id
      FROM brands_users_relations R
      LEFT OUTER JOIN social_accounts S ON S.user_id = R.user_id AND S.del_flg = 0
      WHERE R.del_flg = 0
      AND R.brand_id = ?brand_id? AND R.created_at
      BETWEEN ?from_date? AND ?to_date?
      AND (S.social_media_id = 1
          OR S.social_media_id = 3
          OR S.social_media_id = 4
          OR S.social_media_id = 5
          OR S.social_media_id IS NULL
          OR S.social_media_id = 6 ?social_media_gdo?
          OR S.social_media_id = 7
          OR S.social_media_id = 8
          OR S.social_media_id = 9 ?social_media_linkedin?
      )
      AND R.withdraw_flg = 0 GROUP BY S.social_media_id , R.user_id) tmp
GROUP BY tmp.social_media_id