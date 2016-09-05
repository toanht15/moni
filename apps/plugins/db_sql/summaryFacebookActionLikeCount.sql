SELECT BSA.social_media_account_id, COUNT(*) action_count, BSA.social_app_id, SA.user_id uid, DCU.data_type log_type
FROM brand_social_accounts BSA
INNER JOIN facebook_streams FBS ON FBS.brand_social_account_id = BSA.id AND FBS.del_flg = 0 AND FBS.hidden_flg = 0
INNER JOIN facebook_entries FBE ON FBE.stream_id = FBS.id AND FBE.del_flg = 0
INNER JOIN detail_crawler_urls DCU ON DCU.object_id = FBE.object_id AND DCU.data_type = ?data_type? AND DCU.del_flg = 0
INNER JOIN fb_entries_users_likes FBEUL ON FBEUL.object_id = DCU.object_id AND FBEUL.like_flg = 1 AND FBEUL.del_flg = 0
INNER JOIN social_accounts SA ON SA.social_media_account_id = FBEUL.fb_uid AND SA.social_media_id = ?social_media_id? AND SA.del_flg = 0
WHERE BSA.del_flg = 0 AND BSA.social_app_id = ?social_app_id? AND BSA.hidden_flg = 0
GROUP BY FBEUL.fb_uid,BSA.id