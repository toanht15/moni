<?php
AAFW::import('jp.aainc.classes.brandco.api.SnsPanelExportApiManager');

$content_api = new SnsPanelExportApiManager($this->REQUEST['GET']);

header( 'Content-type: application/json; charset=UTF-8' );
$response_data = $content_api->doProgress();
echo $response_data;
exit();