SELECT  MAX(llad.login_date) as last_login_date
  FROM login_log_admin_data llad
    LEFT JOIN users u  ON u.id = llad.user_id
  WHERE llad.del_flg = 0
  AND u.aa_flg = 0
  AND llad.brand_id = ?brand_id?