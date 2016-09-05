<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class MailQueueService extends aafwServiceBase {

    /** @var MailQueues mail_queues */
    protected $mail_queue_store;

    /** @var aafwDataBuilder data_builder */
    protected $data_builder;

	public function __construct() {
		$this->mail_queue_store = $this->getModel('MailQueues');
        $this->data_builder = aafwDataBuilder::newBuilder();
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

    /**
     * @return \MailQueues
     */
    public function getMailQueueStore() {
        return $this->mail_queue_store;
    }

    /**
     * @param $to_address
     * @return aafwEntityContainer|array
     */
    public function getMailQueuesByToAddress($to_address) {
        $filter = array(
            'to_address' => $to_address
        );

        return $this->getMailQueueStore()->find($filter);
    }
}

