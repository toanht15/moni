<?php

class GAMPClient {

    public $user_id;
    public $client_id;
    public $protocol_version = "1";
    public $track_id;
    public $document_path;
    public $host_name;
    public $page_title;
    public $is_async  = true;
    private $base_url = "http://www.google-analytics.com/collect";
    private $ssl_url  = "https://ssl.google-analytics.com/collect";
    const cookie_name = "_aamp_ga";

    /**
     * @return mixed
     */
    public function sendPageView(){
        if (Util::isBot()) {
            return;
        }

        if ($this->is_async) {
            $this->post_async();
        } else {
            $this->post_sync();
        }

    }

    /**
     * @return array
     */
    private function createPVPostParam() {
        $session_id = $this->client_id ? $this->client_id : session_id();
        if ($_COOKIE[self::cookie_name]) {
            $session_id = $_COOKIE[self::cookie_name];
        }

        $param = "v=".$this->protocol_version."&tid=".$this->track_id."&cid=".$session_id."&t=pageview";
        if ($this->host_name) {
            $param .= "&dh=".$this->host_name;
        }
        if ($this->document_path) {
            $param .= "&dp=".urlencode($this->document_path);
        }
        if ($this->user_id) {
            $param .= "&uid=".$this->user_id;
        }
        if ($this->page_title) {
            $param .= "&dt=".urlencode($this->page_title);
        }
        $param .= "&uip=".Util::getClientIP();

        if ($_SERVER['HTTP_REFERER']) {
            $parts=parse_url($_SERVER['HTTP_REFERER']);
            if ($parts['host'] != Util::getMappedServerName()) {
                $param .= "&dr=".urlencode($_SERVER['HTTP_REFERER']);
            }
        }

        return $param;
    }

    function post_async(){
        try {
            $params = $this->createPVPostParam();
            $parts=parse_url($this->ssl_url);

            $fp = @fsockopen("ssl://".$parts['host'],
                isset($parts['port']) ? $parts['port'] : 443,
                $errno, $errstr, 1);
            if ($errno || $errstr) {
                throw new Exception ("GAMPClient#curl_post_async errorno: ".$errno." errorstr: ".$errstr);
            }
            $out = "POST ".$parts['path']." HTTP/1.1\r\n";
            $out.= "Host: ".$parts['host']."\r\n";
            $out.= "User-Agent:".$_SERVER['HTTP_USER_AGENT']."\r\n";
            $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out.= "Content-Length: ".strlen($params)."\r\n";
            $out.= "Connection: Close\r\n\r\n";
            $out.= $params;

            @fwrite($fp, $out);
            @fclose($fp);

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            return false;
        }
    }

    function post_sync () {
        try {
            $ch = curl_init($this->ssl_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->createPVPostParam());
            $json_response = curl_exec($ch);
            curl_close($ch);
            return json_decode($json_response);
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            return false;
        }
    }
}