<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.BrandGlobalSettingService');

class QuestionNgWordService extends aafwServiceBase {

    private $questionNgWords;

    public function __construct() {
        $this->questionNgWords = $this->getModel("QuestionNgWords");
    }

    public function getQuestionNgWords() {
        return $this->questionNgWords->find(array());
    }

    public function getQuestionNgWordByBrands($brandId) {
        $brandGlobalSettingService = new BrandGlobalSettingService();
        $availableWords = $brandGlobalSettingService->getSettingsByNameAndBrandId(BrandGlobalSettingService::CAN_SET_NG_WORD,$brandId);
        $availableWordIds = array();
        foreach($availableWords as $availableWord) {
            $availableWordIds[] = $availableWord->content;
        }
        $filter = array(
            'id: NOT IN' => $availableWordIds,
        );
        return $this->questionNgWords->find($filter);
    }

    public function getNgWordInQuestion($question, $brandId) {
        $ngWordStr = '';
        $ngWords = $this->getQuestionNgWordByBrands($brandId);
        foreach($ngWords as $word) {
            if (strpos($question,$word->word) !== false) {
                $ngWordStr .= '「'. $word->word . '」';
            }
        }
        return $ngWordStr;
    }

    public function isNgQuestion($question, $brandId) {
        $ngWords = $this->getQuestionNgWordByBrands($brandId);
        foreach($ngWords as $word) {
            if (strpos($question,$word->word) !== false) {
                return true;
            }
        }
        return false;
    }
}