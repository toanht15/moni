<?php
AAFW::import('jp.aainc.classes.brandco.api.TweetExportApiManager');

$content_api = new TweetExportApiManager($this->REQUEST['GET']);

header( 'Content-type: application/json; charset=UTF-8' );
$response_data = $content_api->doProgress();
echo $response_data;
exit();