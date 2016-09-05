<?php

class MailManagerTest extends BaseTest {

    public function test_sendLater() {
        $mail_manager = new MailManager();
        $mail_manager->loadMailContent('mail_address_signup');
        $mail_manager->sendLater('dummy@aainc.co.jp');

        $mail_queues = aafwEntityStoreFactory::create('MailQueues');
        $mail_queue = $mail_queues->findOne(array('to_address' => 'dummy@aainc.co.jp'));
        $this->assertEquals('dummy@aainc.co.jp', $mail_queue->to_address);
    }

    public function test_construct() {
        $mail_params['Subject'] = 'test';
        $mail_manager = new MailManager($mail_params);
        $this->assertEquals('test', $mail_manager->Subject);
    }
}
