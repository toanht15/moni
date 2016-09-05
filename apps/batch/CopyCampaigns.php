<?php
require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.CopyCampaigns');

try{
    $obj = new CopyCampaigns($argv);
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();

}catch(Exception $e){
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('CloseBrand Error');
    $logger->error($e);
}
?>