<?php
AAFW::import ('jp.aainc.classes.services.SocialAccountService');

class SocialAccountServiceTest extends BaseTest {

    /** @var  SocialAccountService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("SocialAccountService");
    }

    public function testGetSocialMedia_ID() {

        $result = $this->target->getSocialMedia(SocialAccountService::SOCIAL_MEDIA_KEY_ID, SocialAccountService::SOCIAL_MEDIA_FACEBOOK);

        $this->assertEquals($result, ['id' => SocialAccountService::SOCIAL_MEDIA_FACEBOOK, 'type' => 'Facebook', 'client_id' => 'fb']);
    }

    public function testGetSocialMedia_TYPE() {

        $result = $this->target->getSocialMedia(SocialAccountService::SOCIAL_MEDIA_KEY_TYPE, 'Facebook');

        $this->assertEquals($result, ['id' => SocialAccountService::SOCIAL_MEDIA_FACEBOOK, 'type' => 'Facebook', 'client_id' => 'fb']);
    }

    public function testGetSocialMedia_CLIENT_ID() {

        $result = $this->target->getSocialMedia(SocialAccountService::SOCIAL_MEDIA_KEY_CLIENT_ID, 'fb');

        $this->assertEquals($result, ['id' => SocialAccountService::SOCIAL_MEDIA_FACEBOOK, 'type' => 'Facebook', 'client_id' => 'fb']);
    }
}