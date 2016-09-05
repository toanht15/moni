<?php
/**
 * WebDriver.php@api
 * User: ishidatakeshi
 * Date: 2014/09/12
 * Time: 16:21
 */

namespace Merazoma\driver;


class WebDriver extends Driver {
    private static $CURL_OPTIONS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => 1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => 'Merazoma',
        CURLOPT_FOLLOWLOCATION => 1,
    );
    private $options = null;
    public function __construct($host, $options = null) {
        parent::__construct($host);
        $this->options = $options;
    }
    public function get($url, $params)
    {
        $url .= strpos('?', $url) === false ? '?' : '&';
        $url .= http_build_query($params);
        return json_decode($this->sendOne('GET', $this->buildUrl($url)));
    }

    public function post($url, $params)
    {
        return json_decode($this->sendOne('POST', $this->buildUrl($url), $params));
    }

    public function delete($url, $params)
    {
        return json_decode($this->sendOne('DELETE', $this->buildUrl($url), $params));
    }

    public function put($url, $file)
    {
        return json_decode($this->sendOne('PUT', $this->buildUrl($url), $file));
    }

    public function buildUrl ($url) {
        return preg_replace('#/$#', '', 'http://' . $this->getHost()). '/' . preg_replace('#^/#', '', $url);
    }

    /**
     * 送信のための準備をする
     * @param $method
     * @param $url
     * @param null $params
     * @param null $headers
     * @return resource
     */
    public function prepareExec($method, $url, $params = null, $headers = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, $this->buildCurlOptions($method, $url, $params, $headers));
        return $curl;
    }

    /**
     * 一件送信をする
     * @param $method
     * @param $url
     * @param null $params
     * @param null $headers
     * @throws \RuntimeException
     * @return mixed
     */
    public function sendOne($method, $url, $params = null, $headers = null)
    {
        $curl = $this->prepareExec($method, $url, $params, $headers);
        list ($http_code, $header, $body) = $this->request($curl);
        if ($http_code < 200 || $http_code > 300) {
//            print $body;
//            throw new \RuntimeException ($url . ':' . $http_code . ':' . $body . '|' . var_export($params, true));
            return false;
        }
        return $body;
    }

    /**
     * cURLでリクエストする
     * @param $curl
     * @return array
     */
    public function request($curl)
    {
        $result = curl_exec($curl);
        $curl_info = curl_getinfo($curl);
        $http_code = $curl_info["http_code"] - 0;
        curl_close($curl);
        list ($header, $body) = preg_split('#\n.?\n#', $result);
        return array($http_code, $header, $body);
    }

    /**
     * cURLのパラメータ設定
     * @param $method メソッド名 (GET, POST, PUT, DELETE に 設定 )
     * @param $url
     * @param $params | ファイルパス | 他値なんでも
     * @param $headers ヘッダに付加したい情報
     * @throws \InvalidArgumentException
     * @return array()
     */
    public function buildCurlOptions($method, $url, $params = null, $headers = null)
    {
        $method = strtoupper($method);
        $options = self::$CURL_OPTIONS;
        if (is_array($this->options)) {
            foreach ($this->options as $key => $val) {
                if (!is_numeric($key)) continue;
                if (!isset($options[$key])) $options[$key] = $val;
            }

        }
        if ($method == 'GET') {
            $query = null;
            if (is_array($params)) {
                $query = http_build_query($params, null, '&');
            } elseif (is_scalar($params)) {
                $query = $params;
            } elseif (is_null($params)) {
                $query = '';
            } else {
                throw new \InvalidArgumentException ('Invalid params');
            }
            if ($query) $url .= (strpos($url, '?') === false ? '?' : '&') . $query;
        } else {
            // POST 系の パラメータの設定
            if (is_array($params)) {
                $options[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
            } elseif (is_file($params)) {
                $options[CURLOPT_POSTFIELDS] = file_get_contents(realpath($params));
            } elseif ($params) {
                $options[CURLOPT_POSTFIELDS] = $params;
            }

            // method の 決定
            if ($method == 'POST') {
                $options[CURLOPT_POST] = 1;
            }
            elseif (in_array($method, array('DELETE', 'PUT'))) {
                $options[CURLOPT_CUSTOMREQUEST] = $method;
            } else  {
                throw new \InvalidArgumentException ('Invalid Method:' . $method);
            }
        }

        if (is_array($headers)) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        elseif ($headers) {
            throw new \InvalidArgumentException ('Invalid Header' . var_export($headers, true));
        }
        $options[CURLOPT_URL] = $url;
        return $options;
    }
}