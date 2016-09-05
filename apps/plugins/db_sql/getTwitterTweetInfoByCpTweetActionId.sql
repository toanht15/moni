SELECT CU.user_id, SA.social_media_account_id as user_twitter_id, SA.name as screen_name,SA.profile_page_url,M.tweet_text,M.tweet_content_url,M.tweet_status,M.created_at,P.image_url,M.approval_status
FROM tweet_messages M
LEFT OUTER JOIN tweet_photos P ON M.id = P.tweet_message_id AND P.del_flg = 0
LEFT OUTER JOIN cp_users CU ON M.cp_user_id = CU.id
LEFT OUTER JOIN social_accounts SA ON CU.user_id = SA.user_id
WHERE M.cp_tweet_action_id = ?cp_tweet_action_id?
AND M.del_flg = 0
AND CU.del_flg = 0
AND SA.social_media_id = 3
AND SA.del_flg = 0
AND M.tweet_content_url <> ''