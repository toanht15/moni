SELECT
  u.name,
  bu.no,
  u.profile_image_url,
  pu.id,
  pu.photo_title,
  pu.photo_comment,
  pu.created_at,
  pu.photo_url,
  pu.approval_status,
  pe.hidden_flg
FROM
  users u INNER JOIN
  brands_users_relations bu ON bu.user_id = u.id AND bu.del_flg = 0 AND bu.withdraw_flg = 0 INNER JOIN
  cp_users cu ON cu.user_id = u.id AND cu.del_flg = 0 INNER JOIN
  photo_users pu ON pu.cp_user_id = cu.id AND pu.del_flg = 0 INNER JOIN
  photo_entries pe ON pe.photo_user_id = pu.id AND pe.del_flg = 0
WHERE
  pu.cp_action_id = ?cp_action_id?
  AND bu.brand_id = ?brand_id?
  AND pu.approval_status = ?approval_status?  ?STATUS?

ORDER BY pu.created_at desc  ?CREATED_AT_DESC?
ORDER BY cu.user_id desc     ?CP_USER_ID_DESC?
