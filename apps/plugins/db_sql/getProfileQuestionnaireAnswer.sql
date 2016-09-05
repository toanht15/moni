SELECT
    id
FROM
    profile_question_choices
WHERE
  del_flg = 0
  AND question_id = ?question_id?
  AND choice = ?choice?