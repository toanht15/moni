<?php

class FacebookLikeCpTestHelper extends CpBaseTest {

    public function saveAction(CpActions $cp_action, $condition = null) {
        if (!$cp_action) return;

        if ($condition['cp_facebook_like_actions']) {
            $cp_concrete_action = $this->updateCpConcreteAction('CpFacebookLikeActions', $cp_action->id, $condition['cp_facebook_like_actions']);
        } else {
            $cp_concrete_action = $this->getCpConcreteAction('CpFacebookLikeActions', $cp_action->id);
        }
        if (!$cp_concrete_action) return;

        $brand_social_account = $this->createBrandSocialAccountByCpAction($cp_action, SocialApps::PROVIDER_FACEBOOK);
        $cp_facebook_like_account = $this->createCpFacebookLikeAccount($cp_action->id, $brand_social_account->id);

        return array('cp_concrete_action' => $cp_concrete_action, 'brand_social_account' => $brand_social_account, 'cp_facebook_like_account' => $cp_facebook_like_account);
    }

    public function joinAction(CpActions $cp_action, CpUsers $cp_user, $condition) {

    }

    protected function createCpFacebookLikeAccount($cp_action_id, $brand_social_account_id) {
        if (!$cp_action_id || !$brand_social_account_id) return;

        $cp_facebook_like_account = $this->entity(
            'CpFacebookLikeAccounts',
            array(
                'cp_action_id' => $cp_action_id,
                'brand_social_account_id' => $brand_social_account_id,
            )
        );

        return $cp_facebook_like_account;
    }
} 