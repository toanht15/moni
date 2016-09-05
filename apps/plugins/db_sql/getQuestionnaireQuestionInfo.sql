SELECT
    qq.id q_id, qqr.number q_no, qq.type_id, qq.question, qcr.use_other_choice_flg, qcr.multi_answer_flg, qc.id qc_id, qc.choice_num, qc.choice, qc.image_url
FROM
    questionnaires_questions_relations qqr
    LEFT JOIN questionnaire_questions qq
        ON qq.id = qqr.question_id AND qq.del_flg = 0
    LEFT JOIN question_choice_requirements qcr
        ON qq.id = qcr.question_id AND qcr.del_flg = 0
    LEFT JOIN question_choices qc
        ON qq.id = qc.question_id
WHERE
    qqr.del_flg = 0
    AND qqr.cp_questionnaire_action_id = (?cp_questionnaire_action_id?)
    AND qq.id IN (?question_ids?)
ORDER BY
    qqr.number