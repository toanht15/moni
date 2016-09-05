SELECT *
FROM social_accounts sa
WHERE
  sa.social_media_id = ?social_media_id?
  AND sa.social_media_account_id = ?social_media_account_id?
  AND sa.user_id = ?user_id?
