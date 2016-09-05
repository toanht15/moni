<?php
AAFW::import('jp.aainc.classes.brandco.api.PageExportApiManager');

$content_api = new PageExportApiManager($this->REQUEST['GET']);

header( 'Content-type: application/json; charset=UTF-8' );
$response_data = $content_api->doProgress();
echo $response_data;
exit();