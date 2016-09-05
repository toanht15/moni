select COUNT(DISTINCT(user_id)) as users from `login_log_data` where login_date > ?one_month_ago?
