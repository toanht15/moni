<?php
require_once('vendor/Michelf/Markdown.php');

$json_data = array();
$json_data["result"] = 'ok';
$json_data["data"] = array(
    'html_content' => \Michelf\Markdown::defaultTransform($_POST['text_content'])
);

echo json_encode($json_data);
exit();
