SELECT DISTINCT
pcah1.choice_id,
pcah1.questionnaires_questions_relation_id,
pcah1.brands_users_relation_id,
pcah1.submitted_at,
pqc.choice
FROM
profile_choice_answer_histories pcah1
INNER JOIN profile_question_choices pqc ON pcah1.choice_id = pqc.id AND pqc.del_flg = 0
WHERE
pcah1.brands_users_relation_id IN (?brands_users_relation_ids?)
AND pcah1.submitted_at <= ?submitted_at?
AND NOT EXISTS (
    SELECT 'X' FROM
    profile_choice_answer_histories pcah2
    WHERE
    pcah1.questionnaires_questions_relation_id = pcah2.questionnaires_questions_relation_id
    AND pcah1.brands_users_relation_id = pcah2.brands_users_relation_id
    AND pcah1.submitted_at < pcah2.submitted_at ?SEARCH_MAX?
    AND pcah1.submitted_at > pcah2.submitted_at ?SEARCH_MIN?
    AND pcah2.submitted_at <= ?submitted_at?
    AND pcah2.del_flg = 0
)
AND pcah1.del_flg = 0
ORDER BY pcah1.brands_users_relation_id ASC