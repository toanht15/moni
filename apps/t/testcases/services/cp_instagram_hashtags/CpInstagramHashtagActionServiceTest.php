<?php
AAFW::import('jp.aainc.classes.services.cp_instagram_hashtags.CpInstagramHashtagActionService');
AAFW::import('jp.aainc.t.helpers.adapters.InstagramHashtagHelper');

class CpInstagramHashtagActionServiceTest extends BaseTest {

    /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
    private $cp_instagram_hashtag_action_service;

    /** @var  CpInstagramHashtagService cp_instagram_hashtag_service */
    private $cp_instagram_hashtag_service;

    /** @var CpInstagramHashtagActionManager manager */
    private $manager;

    public function setUp() {
        $this->cp_instagram_hashtag_action_service = aafwServiceFactory::create('CpInstagramHashtagActionService');
        $this->cp_instagram_hashtag_service = aafwServiceFactory::create('CpInstagramHashtagService');
        $this->manager = new CpInstagramHashtagActionManager();
    }

    public function test_getCpInstagramHashtagActionByCpActionId() {
        list($brand, $cp, $cp_actio_group, $cp_action) = $this->newBrandToAction();
        $cp_actions = $this->manager->createCpActions($cp_actio_group->id, CpAction::TYPE_INSTAGRAM_HASHTAG, 1, 1);

        $cp_instagram_hashtag_action = $this->cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($cp_actions[1]->cp_action_id);
        $this->assertEquals($cp_actions[1]->id, $cp_instagram_hashtag_action->id);
    }

    public function test_saveCpInstagramHashtagAction() {
        list($brand, $cp, $cp_actio_group, $cp_action) = $this->newBrandToAction();
        $cp_actions = $this->manager->createCpActions($cp_actio_group->id, CpAction::TYPE_INSTAGRAM_HASHTAG, 1, 1);

        $cp_actions[1]->title = 'test';
        $cp_instagram_hashtag_action = $this->cp_instagram_hashtag_action_service->saveCpInstagramHashtagAction($cp_actions[1]);
        $this->assertEquals('test', $cp_instagram_hashtag_action->title);
    }

    public function test_initializeInstagramHashtagByCpId() {
        list($brand, $cp, $cp_actio_group, $cp_action) = $this->newBrandToAction();
        $this->updateEntities('CpActions', array('id' => $cp_action->id), array('type' => CpAction::TYPE_INSTAGRAM_HASHTAG));

        $cp_instagram_hashtag_action = $this->entity('CpInstagramHashtagActions', array('cp_action_id' => $cp_action->id));
        $this->entity('CpInstagramHashtags', array('cp_instagram_hashtag_action_id' => $cp_instagram_hashtag_action->id, 'hashtag' => 'aaa'));

        $this->cp_instagram_hashtag_action_service->initializeInstagramHashtagByCpId($cp->id);

        $cp_instagram_hashtag = $this->cp_instagram_hashtag_service->getCpInstagramHashtagByCpInstagramHashtagActionIdAndHashtag($cp_instagram_hashtag_action->id, 'aaa');
        $this->assertNotEquals('0', $cp_instagram_hashtag->total_media_count_start);
    }
}
