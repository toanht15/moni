<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.MailManager');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class MailQueueMailSender {

    protected $logger;

    public function __construct() {
        ini_set('memory_limit', '256M');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function doProcess() {
        /** @var aafwDataBuilder $data_builder */
        $data_builder = aafwDataBuilder::newBuilder();
        $target_mails = $data_builder->selectTargetMails(array());
        if (!$target_mails) return;

        $this->logger->info('start sending mails. : mail total=' . count($target_mails));

        $mailManager = new MailManager();
        foreach ($target_mails as $target_mail) {
            $id = $target_mail['Id'];
            try {
                $result = $data_builder->executeUpdate('BEGIN;');
                if (!$result) {
                    throw new aafwException('EXECUTION BEGIN FAILED!');
                }

                $locked = true;
                $rs = $data_builder->executeUpdate('SELECT del_flg FROM mail_queues WHERE id = '. $id . ' FOR UPDATE');
                if (!$rs) {
                    $locked = false;
                }

                $result = $data_builder->fetchResultSet($rs);
                If ($result['del_flg'] == 1) {
                    $locked = false;
                }
                if (!$locked) {
                    // Curelyの仕様的に、ここでrollbackかcommitしないと、
                    // commit/rollbackがされなくなる。
                    $result = $data_builder->executeUpdate('ROLLBACK;');
                    if (!$result) {
                        throw new aafwException('EXECUTION ROLLBACK FAILED!');
                    }
                    continue;
                }

                $target_mail['Charset'] = $mailManager->Charset;
                $target_mail['RealCharset'] = $mailManager->RealCharset;
                $mailManager->restoreFromQueue($target_mail);
                $mailManager->sendNow();

                $result = $data_builder->executeUpdate('UPDATE mail_queues SET del_flg = 1 WHERE id = ' . $id);
                if (!$result) {
                    throw new aafwException('The deletion of mail has been failed! : id=' . $id);
                }

                $result = $data_builder->executeUpdate('COMMIT;');
                if (!$result) {
                    throw new aafwException('EXECUTION COMMIT FAILED!');
                }
            } catch (Exception $e) {
                $result = $data_builder->executeUpdate('ROLLBACK;');
                if (!$result) {
                    throw new aafwException('EXECUTION ROLLBACK FAILED!');
                }
                $this->logger->error('MailQueueMailSender Error.' . $e);
            }
        }

        $this->logger->info('end sending mails.last_id='.$id);
    }
}
