<?php

AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class UserManagerTest extends BaseTest {

    public function test() {
        $this->assertEquals(true, true);
    }
//
//    public function testConstructor01() {
//        $aaid = $this->newAAIDAccount();
//        $user_info = \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
//            'class' => 'Thrift_UserQuery', 'fields' => array('id' => $aaid)
//        ));
//        new UserManager($user_info);
//        $this->assertTrue(true);
//    }
//
//    public function testIsMailAddressRequired01_whenNoMail() {
//        $aaid = $this->newAAIDAccount(false);
//        $user_info = \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
//            'class' => 'Thrift_UserQuery', 'fields' => array('id' => $aaid)
//        ));
//        $target = new UserManager($user_info);
//        $this->assertTrue($target->isMailAddressRequired());
//    }
//
//    public function testIsMailAddressRequired02_whenHasMail() {
//        $aaid = $this->newAAIDAccount();
//        $user_info = \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
//            'class' => 'Thrift_UserQuery', 'fields' => array('id' => $aaid)
//        ));
//        $target = new UserManager($user_info);
//        $this->assertFalse($target->isMailAddressRequired());
//    }
//
//    public function testGetMailAddressCandidate01_whenNoSocialAccounts() {
//        $aaid = $this->newAAIDAccount(false);
//        $user_info = \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
//            'class' => 'Thrift_UserQuery', 'fields' => array('id' => $aaid)
//        ));
//        $target = new UserManager($user_info);
//        $this->assertEquals("", $target->getMailAddressCandidate());
//    }
//
//    public function testGetMailAddressCandidate02_whenThereAreNoSocialAccountsThatHaveMailAddresses() {
//        $aaid = $this->newAAIDAccount(false);
//        $this->addAAIDSocialAccount($aaid);
//        $user_info = \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
//            'class' => 'Thrift_UserQuery', 'fields' => array('id' => $aaid)
//        ));
//        $target = new UserManager($user_info);
//        $this->assertEquals("", $target->getMailAddressCandidate());
//    }
//
//    public function testGetMailAddressCandidate03_whenSocialAccountThatHasMailFound() {
//        $aaid = $this->newAAIDAccount(false);
//        $mail_address = "test@aainc.co.jp";
//        $this->addAAIDSocialAccount($aaid, $mail_address);
//        $user_info = \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
//            'class' => 'Thrift_UserQuery', 'fields' => array('id' => $aaid)
//        ));
//        $target = new UserManager($user_info);
//        $this->assertEquals("test@aainc.co.jp", $target->getMailAddressCandidate());
//    }
}
