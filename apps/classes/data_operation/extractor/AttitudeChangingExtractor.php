<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.data_operation.extractor.CSVExtractor');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');

/**
 * Class AttitudeChangingExtractor
 *
 * 態度変容データの抽出を担当するクラス
 */
class AttitudeChangingExtractor extends aafwObject implements CSVExtractor {

    /** @var CpQuestionnaireService */
    private $profileQuestionnaireService;

    /** @var BrandsUsersRelationService */
    private $brandsUsersRelationService;

    /** @var aafwDataBuilder */
    private $dataBuilder;

    public function __construct() {
        parent::__construct();
        $this->dataBuilder = aafwDataBuilder::newBuilder();
        $this->brandsUsersRelationService = new BrandsUsersRelationService();
        $this->profileQuestionnaireService = new CpQuestionnaireService(CpQuestionnaireService::TYPE_PROFILE_QUESTION);
    }

    /**
     * @param array $filter (brand_id, date_from, date_to)
     * @return Generator
     */
    public function getCsvData($filter) {

        $filter['date_from'] = $filter['date_from'] ?: null;
        $filter['date_to'] = $filter['date_to'] ?: date('Y-m-d H:i:s');

        $conditions = [
            'brand_id' => $filter['brand_id']
        ];

        // 対象ブランドのプロフィールアンケートの情報（自由回答 or 選択式、設問内容etc）を管理する配列
        $pqColumns = $this->dataBuilder->selectProfileQuestionForCsv($conditions);

        $number = 0;
        $pqHeaders = [];
        foreach($pqColumns as $key => $column) {

            $number++;
            $q = "Q$number. ";

            // 設問名
            $pqHeaders[] = $q . $column['question'];
            $pqHeaders[] = "{$q}回答日";
            if ($column['multi_answer_flg'] === "1") {
                // 複数回答の場合は選択肢をカラムに追加
                $choices = $this->profileQuestionnaireService->getChoicesByQuestionId($column['question_id']);
                foreach($choices as $choice) {
                    $pqHeaders[] = $q . $choice->choice;

                    // 選択肢の情報をpqColumnsに追加しておく
                    $pqColumns[$key]['choices'][] = $choice->toArray();
                }
            }
        }

        $headerColumns = [];
        $headerColumns[] = '会員No';
        // 態度変容FROM
        foreach ($pqHeaders as $header) {
            $headerColumns[] = "S " . $header;
        }
        // 態度変容TO
        foreach ($pqHeaders as $header) {
            $headerColumns[] = "E " . $header;
        }

        // ヘッダ行の返却
        yield $headerColumns;

        // ボディ行
        $page = 1;
        $brandsUsersRelations = $this->getBrandsUsersRelationsByBrandId($filter['brand_id'], $page);

        while($brandsUsersRelations) {

            $bodyRows = [];
            $buRelationIds = [];
            // とりあえず会員No列だけで行を埋める
            foreach ($brandsUsersRelations as $brandsUsersRelation) {
                $id = $brandsUsersRelation->id;
                $buRelationIds[] = $id;
                // 会員No列
                $bodyRows[$id][] = $brandsUsersRelation->no;
            }

            // 態度変容FROM
            $args = ['brands_users_relation_ids' => $buRelationIds];
            if (is_null($filter['date_from'])) {

                // date_fromが指定されない場合、date_to以下の一番最初の回答を抽出
                $args['submitted_at'] = $filter['date_to'];
                $args['SEARCH_MIN'] = '__ON__';
            } else {
                $args['submitted_at'] = $filter['date_from'];
                $args['SEARCH_MAX'] = '__ON__';
            }

            // 指定した日付以下の回答情報を抽出
            $answers = $this->dataBuilder->selectLatestProfileQuestionsInSpecifiedDate($args);
            $this->createAttitudeRowParts($pqColumns, $answers, $bodyRows);

            // 態度変容TO
            $args = [
                'brands_users_relation_ids' => $buRelationIds,
                'submitted_at' => $filter['date_to'],
                'SEARCH_MAX' => '__ON__'
            ];

            $answers = $this->dataBuilder->selectLatestProfileQuestionsInSpecifiedDate($args);
            $this->createAttitudeRowParts($pqColumns, $answers, $bodyRows);

            foreach ($bodyRows as $bodyRow) {
                yield $bodyRow;
            }

            $brandsUsersRelations = $this->getBrandsUsersRelationsByBrandId($filter['brand_id'], ++$page);
        }
    }

    private function getBrandsUsersRelationsByBrandId($brandId, $page) {

        // 一気に取るとメモリがえらいことになるのでページングしながら取得
        $exFilter =[
            'order' => 'id',
            'pager' => [
                'count' => 1000,
                'page' => $page
            ]
        ];
        return $this->brandsUsersRelationService->getBrandsUsersRelationsByBrandId($brandId, $exFilter);
    }

    private function createAttitudeRowParts($pqColumns, $answers, &$bodyRows) {

        foreach ($bodyRows as $bid => $blah) {

            $userRows = [];
            foreach($answers as $key => $answer) {

                // [ブランドユーザー][プロフィールアンケート回答]の多重配列を作成する（実際は、複数回答の場合のみが複数になる
                if ($bid === intval($answer['brands_users_relation_id'])) {

                    $qid = $answer['questionnaires_questions_relation_id'];
                    $userRows[$qid][] = $answer;

                    // 性能のため、一度読んだら潰す
                    // 並び順がbrands_users_relation_idで昇順になっていることが条件
                    unset($answers[$key]);
                    continue;
                }
                break;
            }

            $result = [];
            foreach($pqColumns as $column) {

                $qid = $column['profile_questionnaires_questions_relation_id'];
                // ユーザー回答データの中から、対象の回答の情報を抜き出す
                $userAnswerRows = $userRows[$qid];

                // 複数選択式
                $isMultipleChoice = (intval($column['type_id']) === 1 && intval($column['multi_answer_flg']) === 1);

                $choice = "";
                $submittedAt = "";

                // TODO 今んとこ自由回答形式は絶対入ってこない 回答取得SQL（selectLatestProfileQuestionsInSpecifiedDate）をUNIONする必要有り
                if (count($userAnswerRows) > 0) {

                    $userAnswerRow = $userAnswerRows[0];
                    $choice = $userAnswerRow['choice'];
                    $submittedAt = $userAnswerRow['submitted_at'];
                    if ($isMultipleChoice) {
                        // 複数選択の場合はカンマ区切りで結合したものを答えにする
                        $choice = count($userAnswerRows) ? implode(",", array_column($userAnswerRows, 'choice')) : "";
                    }
                }

                $result[] = $choice;
                $result[] = $submittedAt;

                if ($isMultipleChoice) {

                    foreach($column['choices'] as $choice) {

                        // ユーザーの選択したchoice_idだけの配列を生成
                        $userAnswerChoices = array_column($userAnswerRows, 'choice_id');
                        // 選択していたら"1"を設定
                        $result[] = in_array($choice['id'], $userAnswerChoices, true) ? "1" : "";
                    }
                }
            }

            $bodyRows[$bid] = array_merge($bodyRows[$bid], $result);
        }
    }
}