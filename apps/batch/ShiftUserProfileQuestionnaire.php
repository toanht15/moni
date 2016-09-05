<?php

require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.ShiftUserProfileQuestionnaire');

$obj = new ShiftUserProfileQuestionnaire();
if (!($lock_file = Util::lockFile($obj))) return;
$obj->doProcess();
