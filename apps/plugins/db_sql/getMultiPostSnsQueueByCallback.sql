SELECT social_media_type, callback_parameter, api_result
FROM multi_post_sns_queues
WHERE error_flg = 0
    AND api_result <> ''
    AND callback_function_type = ?callback_function_type?
    AND callback_parameter IN (?callback_parameters?)
