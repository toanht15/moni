SELECT C.id choice_id, count(A.id) cnt
FROM profile_question_choices C
INNER JOIN profile_question_choice_answers A ON C.id = A.choice_id AND A.del_flg = 0
INNER JOIN profile_questionnaires_questions_relations QR ON QR.id = A.questionnaires_questions_relation_id
INNER JOIN brands_users_relations R ON R.id = A.brands_users_relation_id AND R.del_flg = 0
AND R.created_at BETWEEN ?from_date? AND ?to_date?
WHERE C.question_id = ?question_id? AND R.withdraw_flg = 0 AND C.del_flg = 0 GROUP BY C.id