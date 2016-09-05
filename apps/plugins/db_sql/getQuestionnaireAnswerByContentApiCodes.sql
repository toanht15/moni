SELECT qua.*
FROM questionnaire_user_answers qua
    LEFT JOIN content_api_codes cac
        ON cac.cp_action_id = qua.cp_action_id AND cac.del_flg = 0
WHERE qua.del_flg = 0
    AND qua.finished_answer_id <= ?max_id?
    AND qua.approval_status = 1
    AND cac.code = ?code?