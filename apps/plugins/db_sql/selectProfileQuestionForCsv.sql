SELECT
	pqqr.id as profile_questionnaires_questions_relation_id,
	pqqr.question_id as question_id,
	pqq.question as question,
	pqq.type_id as type_id,
	pqcr.multi_answer_flg
FROM
	profile_questionnaires_questions_relations pqqr
	INNER JOIN profile_questionnaire_questions pqq ON pqqr.question_id = pqq.id AND pqq.del_flg = 0
	LEFT OUTER JOIN profile_question_choice_requirements pqcr ON pqq.id = pqcr.question_id AND pqcr.del_flg = 0
WHERE
	pqqr.brand_id = ?brand_id?
AND pqqr.del_flg = 0
ORDER BY
	pqqr.number ASC