<?php
AAFW::import('jp.aainc.classes.brandco.cp.CpQuestionnaireActionManager');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.stores.CpQuestionnaireActions');

class CpQuestionnaireActionManagerTest extends BaseTest {

    protected $target;

    public function setUp() {
        $this->target = $this->getMock('CpQuestionnaireActionManager');
    }

    /**
     *@test
     */
    public function CpActionの作成() {

        $cp_action_group_id = '1';
        $cp_action_id = '1';
        $order_no = '1';

        $this->target = $this->getMockBuilder('CpQuestionnaireActionManager')
                            ->disableOriginalConstructor()
                            ->setMethods(array('createCpAction','createConcreteAction'))
                            ->getMock();

        $return_value1 = new CpAction();
        $return_value1->cp_action_group_id = $cp_action_group_id;
        $return_value1->type = CpAction::TYPE_QUESTIONNAIRE;
        $return_value1->status = CpAction::STATUS_DRAFT;
        $return_value1->order_no = $order_no;

        $return_value2 = new CpAction();
        $return_value2->cp_action_id = $cp_action_id;
        $return_value2->image_url = '';
        $return_value2->text = '';
        $return_value2->button_label_text = '回答する';

        $this->target->expects($this->once())
            ->method('createCpAction')
            ->will($this->returnValue($return_value1));

        $this->target->expects($this->once())
            ->method('createConcreteAction')
            ->will($this->returnValue($return_value2));

        $action = $this->target->createCpActions($cp_action_group_id, CpAction::TYPE_QUESTIONNAIRE, CpAction::STATUS_DRAFT, $order_no);
        $cp_action = $action[0];
        $questionnaire_action = $action[1];

        $this->assertEquals($cp_action->cp_action_group_id, $cp_action_group_id);
        $this->assertEquals($cp_action->status, CpAction::STATUS_DRAFT);
        $this->assertEquals($cp_action->type, CpAction::TYPE_QUESTIONNAIRE);
        $this->assertEquals($cp_action->order_no, $order_no);

        $this->assertEquals($questionnaire_action->cp_action_id, $cp_action_id);
        $this->assertEquals($questionnaire_action->image_url, '');
        $this->assertEquals($questionnaire_action->text, '');
        $this->assertEquals($questionnaire_action->button_label_text, '回答する');

    }
}