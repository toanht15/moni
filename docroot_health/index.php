<?php
/**
 * サーバに対してデプロイが正常に実施できたかどうかを確認するためのヘルス・チェックです。
 */
$TARGET_BRAND = "monipla";
$TARGET_HOST = "monipla.com";
$output = array();
exec("curl -i -0 -H 'host: {$TARGET_HOST}' http://127.0.0.1/{$TARGET_BRAND}", $output);
$text = join("", $output);
if (!preg_match("#Location:.*503.html#", $text)) {
    header("HTTP/1.1 200 OK");
    echo "OK";
} else {
    header("HTTP/1.1 503 Service Temporarily Unavailable");
    echo "NG";
}