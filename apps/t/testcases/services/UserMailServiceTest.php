<?php
AAFW::import('jp.aainc.classes.services.UserMailService');

class UserMailServiceTest extends BaseTest {

    private $t = array();

    /** @var  UserMailService $user_mail_service */
    private $user_mail_service;
    /** @var  MailQueueService $mail_queue_service */
    private $mail_queue_service;

    public function setUp() {
        $aafw_service_factory = new aafwServiceFactory();
        $this->user_mail_service = $aafw_service_factory->create('UserMailService');
        $this->mail_queue_service = $aafw_service_factory->create('MailQueueService');
    }

    public function test_send_正常_count_01() {
        $to_address = 'dummy@aainc.co.jp';
        $template_id = 'welcome_mail';
        $replace_params = array();

        $this->truncateAll('MailQueues');
        $this->user_mail_service->send($to_address, $template_id, $replace_params, null, false);

        $mail_queues = $this->mail_queue_service->getMailQueuesByToAddress($to_address);
        $records = $mail_queues->toArray();

        $this->assertThat(count($records), $this->equalTo(1));
    }

    public function test_send_正常_value_02() {
        $to_address = 'dummy@aainc.co.jp';
        $template_id = 'welcome_mail';
        $replace_params = array();

        $this->truncateAll('MailQueues');
        $this->user_mail_service->send($to_address, $template_id, $replace_params, null, false);

        $mail_queues = $this->mail_queue_service->getMailQueuesByToAddress($to_address);
        $records = $mail_queues->toArray();

        $this->assertThat($records[0]->to_address, $this->equalTo($to_address));
    }

    public function test_send_異常_toAddressが無い_03() {
        $to_address = null;
        $template_id = 'welcome_mail';
        $replace_params = array();

        $this->truncateAll('MailQueues');
        $this->user_mail_service->send($to_address, $template_id, $replace_params, null, false);
    }

    public function test_send_異常_template_idが無い_04() {
        $to_address = 'dummy@aainc.co.jp';
        $template_id = null;
        $replace_params = array();

        $this->truncateAll('MailQueues');
        $this->user_mail_service->send($to_address, $template_id, $replace_params, null, false);
    }
}
