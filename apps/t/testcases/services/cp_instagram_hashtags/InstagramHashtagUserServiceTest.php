<?php
AAFW::import ('jp.aainc.classes.services.cp_instagram_hashtags.InstagramHashtagUserService');

class InstagramHashtagUserServiceTest extends BaseTest {

    /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
    private $instagram_hashtag_user_service;

    public function setUp() {
        $this->instagram_hashtag_user_service = aafwServiceFactory::create('InstagramHashtagUserService');
        $this->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $this->truncateAll('CpInstagramHashtagEntries');
        $this->truncateAll('InstagramHashtagUserPosts');
        $this->truncateAll('InstagramHashtagUsers');
        $this->truncateAll('CpInstagramHashtags');
        $this->truncateAll('CpInstagramHashtagActions');
        $this->executeQuery('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function test_getInstagramHashtagUsersByCpActionId_noData() {
        list($cp, $brand, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $instagram_hashtag_users = $this->instagram_hashtag_user_service->getInstagramHashtagUsersByCpActionId($cp_action);

        $this->assertEquals(array(),$instagram_hashtag_users);
    }

    public function test_getInstagramHashtagUsersByCpActionId() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));

        $instagram_hashtag_users = $this->instagram_hashtag_user_service->getInstagramHashtagUsersByCpActionId($cp_action->id)->toArray();
        $this->assertEquals($instagram_hashtag_user->id, $instagram_hashtag_users[0]->id);
    }

    public function test_getRandomInstagramHashtagUserPostsByCpActionId_承認() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));

        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));

        for ($i=0; $i<30; $i++) {
            $this->entity('InstagramHashtagUserPosts', array('instagram_hashtag_user_id' => $instagram_hashtag_user->id, 'object_id' => $i, 'approval_status' => 1));
        }

        $instagram_hashtag_user_posts = $this->instagram_hashtag_user_service->getRandomInstagramHashtagUserPostsByCpActionId($cp_action->id);

        $this->assertThat(count($instagram_hashtag_user_posts), $this->equalTo('20'));
    }

    public function test_getRandomInstagramHashtagUserPostsByCpActionId_未承認() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));

        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));

        for ($i=0; $i<30; $i++) {
            $this->entity('InstagramHashtagUserPosts', array('instagram_hashtag_user_id' => $instagram_hashtag_user->id, 'object_id' => $i, 'approval_status' => 0));
        }

        $instagram_hashtag_user_posts = $this->instagram_hashtag_user_service->getRandomInstagramHashtagUserPostsByCpActionId($cp_action->id);

        $this->assertThat(count($instagram_hashtag_user_posts), $this->equalTo('0'));
    }

    public function test_getRandomInstagramHashtagUserPostsByCpActionId_非承認() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));

        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));

        for ($i=0; $i<30; $i++) {
            $this->entity('InstagramHashtagUserPosts', array('instagram_hashtag_user_id' => $instagram_hashtag_user->id, 'object_id' => $i, 'approval_status' => 2));
        }

        $instagram_hashtag_user_posts = $this->instagram_hashtag_user_service->getRandomInstagramHashtagUserPostsByCpActionId($cp_action->id);

        $this->assertThat(count($instagram_hashtag_user_posts), $this->equalTo('0'));
    }

    public function test_executeDuplicateInstagramHashtagUserByCpActionId1() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'instagram_user_name' => 'test'));

        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'instagram_user_name' => 'test'));

        $instagram_hashtag_users = $this->instagram_hashtag_user_service->getInstagramHashtagUsersByCpActionIdAndInstagramUserName($cp_action->id, 'test');

        foreach ($instagram_hashtag_users as $instagram_hashtag_user) {
            $this->assertEquals('0', $instagram_hashtag_user->duplicate_flg);
        }
    }

    public function test_executeDuplicateInstagramHashtagUserByCpActionId2() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'instagram_user_name' => 'test'));

        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'instagram_user_name' => 'test'));

        $this->instagram_hashtag_user_service->executeDuplicateInstagramHashtagUserByCpActionId($cp_action->id);

        $instagram_hashtag_users = $this->instagram_hashtag_user_service->getInstagramHashtagUsersByCpActionIdAndInstagramUserName($cp_action->id, 'test');

        foreach ($instagram_hashtag_users as $instagram_hashtag_user) {
            $this->assertEquals('1', $instagram_hashtag_user->duplicate_flg);
        }
    }

    public function test_isValidPostTime_same_time() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));

        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));
        $instagram_hashtag_user = $this->instagram_hashtag_user_service->getInstagramHashtagUserById($instagram_hashtag_user->id);

        $instagram_hashtag_post_date_time = strtotime($instagram_hashtag_user->created_at);
        $this->assertTrue($instagram_hashtag_user->isvalidPostTime($instagram_hashtag_post_date_time));
    }

    public function test_isValidPostTime_same_time_plus_min() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));
        $instagram_hashtag_user = $this->instagram_hashtag_user_service->getInstagramHashtagUserById($instagram_hashtag_user->id);

        $instagram_hashtag_post_date_time = strtotime($instagram_hashtag_user->created_at) + 60;
        $this->assertTrue($instagram_hashtag_user->isvalidPostTime($instagram_hashtag_post_date_time));
    }


    public function test_isValidPostTime_same_time_minus_min() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));
        $instagram_hashtag_user = $this->instagram_hashtag_user_service->getInstagramHashtagUserById($instagram_hashtag_user->id);

        $instagram_hashtag_post_date_time = strtotime($instagram_hashtag_user->created_at) - 60;
        $this->assertFalse($instagram_hashtag_user->isvalidPostTime($instagram_hashtag_post_date_time));
    }

    public function test_isValidPostTime_same_time_minus_arg_null() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));

        $this->assertFalse($instagram_hashtag_user->isvalidPostTime(null));
    }

    public function test_isValidPostTime_same_time_minus_arg_string() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $instagram_hashtag_user = $this->entity('InstagramHashtagUsers', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));

        $this->assertFalse($instagram_hashtag_user->isvalidPostTime('test'));
    }
}
