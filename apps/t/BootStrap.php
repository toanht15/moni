<?php
$_SERVER = array('HTTP_HOST' => 'brandcotest.com');
require_once __DIR__ . '/../config/define.php';
require_once __DIR__ . "/testcases/BaseTest.php";

ini_set('memory_limit', '1024M');
aafwApplicationConfig::getInstance()->loadYAML(AAFW_DIR . '/t/test_files/config/app.yml');