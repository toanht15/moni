<?php
AAFW::import ('jp.aainc.classes.services.cp_instagram_hashtags.CpInstagramHashtagService');

class CpInstagramHashtagServiceTest extends BaseTest {

    /** @var CpInstagramHashtagService $cp_instagram_hashtag_service */
    private $cp_instagram_hashtag_service;
    private $manager;

    public function setUp() {
        $this->cp_instagram_hashtag_service = aafwServiceFactory::create('CpInstagramHashtagService');
        $this->manager = new CpInstagramHashtagActionManager();
        $this->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $this->truncateAll('CpInstagramHashtagEntries');
        $this->truncateAll('InstagramHashtagUserPosts');
        $this->truncateAll('InstagramHashtagUsers');
        $this->truncateAll('CpInstagramHashtagActions');
        $this->truncateAll('CpInstagramHashtags');
        $this->executeQuery('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function test_refreshCpInstagramHashtagsByCpActionIdAndHashtags() {
        $cp_info = $this->newBrandToAction();
        $cp_actions = $this->manager->createCpActions($cp_info[2]->id, CpAction::TYPE_INSTAGRAM_HASHTAG, 1, 1);

        $property = array('cp_instagram_hashtag_action_id' => $cp_actions[1]->id,'hashtag' => 'aaa');
        $this->entity('CpInstagramHashtags', $property);
        $property = array('cp_instagram_hashtag_action_id' => $cp_actions[1]->id,'hashtag' => 'bbb');
        $this->entity('CpInstagramHashtags', $property);

        $this->cp_instagram_hashtag_service->refreshCpInstagramHashtagsByCpActionIdAndHashtags($cp_actions[1]->id, array('test2'));

        $result = $this->cp_instagram_hashtag_service->getCpInstagramHashtagsByCpInstagramHashtagActionId($cp_actions[1]->id);

        $this->assertEquals('1', count($result));
        $this->assertEquals('test2', $result->toArray()[0]->hashtag);
    }

    public function test_refreshCpInstagramHashtagsByCpActionIdAndHashtags_noData() {
        $cp_info = $this->newBrandToAction();
        $cp_actions = $this->manager->createCpActions($cp_info[2]->id, CpAction::TYPE_INSTAGRAM_HASHTAG, 1, 1);

        $this->cp_instagram_hashtag_service->refreshCpInstagramHashtagsByCpActionIdAndHashtags($cp_actions[1]->id, array('test2'));

        $result = $this->cp_instagram_hashtag_service->getCpInstagramHashtagsByCpInstagramHashtagActionId($cp_actions[1]->id);

        $this->assertEquals('1', count($result));
        $this->assertEquals('test2', $result->toArray()[0]->hashtag);
    }

    public function test_getCpInstagramHashtagsByCpInstagramHashtagActionId() {
        $cp_info = $this->newBrandToAction();
        $cp_actions = $this->manager->createCpActions($cp_info[2]->id, CpAction::TYPE_INSTAGRAM_HASHTAG, 1, 1);

        $property = array('cp_instagram_hashtag_action_id' => $cp_actions[1]->id,'hashtag' => 'aaa');
        $entity = $this->entity('CpInstagramHashtags', $property);

        $result = $this->cp_instagram_hashtag_service->getCpInstagramHashtagsByCpInstagramHashtagActionId($entity->cp_instagram_hashtag_action_id);
        $this->assertEquals($entity->toArray(), array('id' => $result->toArray()[0]->id, 'cp_instagram_hashtag_action_id' => $result->toArray()[0]->cp_instagram_hashtag_action_id, 'hashtag' => $result->toArray()[0]->hashtag));
    }

    public function test_getCpInstagramHashtagsByCpInstagramHashtagActionId_引数チェック() {
        $result = $this->cp_instagram_hashtag_service->getCpInstagramHashtagsByCpInstagramHashtagActionId(null);
        $this->assertEmpty($result);
    }

    public function test_updateByCpActionIdAndHashtag() {
        $cp_info = $this->newBrandToAction();
        $cp_actions = $this->manager->createCpActions($cp_info[2]->id, CpAction::TYPE_INSTAGRAM_HASHTAG, 1, 1);

        $entity = $this->cp_instagram_hashtag_service->saveCpInstagramHashtagByCpActionIdAndHashtag($cp_actions[1]->id, 'aaabbb');
        $this->assertEquals(
            array('cp_instagram_hashtag_action_id' => $cp_actions[1]->id, 'hashtag' => 'aaabbb'),
            array('cp_instagram_hashtag_action_id' => $entity->cp_instagram_hashtag_action_id, 'hashtag' => $entity->hashtag)
        );
    }

    public function test_getCpInstagramHashtagByCpInstagramHashtagActionIdAndHashtag() {
        $cp_info = $this->newBrandToAction();
        $cp_actions = $this->manager->createCpActions($cp_info[2]->id, CpAction::TYPE_INSTAGRAM_HASHTAG, 1, 1);

        $property = array('cp_instagram_hashtag_action_id' => $cp_actions[1]->id,'hashtag' => 'aaa');
        $entity = $this->entity('CpInstagramHashtags', $property);

        $result = $this->cp_instagram_hashtag_service->getCpInstagramHashtagByCpInstagramHashtagActionIdAndHashtag($cp_actions[1]->id, 'aaa');
        $this->assertEquals($entity->toArray(), array('id' => $result->id, 'cp_instagram_hashtag_action_id' => $result->cp_instagram_hashtag_action_id, 'hashtag' => $result->hashtag));
    }

    public function test_getCpInstagramHashtagByCpInstagramHashtagActionIdAndHashtag_noData() {
        $cp_info = $this->newBrandToAction();
        $cp_actions = $this->manager->createCpActions($cp_info[2]->id, CpAction::TYPE_INSTAGRAM_HASHTAG, 1, 1);

        $result = $this->cp_instagram_hashtag_service->getCpInstagramHashtagByCpInstagramHashtagActionIdAndHashtag($cp_actions[1]->id, 'aaa');
        $this->assertNull($result);
    }
}
