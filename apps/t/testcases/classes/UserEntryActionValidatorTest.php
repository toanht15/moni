<?php

AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');

class UserEntryActionValidatorTest extends BaseTest {

    public function testValidate_whenPassed() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));
        $cp_user_action_status = $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));

        $target = new UserEntryActionValidator($cp_user->id, $cp_action->id);

        $target->validate();
    }
}
