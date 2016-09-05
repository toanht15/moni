<?php
AAFW::import ('jp.aainc.classes.services.CpEntryProfileQuestionnaireService');

class CpEntryProfileQuestionnaireServiceTest extends BaseTest {

    public function testGetQuestionnairesByCpEntryActionId01_argIsNull() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertEquals(array(), $target->getQuestionnairesByCpActionId(null));
    }

    public function testGetQuestionnairesByCpEntryActionId02_oneMatched() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action1 = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $cp_action2 = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));

        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action1->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action2->id, 'profile_questionnaire_id' => $profile_qst2->id));

        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $result = $target->getQuestionnairesByCpActionId($cp_action1->id)->toArray();
        $this->assertEquals(array('count' => 1, 'id' => $entry_qst1->id), array('count' => count($result), 'id' => $result[0]->id));
    }

    public function testGetQuestionnairesByCpEntryActionId03_notMatched() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));

        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst2->id));

        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertEquals(array(), $target->getQuestionnairesByCpActionId(-1));
    }

    public function testGetQuestionnairesByCpEntryActionId04_twoMatched() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));

        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst2->id));

        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $result = $target->getQuestionnairesByCpActionId($cp_action->id)->toArray();
        $this->assertEquals(
            array('count' => 2, 'ids' => array($entry_qst1->id, $entry_qst2->id)),
            array('count' => count($result), 'ids' => array($result[0]->id, $result[1]->id)));
    }

    public function testGetQuestionnairesByProfileQuestionnaireId01_argIsNull() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertEquals(array(), $target->getQuestionnairesByProfileQuestionnaireId(null));
    }

    public function testGetQuestionnairesByProfileQuestionnaireId02_oneMatched() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst2->id));

        $result = $target->getQuestionnairesByProfileQuestionnaireId($profile_qst1->id)->toArray();
        $this->assertEquals(array('count' => 1, 'id' => $entry_qst1->id), array('count' => count($result), 'id' => $result[0]->id));
    }

    public function testGetQuestionnairesByProfileQuestionnaireId03_twoMatched() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $result = $target->getQuestionnairesByProfileQuestionnaireId($profile_qst1->id)->toArray();
        $this->assertEquals(
            array('count' => 2, 'ids' => array($entry_qst1->id, $entry_qst2->id)),
            array('count' => count($result), 'ids' => array($result[0]->id, $result[1]->id)));
    }

    public function testGetQuestionnairesByProfileQuestionnaireId04_notMatched() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $result = $target->getQuestionnairesByProfileQuestionnaireId(-1);
        $this->assertEquals(array(), $result);
    }

    public function testConvertQuestionnairesToMap01_argIsNull() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertEquals(array(), $target->convertQuestionnairesToMap(null));
    }

    public function testConvertQuestionnairesToMap02_convertOne() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $this->assertEquals(array($entry_qst1->profile_questionnaire_id => 1), $target->convertQuestionnairesToMap(array($entry_qst1)));
    }

    public function testConvertQuestionnairesToMap03_convertTwo() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $this->assertEquals(
            array($entry_qst1->profile_questionnaire_id => 1, $entry_qst2->profile_questionnaire_id => 1),
            $target->convertQuestionnairesToMap(array($entry_qst1, $entry_qst2)));
    }

    public function testCountQuestionnairesByCpEntryActionId01_argIsNull() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertEquals(0, $target->countQuestionnairesByCpActionId(null));
    }

    public function testCountQuestionnairesByCpEntryActionId02_countOne() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $this->assertEquals(1, $target->countQuestionnairesByCpActionId($cp_action->id));
    }

    public function testCountQuestionnairesByCpEntryActionId02_countTwo() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $this->assertEquals(2, $target->countQuestionnairesByCpActionId($cp_action->id));
    }

    public function testCountQuestionnairesByCpEntryActionId02_countZero() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $this->assertEquals(0, $target->countQuestionnairesByCpActionId(-1));
    }

    public function testHasEntryQuestionnaire01_argIsNull() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertFalse($target->hasEntryQuestionnaire(null));
    }

    public function testHasEntryQuestionnaire02_countOne() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertTrue($target->hasEntryQuestionnaire(array(1)));
    }

    public function testHasEntryQuestionnaire03_countTwo() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertTrue($target->hasEntryQuestionnaire(array(1, 2)));
    }

    public function testHasEntryQuestionnaire04_countZero() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $this->assertFalse($target->hasEntryQuestionnaire(array()));
    }

    public function testClearQuestionnairesByCpEntryActionId01_argIsNull() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        $target->clearQuestionnairesByCpActionId(null);
        $this->assertTrue(true);
    }

    public function testClearQuestionnairesByCpEntryActionId02_oneMatched() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $target->clearQuestionnairesByCpActionId($cp_action->id);

        $this->assertEquals(array(), $target->getQuestionnairesByCpActionId($cp_action->id));
    }

    public function testClearQuestionnairesByCpEntryActionId03_twoMatched() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $target->clearQuestionnairesByCpActionId($cp_action->id);

        $this->assertEquals(array(), $target->getQuestionnairesByCpActionId($cp_action->id));
    }

    public function testClearQuestionnairesByCpEntryActionId04_notMatched() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));

        $target->clearQuestionnairesByCpActionId($cp_action->id);

        $this->assertEquals(array(), $target->getQuestionnairesByCpActionId($cp_action->id));
    }

    public function testDeleteEntryQuestionnairesByProfileQuestionnaireId01_clearZero() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));

        $target->deleteEntryQuestionnairesByProfileQuestionnaireId($profile_qst1->id);
        $this->assertEquals(array(), $target->getQuestionnairesByProfileQuestionnaireId($profile_qst1->id));
    }

    public function testDeleteEntryQuestionnairesByProfileQuestionnaireId02_clearOne() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $target->deleteEntryQuestionnairesByProfileQuestionnaireId($profile_qst1->id);
        $this->assertEquals(array(), $target->getQuestionnairesByProfileQuestionnaireId($profile_qst1->id));
    }

    public function testDeleteEntryQuestionnairesByProfileQuestionnaireId03_clearTwo() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $profile_qst2 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $entry_qst1 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));
        $entry_qst2 = $this->entity('CpProfileQuestionnaires', array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id));

        $target->deleteEntryQuestionnairesByProfileQuestionnaireId($profile_qst1->id);
        $this->assertEquals(array(), $target->getQuestionnairesByProfileQuestionnaireId($profile_qst1->id));
    }

    public function testAddQuestionnaire01_firstArgIsNull() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        try {
            $target->addQuestionnaire(null, 1);
            $this->fail();
        } catch(aafwException $e) {
            echo($e->getMessage());
        }
    }

    public function testAddQuestionnaire02_secondArgIsNull() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');
        try {
            $target->addQuestionnaire(1, null);
            $this->fail();
        } catch(aafwException $e) {
            echo($e->getMessage());
        }
    }

    public function testAddQuestionnaire03_addNewOne() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));

        $result = $target->addQuestionnaire($cp_action->id, $profile_qst1->id);

        $this->assertEquals(
            array('cp_action_id' => $cp_action->id, 'profile_questionnaire_id' => $profile_qst1->id),
            array('cp_action_id' => $result->cp_action_id, 'profile_questionnaire_id' => $result->profile_questionnaire_id));
    }

    public function testCopyQuestionnaire01_copyNewOne() {
        /** @var CpEntryProfileQuestionnaireService $target */
        $target = aafwServiceFactory::create('CpEntryProfileQuestionnaireService');

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action1 = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));
        $cp_action2 = $this->entity('CpActions', array('type' => CpAction::TYPE_ENTRY, 'cp_action_group_id' => $cp_action_group->id));

        $profile_qst1 = $this->entity('ProfileQuestionnaireQuestions', array('type_id' => 1));
        $old_qst = $target->addQuestionnaire($cp_action1->id, $profile_qst1->id);

        $target->copyQuestionnaire($old_qst, $cp_action2->id);

        $this->assertEquals(1, $target->countQuestionnairesByCpActionId($cp_action2->id));
    }
}