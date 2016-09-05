<?php
require_once dirname(__FILE__) . '/../config/define.php';
AAFW::import('jp.aainc.classes.batch.SyncTweetPosts');

$sync_tweet = new SyncTweetPosts();
if (!($lock_file = Util::lockFile($sync_tweet))) return;
$sync_tweet->doProcess();
