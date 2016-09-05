<?php
AAFW::import ('jp.aainc.classes.services.EngagementLogService');

class EngagementLogServiceTest extends BaseTest {

    /** @var  CpUserService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("EngagementLogService");
    }

    public function testGetEngagementLogByIds_whenFound() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));
        $brand_social_account = $this->entity('BrandSocialAccounts', array('brand_id' => $brand->id, 'user_id' => $user->id));
        $engagement_log = $this->entity('EngagementLogs', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'brand_social_account_id' => $brand_social_account->id));

        $result = $this->target->getEngagementLogByIds($cp_user->id, $cp_action->id, $brand_social_account->id);

        $this->assertEquals(
            array('id' => $engagement_log->id, 'cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'brand_social_account_id' => $brand_social_account->id),
            array('id' => $result->id, 'cp_user_id' => $result->cp_user_id, 'cp_action_id' => $result->cp_action_id, 'brand_social_account_id' => $result->brand_social_account_id)
        );
    }

    /**
     * @test
     */
    public function UNREAD_FLGの値が正しいこと() {
        $this->assertEquals(
            CpFacebookLikeLog::LIKE_ACTION_UNREAD,
            EngagementLog::UNREAD_FLG
        );
    }

    /**
     * @test
     */
    public function LIKED_FLGの値の値が正しいこと() {
        $this->assertEquals(
            CpFacebookLikeLog::LIKE_ACTION_EXEC,
            EngagementLog::LIKED_FLG
        );
    }

    /**
     * @test
     */
    public function PREV_LIKED_FLGの値の値が正しいこと() {
        $this->assertEquals(
            CpFacebookLikeLog::LIKE_ACTION_ALREADY,
            EngagementLog::PREV_LIKED_FLG
        );
    }

    /**
     * @test
     */
    public function SKIP_FLGの値の値が正しいこと() {
        $this->assertEquals(
            CpFacebookLikeLog::LIKE_ACTION_SKIP,
            EngagementLog::SKIP_FLG
        );
    }
}
