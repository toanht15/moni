<?php
AAFW::import('jp.aainc.classes.services.MailQueueService');

class MailQueueServiceTest extends BaseTest {

    /** @var  MailQueueService $mail_queue_service */
    private $mail_queue_service;

    public function setUp() {
        $aafw_service_factory = new aafwServiceFactory();
        $this->mail_queue_service = $aafw_service_factory->create('MailQueueService');
    }

    public function test_getMailQueueStore_01() {
        $mail_queue_store = $this->mail_queue_service->getMailQueueStore();

        $this->assertInstanceOf('MailQueues', $mail_queue_store);
    }

    public function test_getMailQueuesByToAddress_正常_count_01() {
        $to_address = 'dummy@aainc.co.jp';

        $this->truncateAll('MailQueues');
        $mail_queue = $this->Entity('MailQueues', array('to_address' => $to_address));
        $mail_queues = $this->mail_queue_service->getMailQueuesByToAddress($to_address);

        $records = $mail_queues->toArray();

        $this->assertThat(count($records), $this->equalTo(1));
    }

    public function test_getMailQueuesByToAddress_正常_value_02() {
        $to_address = 'dummy@aainc.co.jp';

        $this->truncateAll('MailQueues');
        $mail_queue = $this->Entity('MailQueues', array('to_address' => $to_address));
        $mail_queues = $this->mail_queue_service->getMailQueuesByToAddress($to_address);

        $records = $mail_queues->toArray();

        $this->assertThat($records[0]->id, $this->equalTo($mail_queue->id));
    }

    public function test_getMailQueuesByToAddress_異常_03() {
        $to_address = 'dummy@aainc.co.jp';

        $this->truncateAll('MailQueues');
        $mail_queues = $this->mail_queue_service->getMailQueuesByToAddress($to_address);

        $this->assertThat(count($mail_queues), $this->equalTo(0));
    }
}
