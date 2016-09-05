SELECT
	s.cp_action_id										'cp_action_id',
	s.questionnaire_question_id				'questionnaire_question_id',
	s.question_choice_id							'question_choice_id',
	COUNT(s.brands_users_relation_id)	'n_answers'
FROM (
	SELECT
		ca.id											'cp_action_id',
		qqr.question_id						'questionnaire_question_id',
		IFNULL(qca.choice_id, 0)	'question_choice_id',
		IFNULL(qca.brands_users_relation_id, qfa.brands_users_relation_id) 'brands_users_relation_id'
		FROM cps c
	INNER JOIN brands b
		ON b.id = c.brand_id AND b.test_page = 0 AND b.del_flg = 0
	INNER JOIN cp_action_groups cag
		ON cag.cp_id = c.id AND cag.del_flg = 0
	INNER JOIN cp_actions ca
		ON ca.cp_action_group_id = cag.id AND ca.type = 5 AND ca.del_flg = 0
	INNER JOIN cp_questionnaire_actions cqa
		ON cqa.cp_action_id = ca.id AND cqa.del_flg = 0
	INNER JOIN questionnaires_questions_relations qqr
		ON qqr.cp_questionnaire_action_id = cqa.id AND qqr.del_flg = 0
	INNER JOIN questionnaire_questions qq
		ON qq.id = qqr.question_id AND qq.del_flg = 0
	LEFT OUTER JOIN question_choice_answers qca
		ON qca.question_id = qq.id AND qq.type_id != 2 AND qca.del_flg = 0
	LEFT OUTER JOIN question_free_answers qfa
		ON qfa.question_id = qq.id AND qq.type_id = 2 AND qfa.del_flg = 0
	WHERE
		c.status IN (3, 5)
		AND DATE(c.start_date) <= ?target_date?
		AND (DATE(c.end_date) >= ?target_date? OR DATE(c.end_date) = '0000-00-00')
		AND c.del_flg = 0
) s
LEFT OUTER JOIN brands_users_relations bur
	ON bur.id = s.brands_users_relation_id AND bur.del_flg = 0 AND bur.withdraw_flg = 0
GROUP BY
	cp_action_id,
	questionnaire_question_id,
	question_choice_id

