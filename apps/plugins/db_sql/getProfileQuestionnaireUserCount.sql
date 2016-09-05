SELECT count(DISTINCT A.brands_users_relation_id) cnt
FROM profile_question_choice_answers A
INNER JOIN brands_users_relations R ON R.id = A.brands_users_relation_id AND R.del_flg = 0
INNER JOIN profile_questionnaires_questions_relations QR ON QR.id = A.questionnaires_questions_relation_id AND QR.public = 1
AND R.created_at BETWEEN ?from_date? AND ?to_date?
WHERE A.questionnaires_questions_relation_id = ?relation_id? AND R.withdraw_flg = 0 AND A.del_flg = 0