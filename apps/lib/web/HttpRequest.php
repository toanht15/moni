<?php
class HttpRequest {
    public function get ( $url ) {
        $context = stream_context_create ( array (
            'http' => array (
                'method' => 'GET',
                "header" =>
                    "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/536.26.14 (KHTML, like Gecko) Version/6.0.1 Safari/536.26.14\r\n".
                    "Accept: text/xml,text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8 \r\n" .
                    "Keep-Alive: 300\r\n" .
                    "Connection: keep-alive\r\n" .
                    "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n",
            ),
        ));
        return file_get_contents ( $url, false, $context );
    }
}
