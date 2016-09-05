<?php
require_once dirname(__FILE__) . '/../../config/define.php';

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class DeletePhysicalSocialAccount {

    public $logger;

    const N_LIMIT = 1000;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function doProcess() {
        $this->logger->info('Start: DeletePhysicalSocialAccount#doProcess');

        /** @var aafwDataBuilder $builder */
        $builder = aafwDataBuilder::newBuilder();
        while ($social_accounts = $builder->getBySQL("SELECT id FROM social_accounts WHERE del_flg = 1 LIMIT " . self::N_LIMIT, array())) {
            try {
                $social_account_ids = array();
                foreach ($social_accounts as $social_account) {
                    $social_account_ids[] = $social_account['id'];
                }

                $builder->executeUpdate("DELETE FROM social_accounts WHERE del_flg = 1 AND id IN (" . implode(',', $social_account_ids) . ")");
            } catch (Exception $e) {
                $this->logger->error("Error: DeletePhysicalSocialAccount#doProcess");
                $this->logger->error($e);
            }
        }

        $this->logger->info('End: DeletePhysicalSocialAccount#doProcess');
    }
}
