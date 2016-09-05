SELECT BSA.social_media_account_id, COUNT(*) action_count, BSA.social_app_id, SA.user_id uid, DCU.data_type log_type
FROM brand_social_accounts BSA
INNER JOIN twitter_streams TWS ON TWS.brand_social_account_id = BSA.id AND TWS.del_flg = 0 AND TWS.hidden_flg = 0
INNER JOIN twitter_entries TWE ON TWE.stream_id = TWS.id AND TWE.del_flg = 0
INNER JOIN detail_crawler_urls DCU ON DCU.object_id = TWE.object_id AND DCU.data_type = ?data_type? AND DCU.del_flg = 0
INNER JOIN tw_entries_users_replies TEURP ON TEURP.entry_object_id = DCU.object_id AND TEURP.del_flg = 0
INNER JOIN tw_entries_users_mentions TEUM ON TEUM.id = TEURP.mention_id AND TEUM.del_flg = 0
INNER JOIN social_accounts SA ON SA.social_media_account_id = TEUM.tw_uid AND SA.social_media_id = ?social_media_id? AND SA.del_flg = 0
WHERE BSA.del_flg = 0 AND BSA.social_app_id = ?social_app_id? AND BSA.hidden_flg = 0
GROUP BY TEUM.tw_uid,BSA.id
