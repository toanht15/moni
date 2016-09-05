<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class QuestionTypeService extends aafwServiceBase {

    private $question_type;

    const CHOICE_ANSWER_TYPE = 1;
    const FREE_ANSWER_TYPE = 2;
    const CHOICE_IMAGE_ANSWER_TYPE = 3;
    const CHOICE_PULLDOWN_ANSWER_TYPE = 4;

    private static $question_type_text = array(
        self::CHOICE_ANSWER_TYPE => '選択式（テキスト）',
        self::FREE_ANSWER_TYPE => '自由回答',
        self::CHOICE_IMAGE_ANSWER_TYPE => '選択式（画像）',
        self::CHOICE_PULLDOWN_ANSWER_TYPE => '選択式（プルダウン）'
    );

    public function __construct() {
        $this->question_type = $this->getModel('QuestionTypes');
    }

    public static function isChoiceQuestion ($type) {
        return ($type == self::CHOICE_ANSWER_TYPE || $type == self::CHOICE_IMAGE_ANSWER_TYPE || $type == self::CHOICE_PULLDOWN_ANSWER_TYPE);
    }

    public static function getQuestionTypeText($type) {
        return self::$question_type_text[$type];
    }
}