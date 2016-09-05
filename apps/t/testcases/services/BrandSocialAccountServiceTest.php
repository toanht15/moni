<?php

require_once('vendor/google/Google_Client.php');
AAFW::import ('jp.aainc.classes.services.BrandSocialAccountService');

class BrandSocialAccountServiceTest extends BaseTest {

    /** @var BrandSocialAccountService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create('BrandSocialAccountService');
        $this->truncateAll('BrandcoSocialAccounts');
    }

    public function testGetErrorMessage01_whenGoogleAuthErrorOccurred() {
        $brand = $this->entity('Brands');
        $user = $this->newUser();
        $account = $this->entity('BrandSocialAccounts', array('brand_id' => $brand->id, 'social_app_id' => SocialApps::PROVIDER_GOOGLE, 'user_id' => $user->id));
        $exp = new Google_ServiceException('TEST', 0, null, array(array('reason' => 'authError')));

        $result = $this->target->getErrorMessage($account, $exp);
        $modified_account = $this->findOne('BrandSocialAccounts', array('id' => $account->id));
        $this->assertEquals(array('TEST', BrandSocialAccounts::TOKEN_EXPIRED), array($result, $modified_account->token_expired_flg));
    }

    public function testGetErrorMessage02_whenGoogleAuthExceptionOccurred() {
        $brand = $this->entity('Brands');
        $user = $this->newUser();
        $account = $this->entity('BrandSocialAccounts', array('brand_id' => $brand->id, 'social_app_id' => SocialApps::PROVIDER_GOOGLE, 'user_id' => $user->id));
        $exp = new Google_AuthException();
        $this->setPrivateFieldValue($exp, 'message', 'TEST');

        $result = $this->target->getErrorMessage($account, $exp);
        $modified_account = $this->findOne('BrandSocialAccounts', array('id' => $account->id));
        $this->assertEquals(array('TEST', BrandSocialAccounts::TOKEN_EXPIRED), array($result, $modified_account->token_expired_flg));
    }
}