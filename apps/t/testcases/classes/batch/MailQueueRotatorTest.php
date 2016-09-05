<?php
AAFW::import('jp.aainc.classes.batch.MailQueueRotator');

class MailQueueRotatorTest extends BaseTest {

    /** @var MailQueueRotator mail_queue_rotator */
    private $mail_queue_rotator;

    /** @var aafwDataBuilder $db */
    private $db;

    public function setUp() {
        $this->db = aafwDataBuilder::newBuilder();
        $this->mail_queue_rotator = new MailQueueRotator();
        $this->truncateAll('MailQueues');
    }

    public function test_doProcess_3日のデータは残す() {
        $date = date('Y-m-d', strtotime('-3 day'));

        $mail_queue = $this->entity('MailQueues', array('created_at' => $date . '00:00:00'));
        /** @var MailQueues $mail_queue_store */
        $mail_queue_store = $this->getModel('MailQueues');
        // 意図的にdel_flgを立てる必要あり
        $mail_queue_store->delete($mail_queue);

        $this->mail_queue_rotator->doProcess();

        $count = $this->db->getBySQL('select count(*) cnt from mail_queues');
        $this->assertEquals(1, $count[0]['cnt']);
    }

    public function test_doProcess_3日以上前のデータは削除する() {
        $date = date('Y-m-d', strtotime('-4 day'));

        $mail_queue = $this->entity('MailQueues', array('created_at' => $date . '23:59:59'));
        /** @var MailQueues $mail_queue_store */
        $mail_queue_store = $this->getModel('MailQueues');
        // 意図的にdel_flgを立てる必要あり
        $mail_queue_store->delete($mail_queue);

        $this->mail_queue_rotator->doProcess();

        $count = $this->db->getBySQL('select count(*) cnt from mail_queues');
        $this->assertEquals(0, intval($count[0]['cnt']));
    }

    public function test_doProcess_3日以上前のデータでもdel_flgが0のやつは残す() {
        $date = date('Y-m-d', strtotime('-3 day'));

        $mail_queue = $this->entity('MailQueues', array('created_at' => $date . '23:59:59'));
        $this->mail_queue_rotator->doProcess();

        $count = $this->db->getBySQL('select count(*) cnt from mail_queues');
        $this->assertEquals(1, intval($count[0]['cnt']));
    }

    public function test_getTargetDateTime() {
        $date = date('Y-m-d', strtotime('-3 day'));
        $datetime = $date . ' 00:00:00';
        $target_date_time = $this->mail_queue_rotator->getTargetDateTime();
        $this->assertEquals($datetime, $target_date_time);
    }
}