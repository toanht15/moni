<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SyncInstagramHashtagUserPost');

$insta = new SyncInstagramHashtagUserPost();
if (!($lock_file = Util::lockFile($insta))) return;
$insta->doProcess();
