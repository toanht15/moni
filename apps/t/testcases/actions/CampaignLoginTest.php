<?php
AAFW::import('jp.aainc.actions.user.brandco.auth.campaign_login');
AAFW::import('jp.aainc.lib.web.aafwController');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class CampaignLoginTest extends BaseTest {


    protected function setUp() {
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
    }


    public function testDoSubAction_01_checkValidUserSNSAccountTypeFail() {
        $brand = $this->entity('Brands');
        BrandInfoContainer::getInstance()->initialize($brand);
        $this->entity("BrandContracts", array("brand_id" => $brand->id, "contract_end_date" => "9999-12-31 23:59:59", "display_end_date" => "9999-12-31 23:59:59"));
        $user = $this->newUser();
        $this->entity("BrandsUsersRelations", array("brand_id" => $brand->id, "user_id" => $user->id));

        $cp_login = new campaign_login();
        $g = array('directory_name' => 'TEST', 'cp_id' => BrandTest::test_id);
        $cp_login->rewriteParams(array(), $g, array('pl_monipla_userId' => $user->id, 'pl_loginBrandIds' => array($brand->id => 1)));
        $cp_login->doSubAction();

        $this->assertEquals($cp_login->isLogin(), false);
    }

    public function testDoSubAction_02_joined() {
        $brand = $this->entity('Brands');
        $updated_brand = $this->entity('Brands', array('id' => $brand->id, 'directory_name' => 'TEST' . $brand->id));
        BrandInfoContainer::getInstance()->initialize($updated_brand);
        $cp = $this->entity("Cps", array("brand_id" => $brand->id, "type" => 2, "recruitment_note" => "", "extend_tag" => ""));
        $user = $this->newUser();
        $this->entity("CpUsers", array("cp_id" => $cp->id, "user_id" => $user->id));
        $this->entity("BrandsUsersRelations", array("brand_id" => $brand->id, "user_id" => $user->id));

        $cp_login = new campaign_login();
        $g = array('directory_name' => 'TEST'. $brand->id,
            'cp_id' => $cp->id,
            'platform' => 'Facebook');

        $s = array('pl_monipla_userId' => $user->id,
            'pl_loginBrandIds' => array($brand->id => 1),
            'pl_monipla_userInfo' => array('socialAccounts' => array('fb')));

        $cp_login->rewriteParams(array(), $g, $s);
        $cp_login->doThisFirst();

        $url = $cp_login->doSubAction();

        $this->assertEquals($cp_login->getSession('clientId'), 'Facebook');
        $this->assertEquals($cp_login->getData()['beginner_flg'], CpUser::NOT_BEGINNER_USER);
        $this->assertEquals($cp_login->getData()['cp_id'], $cp->id);
        $this->assertEquals($url, 'redirect: '.Util::rewriteUrl('messages', 'thread', array($cp->id)));
    }

    public function testDoSubAction_03_notJoin() {
        $brand = $this->entity('Brands');
        $updated_brand = $this->entity('Brands', array('id' => $brand->id, 'directory_name' => 'TEST' . $brand->id));
        BrandInfoContainer::getInstance()->initialize($updated_brand);
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $user = $this->newUser();
        $this->entity("BrandsUsersRelations", array("brand_id" => $brand->id, "user_id" => $user->id));

        $cp_login = new campaign_login();
        $g = array('directory_name' => 'TEST' . $brand->id,
            'cp_id' => $cp->id,
            'platform' => 'Facebook');

        $s = array('pl_monipla_userId' => $user->id,
            'pl_loginBrandIds' => array($brand->id => 1),
            'pl_monipla_userInfo' => array('socialAccounts' => array('fb')));

        $cp_login->rewriteParams(array(), $g, $s);
        $cp_login->doThisFirst();

        $url = $cp_login->doSubAction();

        $this->assertEquals($cp_login->getSession('clientId'), 'Facebook');
        $this->assertEquals($cp_login->getData()['beginner_flg'], CpUser::NOT_BEGINNER_USER);
        $this->assertEquals($cp_login->getData()['cp_id'], $cp->id);
        $this->assertEquals($url, 'user/brandco/auth/signup_redirect.php');
    }
}
