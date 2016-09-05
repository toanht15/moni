<?php
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.t.helpers.adapters.UserProfileTestHelper');

class getProfileQuestionnaireUserCountTest extends BaseTest {

    public function testGetProfileQuestionnaireUserCount01() {
        $profile_helper = new UserProfileTestHelper();

        $brand = $this->entity("Brands", array('enterprise_id' => 1));

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $user4 = $this->newUser();

        $relations = $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at'=> date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at'=> date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at'=> date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user4->id,
                'brand_id' => $brand->id,
                'created_at'=> date('Y-m-d H:i:s', strtotime('2015-05-03 12:00:00'))),
        ));

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::SINGLE_ANSWER,
            'public' => 1
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $profile_helper->newProfileQuestionnaireByBrand($brand, $condition);

        $choices[2] = $this->entity('ProfileQuestionChoices',
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => '選択肢3'
            )
        );

        $this->entities('ProfileQuestionChoiceAnswers', array(
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[0]->id
            ),
            array(
                'choice_id' => $choices[1]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[0]->id
            ),
            array(
                'choice_id' => $choices[2]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[0]->id
            ),
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[1]->id
            ),
            array(
                'choice_id' => $choices[1]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[1]->id
            ),
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[2]->id
            ),
            array(
                'choice_id' => $choices[1]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[3]->id
            ),
        ));

        $condition = array(
            'relation_id' => $questionnaire_relation->id,
            'from_date' => date('Y-m-d H:i:s', strtotime('2015-04-30 00:00:00')),
            'to_date' => date('Y-m-d H:i:s', strtotime('2015-05-02 23:59:59')),
        );
        $args = array($condition, array());

        $result = $this->getProfileQuestionnaireUserCount($args[0]);

        $this->assertEquals(3, $result[0]['cnt']);
    }
}
