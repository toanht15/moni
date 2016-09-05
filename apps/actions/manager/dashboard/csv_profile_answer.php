<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

/**
 * Class csv_profile_answer
 *
 * コーセー様のプロフィール登録情報の更新率を把握するための
 * 暫定ダウンロードプログラムです。
 * パフォーマスン等考慮していないので
 * 実行する際は、十分に注意してください。
 *
 */
class csv_profile_answer extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'csv_profile_answer';
    public $NeedOption = array();

    public $db;
    public $logger;

    const LIMIT_ROW = 1000;

    public function doThisFirst() {
        //ini_set('memory_limit', '4096M');
        // 4096でも動かなかったため、メモリ無制限に
        ini_set('memory_limit', '-1');
        // タイムアウト設定(1h)
        ini_set('max_execution_time', 3600);

        $this->db = new aafwDataBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function validate() {
        if (!$this->brand_id) {
            return false;
        }

        return true;
    }

    public function doAction() {
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->createService('BrandsUsersRelationService');
        $brands_users_relations = $brands_users_relation_service->getBrandsUsersRelationsByBrandId($this->brand_id)->toArray();

        $ans_hash = [];
        while ($relations = array_splice($brands_users_relations, 0, self::LIMIT_ROW)) {
            // NO取得
            $no_tmp = [];
            foreach ($relations as $relation) {
                $no_tmp[] = $relation->no;
            }
            $relation_no = implode(',', $no_tmp);

            $this->logger->info('user_relations_no: ' . $relation_no);

            // 回答情報取得
            $rs = $this->getProfileAnswersByNo($this->brand_id, $relation_no);
            $first_data = $this->db->fetch($rs);
            $no = $first_data['no'];
            $question_id = $first_data['id'];
            $ans = [];
            $choice = [];
            $ans[] = $first_data['question'];
            $choice[] = $first_data['choice'];
            // ['user_id' => ['question', 'answer,answer', ....]]
            while ($data = $this->db->fetch($rs)) {
                if ($no == $data['no']) {
                    if ($question_id != $data['id']) {
                        $ans[] = implode(',', $choice);
                        $ans[] = $data['question'];
                        $choice = [];
                        $question_id = $data['id'];
                    }
                    $choice[] = $data['choice'];
                    continue;
                } else {
                    $ans[] = implode(',', $choice);
                    $ans_hash[$no] = $ans;
                    // 初期化
                    $no = $data['no'];
                    $question_id = $data['id'];
                    $ans = [];
                    $choice = [];
                    $ans[] = $data['question'];
                    $choice[] = $data['choice'];
                }
            }
            if ($ans) {
                $ans[] = implode(',', $choice);
                $ans_hash[$no] = $ans;
            }

            $this->logger->info('ProfileAnswer loop done');

            // 履歴情報の取得
            $rs = $this->getProfileAnswerHistories($this->brand_id, $relation_no);
            $first_data = $this->db->fetch($rs);
            $submitted_at = $first_data['submitted_at'];
            $no = $first_data['no'];
            $question_id = $first_data['id'];
            $ans = [];
            $choice = [];
            $ans[] = $first_data['submitted_at'];
            $ans[] = $first_data['question'];
            $choice[] = $first_data['choice'];
            $days = [];
            // ['user_id' => ['question', 'answer,answer', ....]]
            while ($data = $this->db->fetch($rs)) {
                if ($no == $data['no']) {
                    if ($submitted_at != $data['submitted_at']) {
                        $ans[] = implode(',', $choice);
                        $days[] = $ans;
                        $ans = [];
                        $choice = [];
                        $ans[] = $data['submitted_at'];
                        $ans[] = $data['question'];
                        $submitted_at = $data['submitted_at'];
                        $question_id = $data['id'];
                    } else {
                        if ($question_id != $data['id']) {
                            $ans[] = implode(',', $choice);
                            $ans[] = $data['question'];
                            $choice = [];
                            $question_id = $data['id'];
                        }
                    }
                    $choice[] = $data['choice'];
                    continue;
                } else {
                    if ($ans) {
                        $ans[] = implode(',', $choice);
                        $days[] = $ans;
                    }
                    // 最新のsubmittedを削除
                    array_pop($days);
                    foreach ($days as $day) {
                        foreach ($day as $v) {
                            array_push($ans_hash[$no], $v);
                        }
                    }
                    // 初期化
                    $submitted_at = $data['submitted_at'];
                    $no = $data['no'];
                    $question_id = $data['id'];
                    $days = [];
                    $ans = [];
                    $choice = [];
                    $ans[] = $data['submitted_at'];
                    $ans[] = $data['question'];
                    $choice[] = $data['choice'];
                }
            }
            if ($ans) {
                $ans[] = implode(',', $choice);
                $days[] = $ans;
            }
            if ($days) {
                // 最新のsubmittedを削除
                array_pop($days);
                foreach ($days as $day) {
                    foreach ($day as $v) {
                        array_push($ans_hash[$no], $v);
                    }
                }
            }
            $this->logger->info('ProfileAnswerHistory Loop Done');
        }
        // CSV出力
        $csv = new CSVParser();
        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $answers = [];
        foreach ($ans_hash as $no => $values) {
            array_unshift($values, $no);
            $array_data = $csv->out(array('data' => $values), 1);
            print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        }

        exit();
    }

    private function getProfileAnswerHistories($brand_id, $relation_no) {
        $sql = "
SELECT
BUR.no,
ANS.questionnaires_questions_relation_id,
Q.id,
Q.question,
C.id choice_id,
C.choice,
DATE_FORMAT(ANS.submitted_at, '%Y/%m/%d %T') as submitted_at
FROM
profile_questionnaire_questions Q
INNER JOIN
profile_questionnaires_questions_relations R ON Q.id = R.question_id AND R.del_flg = 0
INNER JOIN
profile_question_choices C ON C.question_id = R.question_id AND C.del_flg = 0
INNER JOIN
profile_choice_answer_histories ANS ON ANS.choice_id = C.id and ANS.del_flg = 0
INNER JOIN
brands_users_relations BUR ON BUR.id = ANS.brands_users_relation_id and BUR.del_flg = 0 
WHERE
R.brand_id = ${brand_id} AND
Q.del_flg = 0 AND
BUR.no in (${relation_no}) AND
ANS.questionnaires_questions_relation_id in (6, 7, 8)
ORDER BY BUR.no, ANS.submitted_at, Q.id, C.id
";

        return $this->db->getBySQL($sql, ['__NOFETCH__']);
    }

    private function getProfileAnswersByNo($brand_id, $relation_no) {
        $sql = "
SELECT
BUR.no,
ANS.questionnaires_questions_relation_id,
Q.id,
Q.question,
C.id choice_id,
C.choice
FROM
profile_questionnaire_questions Q
INNER JOIN
profile_questionnaires_questions_relations R ON Q.id = R.question_id AND R.del_flg = 0
INNER JOIN
profile_question_choices C ON C.question_id = R.question_id AND C.del_flg = 0
INNER JOIN
profile_question_choice_answers ANS ON ANS.choice_id = C.id and ANS.del_flg = 0
INNER JOIN
brands_users_relations BUR ON BUR.id = ANS.brands_users_relation_id and BUR.del_flg = 0 
WHERE
R.brand_id = ${brand_id} AND
Q.del_flg = 0 AND
BUR.no in (${relation_no}) AND
ANS.questionnaires_questions_relation_id in (6, 7, 8)
ORDER BY BUR.no, Q.id, C.id
";

        return $this->db->getBySQL($sql, ['__NOFETCH__']);
    }
}
