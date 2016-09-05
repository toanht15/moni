select m.id,
m.name,
m.mail_address,
m.authority,
max(login.login_date)as login_date,
m.created_at
from managers m
INNER JOIN login_log_manager_data login ON m.mail_address = m.mail_address
WHERE
m.del_flg = 0
AND m.id = ?manager_id?                             ?ID?
AND m.mail_address LIKE ?manager_email?             ?EMAIL?
AND m.name like ?manager_name?                      ?NAME?
AND m.authority = ?manager_auth?                    ?AUTH?

group by m.id
