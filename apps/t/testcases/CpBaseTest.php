<?php

abstract class CpBaseTest extends BaseTest {

    public abstract function saveAction(CpActions $cp_action, $condition);

    public abstract function joinAction(CpActions $cp_action, CpUsers $cp_user, $condition);

    public function getCpConcreteAction($store, $cp_action_id) {
        if ($store || $cp_action_id) return;

        return $this->findOne($store, array('cp_action_id' => $cp_action_id));
    }

    public function updateCpConcreteAction($store, $cp_action_id, $condition) {
        if ($store || $cp_action_id || $condition) return;

        return $this->updateEntities($store, array('cp_action_id' => $cp_action_id), $condition);
    }

    public function getCpByCpAction(CpActions $cp_action) {
        if(!$cp_action) return;

        $cp_action_group = $this->getCpActionGroupByCpAction($cp_action);
        $cp = $this->getCpByCpActionGroup($cp_action_group);
        return $cp;
    }

    public function getCpActionGroupByCpAction(CpActions $cp_action) {
        if(!$cp_action) return;

        return $this->findOne('CpActionGroups', array('id' => $cp_action->cp_action_group_id));
    }

    public function getCpByCpActionGroup(CpActionGroups $cp_action_group) {
        if(!$cp_action_group) return;

        return $this->findOne('Cps', array('id' => $cp_action_group->cp_id));
    }

    public function createBrandSocialAccountByCpAction(CpActions $cp_action, $social_app_id) {
        if (!$cp_action || !$social_app_id) return;

        $cp = $this->getCpByCpAction($cp_action);
        $user = $this->newUser();

        $brand_social_account = $this->entity(
            'BrandSocialAccounts',
            array(
                'brand_id' => $cp->brand_id,
                'user_id' => $user->id,
                'social_app_id' => $social_app_id,
            )
        );
        return $brand_social_account;
    }
}