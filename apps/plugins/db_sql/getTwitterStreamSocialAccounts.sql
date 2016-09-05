SELECT ts.id as stream_id, bsa.social_media_account_id as twitter_id
FROM twitter_streams ts
    INNER JOIN brand_social_accounts bsa ON ts.brand_social_account_id = bsa.id
WHERE
  ts.id >= ?last_crawler_stream_id?
AND
  ts.del_flg = 0
AND
  bsa.del_flg = 0
ORDER BY ts.id ASC
