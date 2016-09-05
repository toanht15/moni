<?php
AAFW::import ('jp.aainc.classes.services.BrandcoSocialAccountService');

class BrandcoSocialAccountServiceTest extends BaseTest {

    /** @var BrandcoSocialAccountService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create('BrandcoSocialAccountService');
    }

    public function testCreateEmptyBrandcoSocialAccount() {
        $brandcoSocialAccount = $this->target->createEmptyBrandcoSocialAccount();

        $this->assertNull($brandcoSocialAccount->id);
    }

    public function testGetBrandcoSocialAccount() {
        $newUser        = $this->newUser();
        $socialAppId    = SocialApps::PROVIDER_TWITTER;
        $this->entity('BrandcoSocialAccounts', array('user_id' => $newUser->id, 'social_app_id' => $socialAppId));

        $brandcoSocialAccount = $this->target->getBrandcoSocialAccount($newUser->id, $socialAppId);

        $this->assertEquals(array('user_id' => $newUser->id, 'social_app_id' => $socialAppId), array('user_id' => $brandcoSocialAccount->user_id, 'social_app_id' => $brandcoSocialAccount->social_app_id));
    }

    public function testSaveBrandcoSocialAccount() {
        $newUser = $this->newUser();
        $brandcoSocialAccount = $this->entity('BrandcoSocialAccounts', array('user_id' => $newUser->id));
        $brandcoSocialAccount->social_app_id = SocialApps::PROVIDER_TWITTER;

        $this->target->saveBrandcoSocialAccount($brandcoSocialAccount);

        $result = $this->findOne('BrandcoSocialAccounts', array('user_id' => $newUser->id, 'social_app_id' => SocialApps::PROVIDER_TWITTER));
        $this->assertEquals(array('user_id' => $newUser->id, 'social_app_id' => SocialApps::PROVIDER_TWITTER),
                            array('user_id' => $result->user_id, 'social_app_id' => $result->social_app_id));
    }

    public function testGetBrandcoSocialAccounts() {
        $newUser = $this->newUser();
        $this->entity('BrandcoSocialAccounts', array('user_id' => $newUser->id, 'social_app_id' => SocialApps::PROVIDER_TWITTER));
        $this->entity('BrandcoSocialAccounts', array('user_id' => $newUser->id, 'social_app_id' => SocialApps::PROVIDER_FACEBOOK));

        $brandcoSocialAccounts = $this->target->getBrandcoSocialAccounts($newUser->id);

        $this->assertCount(2, $brandcoSocialAccounts);

    }

    public function testDeleteBrandcoSocialAccount() {
        $newUser = $this->newUser();
        $this->entity('BrandcoSocialAccounts', array('user_id' => $newUser->id, 'social_app_id' => SocialApps::PROVIDER_TWITTER));

        $this->target->deleteBrandcoSocialAccount($newUser->id, SocialApps::PROVIDER_TWITTER);

        $brandcoSocialAccount = $this->findOne('BrandcoSocialAccounts', array('user_id' => $newUser->id, 'social_app_id' => SocialApps::PROVIDER_TWITTER));

        $this->assertNull($brandcoSocialAccount);
    }

    public function testDeleteBrandcoSocialAccounts() {
        $newUser = $this->newUser();
        $this->entity('BrandcoSocialAccounts', array('user_id' => $newUser->id, 'social_app_id' => SocialApps::PROVIDER_TWITTER));
        $this->entity('BrandcoSocialAccounts', array('user_id' => $newUser->id, 'social_app_id' => SocialApps::PROVIDER_FACEBOOK));

        $this->target->deleteBrandcoSocialAccounts($newUser->id);

        $brandcoSocialAccounts = $this->find('BrandcoSocialAccounts', array('user_id' => $newUser->id));

        $this->assertEquals(array(), $brandcoSocialAccounts);

    }
}