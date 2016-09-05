<?php
require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.CpLostUserDelivery');

try{
    $obj = new CpLostUserDelivery();
    if (!($lock_file = Util::lockFile($obj))) return;
    $obj->doProcess();

}catch(Exception $e){
    $logger = aafwLog4phpLogger::getDefaultLogger();
    $logger->error('Cp Lost User Delivery Error');
    $logger->error($e);
}
?>