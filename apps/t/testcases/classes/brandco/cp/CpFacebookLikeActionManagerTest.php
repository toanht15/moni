<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 15/07/22
 * Time: 10:59
 */
AAFW::import('jp.aainc.classes.brandco.cp.CpFacebookLikeActionManager');
require_once("CpActionManagerTestBase.php");

class CpFacebookLikeActionManagerTest extends CpActionManagerTestBase {

    public function testDeletePhysicalRelatedCpActionData() {

        $this->deletePhysicalRelatedCpActionData_createData(CpAction::TYPE_FACEBOOK_LIKE);

    }

    public function deletePhysicalRelatedCpActionData_testFunction($brand, $cp, $cp_action_group, $cp_action,$user, $cp_user) {

        $brand_social_account = $this->entity("BrandSocialAccounts", array("brand_id" => $brand->id, "user_id" => $user->id, "social_app_id" => SocialApps::PROVIDER_FACEBOOK));
        $engagement_log = $this->entity("EngagementLogs", array("cp_action_id" => $cp_action->id, "cp_user_id" => $cp_user->id, "brand_social_account_id" => $brand_social_account->id));

        $this->assertEquals($this->countEntities("EngagementLogs", array("id" => $engagement_log->id)), 1);

        $manager_class = new CpFacebookLikeActionManager();
        $manager_class->deletePhysicalRelatedCpActionData($cp_action);

        $this->purge("BrandSocialAccounts", $brand_social_account->id);
        $this->assertEquals($this->countEntities("EngagementLogs", array("id" => $engagement_log->id)), 0);
    }
}
