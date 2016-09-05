<?php
class aafwCurlRequestBase {
    private  static $CURL_OPTIONS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => 1,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'aafw',
    );


    /**
     * cURLでリクエストする
     * @param メソッド名
     * @param URL
     * @param パラメータ
     * @param 付加したいヘッダ
     * @return 本文
     */
    public function request ( $method, $url, $params = null , $headers = null ) {
        $curl      = curl_init();
        curl_setopt_array ( $curl, $this->buildCurlOptions ( $method, $url, $params, $headers ) );
        $result    = curl_exec ( $curl );
        $curl_info = curl_getinfo ( $curl );
        $http_code = $curl_info["http_code"] - 0;

        if ( $http_code < 200 || $http_code > 300 ) {
            $cur_error = curl_errno($curl) ? curl_error($curl) : 'OK';
            throw new Exception ('Curl Status: ' . $cur_error . '|' . $url . ':' . $http_code . ':' . $result . '|' . var_export ( $params, true ), $http_code);
        }
        curl_close ( $curl );
        list ( $header, $body ) = preg_split( '#\n.?\n#', $result );
        return $body;
    }


    /**
     * cURLのパラメータ設定
     * @param メソッド名 (GET, POST, PUT, DELETE に 設定 )
     * @param array | ファイルパス | 他値なんでも
     * @param ヘッダに付加したい情報
     * @return array()
     */
    public function buildCurlOptions ( $method, $url, $params = null , $headers = null ) {
        $method = strtoupper ( $method );
        $options = self::$CURL_OPTIONS;
        if ( $method == 'GET' ){
            $query =  null;
            if ( is_array ( $params ) ) {
                $query = http_build_query ( $params, null, '&' );
            }
            elseif ( is_scalar ( $params ) ) {
                $query = $params;
            }
            elseif ( is_null ( $params ) ) {
                $query = '';
            }
            else {
                throw new Exception ( 'Invalid params' );
            }
            if ( $query ) $url .=  ( preg_match ( '#\?#', $url ) ? '&' : '?' )  . $query;
        }
        else {
            // POST 系の パラメータの設定
            if     ( is_array ( $params ) ) $options[CURLOPT_POSTFIELDS] = http_build_query ( $params, null, '&' );
            elseif ( is_file ( $params ) )  $options[CURLOPT_POSTFIELDS] = file_get_contents ( $params );
            elseif ( $params )              $options[CURLOPT_POSTFIELDS] = $params;
            // method の 決定
            if     ( $method == 'POST' )    $options[CURLOPT_POST]       = 1;
            elseif ( in_array ( $method, array ( 'DELETE', 'PUT' )  ) )  $options[CURLOPT_CUSTOMREQUEST] = $method;
            else                                                         throw new Exception ( 'Invalid Method:' . $method );
        }

        if     ( is_array ( $headers ) ) $options[CURLOPT_HTTPHEADER] = $headers;
        elseif ( $headers )              throw new Exception ( 'Invalid Header' . var_export ( $headers, true ) );

        $options[CURLOPT_URL] = $url;
        return $options;
    }

}
