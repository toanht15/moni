
select COUNT(DISTINCT(user_id)) as users 
from login_log_data 
where YEAR(login_date) = ?this_year? AND MONTH(login_date) = ?this_month?
