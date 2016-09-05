<?php
AAFW::import ('jp.aainc.classes.services.CpQuestionnaireService');

class CpQuestionnaireServiceTest extends BaseTest {

    /** @var  CpQuestionnaireService $target */
    private $profile_target;

    public function setUp() {
        $this->profile_target = aafwServiceFactory::create("CpQuestionnaireService", CpQuestionnaireService::TYPE_PROFILE_QUESTION);
    }

    /**
     * Profile
     */

    public function testGetQuestionMapByIds01P_whenNotExist() {
        $result = $this->profile_target->getQuestionMapByIds(array(-1, -2, -3));
        $this->assertEquals(array(), $result);
    }

    public function testGetQuestionMapByIds02P_whenOneEntityMatch() {
        $question1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $question2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $result = $this->profile_target->getQuestionMapByIds(array($question1->id));
        $this->assertTrue(isset($result[$question1->id]), "The id must be set!: result=" . json_encode($result));
    }

    /**
     * CP
     */
}
