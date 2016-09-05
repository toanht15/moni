<?php
AAFW::import('jp.aainc.classes.brandco.api.PopularVoteExportApiManager');

$content_api = new PopularVoteExportApiManager($this->REQUEST['GET']);

header( 'Content-type: application/json; charset=UTF-8' );
$response_data = $content_api->doProgress();
echo $response_data;
exit();