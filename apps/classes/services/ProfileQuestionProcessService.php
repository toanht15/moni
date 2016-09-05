<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

/**
 * プロフィールアンケートのハードコーディング処理サービス
 * Class ProfileQuestionProcessService
 */
class ProfileQuestionProcessService extends aafwServiceBase {


    //TODO カンコーブランドのquestion_relation_idやdefault_start_yearをセットする
    //TODO ハードコーディング: カンコーブランドの３つプロフィールアンケート質問
    const KANKO_QUESTION_RELATION_ID_1 = 240;
    const KANKO_QUESTION_RELATION_ID_2 = 241;
    const KANKO_QUESTION_RELATION_ID_3 = 242;

    const DEFAULT_START_YEAR = 2014;      //TODO ハードコーディング: カンコーブランドのスタート年

    /**
     * TODO ハードコーディング
     * カンコーブランドの追加カラム
     * @return array
     */
    public function getExtendColumnForUserList() {
        return array(
            self::KANKO_QUESTION_RELATION_ID_1 => "子供（第１子）年代",
            self::KANKO_QUESTION_RELATION_ID_2 => "子供（第２子）年代",
            self::KANKO_QUESTION_RELATION_ID_3 => "子供（第３子）年代"
        );
    }

    /**
     * TODO ハードコーディング
     * カンコーブランドのプロフィールアンケートの回答結果でユーザーの子供の年代を測定する
     * @param $user_id
     * @param $brand_id
     * @param $question_relation_ids
     * @return array
     */
    public function getChildsBirthOfPeriod($user_id, $brand_id, $question_relation_ids) {
        /** @var BrandsUsersRelationService $brand_user_relation_service */
        $brands_users_relation_service = $this->getService('BrandsUsersRelationService');
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        $brands_users_relation = $brands_users_relation_service->getBrandsUsersRelation($brand_id, $user_id);

        $child_births = array();
        foreach ($question_relation_ids as $relation_id) {
            $question_choice_answer = $cp_questionnaire_service->getSingleAndMultiChoiceAnswer($brands_users_relation->id, $relation_id);

            //回答しない場合は、何も表示しない
            if (!$question_choice_answer) {
                $child_births[$relation_id] = "-";
                continue;
            }

            $question_choice_answer_current = $question_choice_answer->current();

            //回答内容を取得する
            $choice = $cp_questionnaire_service->getChoiceById($question_choice_answer_current->choice_id);

            //アンケートの回答時間
            $question_answered_time = strtotime($question_choice_answer_current->created_at);
            $question_answered_year = date("Y", $question_answered_time);
            $question_answered_month = date("m", $question_answered_time);

            //アンケートの回答でユーザーの子供の年齢に変換する
            $user_child_age = $this->getChildAgeByQuestionAnswerChoice($choice->choice_num);

            if ($user_child_age < 0) {
                $child_births[$relation_id] = "-";
                continue;
            }

            $year_of_birth_begin = $question_answered_month < 4 ? ($question_answered_year - $user_child_age - 1) : ($question_answered_year - $user_child_age);

            //子供の生まれた年代に変換する
            $period_of_birth = ($year_of_birth_begin - 1) . '年/4月〜' . ($year_of_birth_begin) . '年/3月';

            $child_births[$relation_id] = $period_of_birth;
        }

        return $child_births;
    }

    /**
     * TODO ハードコーディング
     * カンコーブランドのプロフィールアンケート質問のchoice_numから年齢に変換する
     * @param $choice_num
     * @return int
     */
    private function getChildAgeByQuestionAnswerChoice($choice_num) {
        if (!$choice_num) return -1;

        switch ($choice_num) {
            //0歳児（男の子）/ 0歳児（女の子）
            case 1:
            case 2:
                return 0;
            //1歳児（男の子）/ 1歳児（女の子）
            case 3:
            case 4:
                return 1;
            //2歳児（男の子）/ 2歳児（女の子）
            case 5:
            case 6:
                return 2;
            //3歳児（男の子）/ 3歳児（女の子）
            case 7:
            case 8:
                return 3;
            //保育・幼稚園年少（男児）/ 保育・幼稚園年少（女児）=> 4歳
            case 9:
            case 10:
                return 4;
            //保育・幼稚園年中（男児）/ 保育・幼稚園年中（女児）=> 5歳
            case 11:
            case 12:
                return 5;
            //保育・幼稚園年上（男児) / 保育・幼稚園年上（女児) => 6歳
            case 13:
            case 14:
                return 6;
            //小学校1年生（男の子）/ 小学校1年生（女の子）=> 7歳
            case 15:
            case 16:
                return 7;
            //小学校2年生（男の子）/ 小学校2年生（女の子）=> 8歳
            case 17:
            case 18:
                return 8;
            //小学校３年生（男の子）/ 小学校３年生（女の子）=> 9歳
            case 19:
            case 20:
                return 9;
            //小学校４年生（男の子）/ 小学校４年生（女の子）=> 10歳
            case 21:
            case 22:
                return 10;
            //小学校５年生（男の子）/ 小学校５年生（女の子）=> 11歳
            case 23:
            case 24:
                return 11;
            //小学校６年生（男の子）/ 小学校６年生（女の子）=> 12歳
            case 25:
            case 26:
                return 12;
            //中学校１年生（男の子）/ 中学校１年生（女の子）=> 13歳
            case 27:
            case 28:
                return 13;
            //中学校２年生（男の子）/ 中学校２年生（女の子）=> 14歳
            case 29:
            case 30:
                return 14;
            //中学校３年生（男の子）/ 中学校３年生（女の子）=> 15歳
            case 31:
            case 32:
                return 15;
            //高校１年生（男の子）/ 高校１年生（女の子）=> 16歳
            case 33:
            case 34:
                return 16;
            //高校２年生（男の子）/ 高校２年生（女の子）=> 17歳
            case 35:
            case 36:
                return 17;
            //高校３年生（男の子）/ 高校３年生（女の子）=> 18歳
            case 37:
            case 38:
                return 18;
            //高校卒業以上（男の子）/ 高校卒業以上（女の子）=> 年齢不明
            case 39:
            case 40:
            //こどもはいない => 年齢不明
            case 41:
                return -1;
        }
    }

    /**
     * TODO ハードコーディング
     * 子供の生まれた期間をファンリスト絞り込み条件に変換する
     * @param $period_from
     * @param $period_to
     * @param $question_relation_id
     * @return array
     */
    public function convertChildBirthPeriodToSearchCondition($period_from, $period_to, $question_relation_id) {
        $current_year = date("Y");
        $search_conditions = array();

        for ($year = self::DEFAULT_START_YEAR; $year <= $current_year; $year++) {
            if ($period_from > $year) {
                continue;
            }
            $condition = $this->createConditionByChildBirthYearAndAnswerYear($period_from, $period_to, $year, $question_relation_id);
            $search_conditions = array_merge($search_conditions, $condition);
        }

        //絞り込み条件がない場合は、
        if (!count($search_conditions)) {
            $search_conditions[] = array(
                'choice_ids' => array(0),
                'created_at_from' => '',
                'created_at_to' => ''
            );
        }

        return $search_conditions;
    }

    /**
     * TODO ハードコーディング
     * カンコーブランドの追加カラムの絞り込み
     * @param $child_birth_year_from
     * @param $child_birth_year_to
     * @param $answer_year
     * @param $question_relation_id
     * @return array
     */
    private function createConditionByChildBirthYearAndAnswerYear($child_birth_year_from, $child_birth_year_to, $answer_year, $question_relation_id) {
        $search_conditions = array();

        //回答日：> 04月 -> $answer_yearの学年
        $child_age_to = Util::isNullOrEmpty($child_birth_year_from) ? 18 : intval($answer_year - $child_birth_year_from - 1);
        $child_age_from = Util::isNullOrEmpty($child_birth_year_to) ? 0 : intval($answer_year - $child_birth_year_to);

        $condition = $this->generateConditionByChildAgeRange($child_age_from, $child_age_to, $answer_year, $question_relation_id);
        if (count($condition) > 0) {
            $search_conditions[] = $condition;
        }

        //回答日：< 04月 -> $answer_yearの昨年の学年 ->学年 = $answer_year - 1
        $answer_year = $answer_year - 1;
        $child_age_to = Util::isNullOrEmpty($child_birth_year_from) ? 18 : intval($answer_year - $child_birth_year_from - 1);
        $child_age_from = Util::isNullOrEmpty($child_birth_year_to) ? 0 : intval($answer_year - $child_birth_year_to);

        $condition = $this->generateConditionByChildAgeRange($child_age_from, $child_age_to, $answer_year, $question_relation_id);
        if (count($condition) > 0) {
            $search_conditions[] = $condition;
        }


        return $search_conditions;
    }

    /**
     * TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み
     * 年齢から絞り込み条件に変換する
     * @param $child_age_from
     * @param $child_age_to
     * @param $answer_year
     * @param $question_relation_id
     * @return array
     */
    private function generateConditionByChildAgeRange($child_age_from, $child_age_to, $answer_year, $question_relation_id) {

        if ($child_age_to < 0) {
            return array();
        }

        //子供の年齢を制限する (0-18)
        $child_age_to = $child_age_to > 18 ? 18 : $child_age_to;
        $child_age_from = $child_age_from > 0 ? $child_age_from : 0;


        //子供の年齢をchoice_numに変換する
        $choice_nums = array();
        for ($i = $child_age_from; $i <= $child_age_to; $i++) {
            $choice_num = $this->getAnswerChoiceNumFromChildAge($i);
            $choice_nums = array_merge($choice_nums, $choice_num);
        }

        //$choice_numsがない場合は、空に戻す
        if (!count($choice_nums)) {
            return array();
        }

        $choice_ids = $this->getChoiceIdFromQuestionRelationIdAndChoiceNum($question_relation_id, $choice_nums);

        $condition = array(
            'choice_ids'      => $choice_ids,
            'created_at_from' => $answer_year . '-04-01',
            'created_at_to'   => ($answer_year + 1) . '-03-31'
        );

        return $condition;
    }

    /**
     * TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み
     * 子供の年齢からプロフィールアンケート質問のchoice_numに変換する
     * @param $age
     * @return array
     */
    public function getAnswerChoiceNumFromChildAge($age) {
        switch ($age) {
            case 0:
                return array(1,2);
            case 1:
                return array(3,4);
            case 2:
                return array(5,6);
            case 3:
                return array(7, 8);
            case 4:
                return array(9, 10);
            case 5:
                return array(11, 12);
            case 6:
                return array(13, 14);
            case 7:
                return array(15, 16);
            case 8:
                return array(17, 18);
            case 9:
                return array(19, 20);
            case 10:
                return array(21, 22);
            case 11:
                return array(23, 24);
            case 12:
                return array(25, 26);
            case 13:
                return array(27, 28);
            case 14:
                return array(29, 30);
            case 15:
                return array(31, 32);
            case 16:
                return array(33, 34);
            case 17:
                return array(35, 36);
            case 18:
                return array(37, 38);
            default: //子供の生まれた年代は不明の場合、
                return array();
        }
    }

    /**
     * TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み
     * @param $question_relation_id
     * @param $choice_num
     * @return null
     */
    private function getChoiceIdFromQuestionRelationIdAndChoiceNum($question_relation_id, $choice_num) {
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $question = $cp_questionnaire_service->getProfileQuestionRelationsById($question_relation_id);

        $choices = $cp_questionnaire_service->getChoicesByQuestionId($question->id);

        $choice_ids = array();
        foreach ($choices as $choice) {
            if (in_array($choice->choice_num, $choice_num)) {
                $choice_ids[] = $choice->id;
            }
        }

        return array_unique($choice_ids);
    }
}