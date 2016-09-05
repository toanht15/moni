<?php

class Util {

    const NOT_MAPPED_BRAND = -1;

    private static $week = array("日", "月", "火", "水", "木", "金", "土");

    private static $defaultDomain;

    private static $defaultManagerDomain;

    private static $domainMapping;

    public static function getShortenUrlByBitly($longUrl) {
        $settings = aafwApplicationConfig::getInstance();
        $longUrl = urlencode($longUrl);
        $req = 'http://api.bit.ly/shorten?login=' . $settings->query('ShortenURL.user') . '&apiKey=' . $settings->query('ShortenURL.apiKey') . '&version=2.0.1&longUrl=' . $longUrl;
        $contents = file_get_contents($req);
        if (isset($contents)) {
            $url = json_decode($contents, true);
        }
        return $url['results'][urldecode($longUrl)]['shortUrl'];
    }

    /**
     * 前後の全半角スペース・改行コードをtrimする
     * @param $str
     * @return string
     */
    public static function trimEmSpace($str) {
        $str = preg_replace('/^[ 　]+/u', '', $str);
        $str = preg_replace('/[ 　]+$/u', '', $str);
        $str = trim($str);
        return $str;
    }

    /**
     * BRをLNに変える
     * @author T.Nagano
     * @param $string The string to convert
     * @return string The converted string
     */
    public static function br2nl($string) {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }

    /**
     * 改行コードを置換する
     * @param string $src 検索する文字列
     * @param string $replace 置き換える文字列(デフォは'')
     * @return mixed
     */
    public static function nlReplace($src, $replace = '') {
        return str_replace(array("\r\n", "\n", "\r"), $replace, $src);
    }

    /**
     * アライドなど、運営者のIPか判定する
     * @author T.Nagano
     * @return bool
     */
    public static function isManagerIp() {
        $client_ip = self::getClientIP();
        if (!trim($client_ip)) return false;
        $manager_ip = aafwApplicationConfig::getInstance()->query('ManagerIps');
        if ($manager_ip && count($manager_ip) > 0) {
            foreach ($manager_ip as $item) {
                list($ip, $len) = explode('/', $item);
                if (is_numeric($len)) {
                    if (self::checkIpInSubnet($client_ip, $ip, $len)) {
                        return true;
                    }
                } else {
                    if ($client_ip == $item) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function getClientIP(){
        if (getenv('HTTP_X_FORWARDED_FOR')){
            return  getenv("HTTP_X_FORWARDED_FOR");
        }else if (getenv('REMOTE_ADDR')) {
            return getenv("REMOTE_ADDR");
        }else if (getenv('HTTP_CLIENT_IP')) {
            return getenv("HTTP_CLIENT_IP");
        }
        return null;
    }

    public static function createBaseUrl(Brand $brand, $secure = false) {
        $protocol = config('Protocol.Normal');
        if ($secure) {
            $protocol = config('Protocol.Secure');
        }
        $host = Util::getMappedServerName($brand->id);

        return $protocol . '://' . $host . '/' . self::resolveDirectoryPath($brand->id, $brand->directory_name);
    }

    /**
     * @param $host
     * @param array $arrayParam
     * @param array $queryParam
     * @return string
     */
    public static function createApplicationUrl($host, $arrayParam = array(), $queryParam = array()) {
        $protocol = config('Protocol.Normal');
        $url = $protocol . '://' . $host . '/';

        if (is_array($arrayParam) && count($arrayParam) > 0) {
            foreach ($arrayParam as $key => $data) {
                $url .=  $data . "/";
            }
        }

        if (is_array($queryParam) && count($queryParam) > 0) {
            $query = '';
            foreach ($queryParam as $key => $value) {
                if ($query == '') $query = "?" . $key . "=" . $value;
                else                $query .= "&" . $key . "=" . $value;
            }
            $url .= $query;
        }

        return $url;
    }

    /**
     * Set rewrite url
     *
     * @param $package The package name
     * @param $action The action name
     * @param array $arrayParam The array paramater for link
     * @param array $queryParam
     * @param string $base_url
     * @return string The url has been rewrite
     */
    public static function rewriteUrl($package, $action, $arrayParam = array(), $queryParam = array(), $base_url = '', $secure = false) {
        if ($base_url) {
            $url = $base_url;
        } else {
            $url = self::getBaseUrl($secure);
        }
        if (!empty($package) && !empty($action)) {
            $url .= $package . "/" . $action;
        } elseif (!empty($action)) {
            $url .= $action;
        } else {
            $url .= "";
        }

        if (is_array($arrayParam) && count($arrayParam) > 0) {
            foreach ($arrayParam as $key => $data) {
                $url .= "/" . $data;
            }
        }

        if (is_array($queryParam) && count($queryParam) > 0) {
            $query = '';
            foreach ($queryParam as $key => $value) {
                if ($query == '') $query = "?" . $key . "=" . $value;
                else                $query .= "&" . $key . "=" . $value;
            }
            $url .= $query;
        }
        return $url;
    }

    /**
     * Set rewrite url without domain mapping
     *
     * @param $package The package name
     * @param $action The action name
     * @param array $arrayParam The array paramater for link
     * @param array $queryParam
     * @return string The url has been rewrite
     *
     */
    public static function rewriteUrlWithoutDomainMapping($package, $action, $arrayParam = array(), $queryParam = array(), $secure = false) {
        $mapped_brand_id = Util::getMappedBrandId();
        $base_url = '';
        if ($mapped_brand_id !== Util::NOT_MAPPED_BRAND) {
            $brand_service = new BrandService();
            AAFW::import('jp.aainc.classes.BrandInfoContainer');
            $brand = BrandInfoContainer::getInstance()->getBrand();
            if ($brand === null) {
                $brand = $brand_service->getBrandById($mapped_brand_id);
            }
            $directory_name = '';
            $mapped_server_name = Util::getMappedServerName($mapped_brand_id);
            if ($brand->directory_name !== $mapped_server_name) {
                $directory_name = $brand->directory_name . '/';
            }
            $protocol = $secure ? config('Protocol.Secure') : self::getHttpProtocol();
            $base_url = $protocol . '://' . self::getDefaultBRANDCoDomain() . '/' . $_SERVER['HTTP_HOST'] . '/' . $directory_name;
        }
        return self::rewriteUrl($package, $action, $arrayParam, $queryParam, $base_url, $secure);
    }

    public static function getRedirectUrl($secure = false, $brand_id = -1) {
        $base_url = 'https://' . Util::getMappedServerName($brand_id);
        $redirect_url = self::getBaseUrl($secure);

        return strcmp($base_url, $redirect_url) == 0 ? $base_url : $redirect_url;
    }

    public static function getBaseUrl($secure = false) {
        $mapped_brand_id = Util::getMappedBrandId();
        $directory_name = '';
        if ($mapped_brand_id === Util::NOT_MAPPED_BRAND) {
            $request = self::parseRequestUri($_SERVER['REQUEST_URI']);
            $directory_name = ($request['directory_name']) ? $request['directory_name'] . '/' : '';
        } else {
            $mapped_server_name = Util::getMappedServerName($mapped_brand_id);
            $brand_service = new BrandService();
            AAFW::import('jp.aainc.classes.BrandInfoContainer');
            $brand = BrandInfoContainer::getInstance()->getBrand();
            if ($brand === null) {
                $brand = $brand_service->getBrandById($mapped_brand_id);
            }
            if ($brand->directory_name !== $mapped_server_name) {
                $directory_name = $brand->directory_name . '/';
            }
        }
        $protocol = $secure ? config('Protocol.Secure') : self::getHttpProtocol();

        return $protocol . '://' . $_SERVER['HTTP_HOST'] . '/' . $directory_name;
    }

    public static function getBrandBaseUrl($brand_id, $directory_name, $secure = false) {
        $protocol = $secure ? config("Protocol.Secure") : Util::getHttpProtocol();
        $base_url = $protocol . "://" . Util::getMappedServerName($brand_id) . "/" . Util::resolveDirectoryPath($brand_id, $directory_name);
        return $base_url;
    }

    public static function getCurrentUrl($secure = false) {
        $protocol = $secure ? config('Protocol.Secure') : self::getHttpProtocol();

        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function getPreviewUrl($preview_mode, $secure = false) {
        $preview_url = self::getCurrentUrl($secure);
        $sep = parse_url($preview_url, PHP_URL_QUERY) != '' ? '&' : '?';

        return $preview_url . $sep . 'preview=' . $preview_mode;
    }

    public static function getUrlFromPath($path, $secure = false) {
        $protocol = $secure ? config('Protocol.Secure') : self::getHttpProtocol();
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $path;
    }

    public static function stripQueryString($url) {
        $url_parts = parse_url($url);
        return $url_parts['scheme'] . '://' . $url_parts['host'] . (isset($url_parts['path']) ? $url_parts['path'] : '');
    }
    /**
     * リクエストURIを解析し、実行対象のアクションと付随する情報を取得します。
     *
     * <p>解析して取得する情報は以下の通りです。</p>
     * <ul>
     *  <li>action = 実行対象のアクション名</li>
     *  <li>__path = 先頭の「/」以降の解析前の生のパス情報</li>
     *  <li>directory_name = 実行対象のディレクトリ名(≒ブランド)</li>
     *  <li>req = クエリ文字列</li>
     *  <li>exts = パッケージ名、アクション名以降のパス</li>
     *  <li>package = パッケージ名</li>
     * </ul>
     *
     * <p>また、アクション名を取得する時のルールは以下の通りです。</p>
     * <ul>
     *  <li>${アクションのルート・パス} + / + ${サイトのパス} + / + ${REQUEST_URIからディレクトリ名部分を削除したもの}</li>
     * </ul>
     *
     * @param $requestUri
     * @param null $site
     * @return array
     */
    public static function parseRequestUri($requestUri, $site = null) {
        // $siteがnullでもInstanceが生成されている場合がほとんど
        $controller = aafwController::getInstance($site);

        $result = array();
        if (!$requestUri || $requestUri == '/' || preg_match('#^/\?#', $requestUri)) {
            $result['action'] = 'index';
            return $result;
        }

        if (!preg_match('#^/([^\?]+)(?:\?|$)#', $requestUri, $tmp)) return;

        $subdir = str_replace('/', '', $controller->getSubDirectory());

        $ac_path = preg_replace(array('#//#', '#/$#'), array('/', ''), $controller->getActionPath());

        if ($subdir) $tmp[1] = preg_replace('#/?' . $subdir . '/?#', '', $tmp[1]);

        list($package_name, $action_name, $path) = array('', '', preg_grep('#.#', preg_split('#/#', $tmp[1])));

        $tmp = array();
        foreach ($path as $x) {
            if (preg_match('#^\.+$#', $x)) continue;
            $tmp[] = $x;
        }
        $result['__path'] = $path = $tmp;

        // 該当ファイルがある場合はファイルを優先
        if (is_file($ac_path . '/' . ($controller->getSite() ? $controller->getSite() . '/' : '') . preg_replace('#\..+$#', '', $path[0]) . '.php')) {
            $action_name = array_shift($path);
        } // 該当するディレクトリがある場合
        elseif (is_dir($ac_path . '/' . ($controller->getSite() ? $controller->getSite() . '/' : '') . $path[0])) {
            $package_name = array_shift($path);
            $action_name = array_shift($path);
            if (!$action_name) $action_name = 'index';
        } // 該当するファイルとディレクトリがない場合はbrandcoディレクトリを参照する。
        else {
            $result['directory_name'] = array_shift($path);
            // 該当ファイルがある場合
            if (is_file($ac_path . '/' . ($controller->getSite() ? $controller->getSite() . '/brandco/' : 'brandco/') . preg_replace('#\..+$#', '', $path[0]) . '.php')) {
                $package_name = 'brandco/';
                $action_name = array_shift($path);
            } // 該当するディレクトリがある場合
            elseif (is_dir($ac_path . '/' . ($controller->getSite() ? $controller->getSite() . '/brandco/' : 'brandco/') . $path[0])) {
                $package_name = 'brandco/' . array_shift($path);
                $action_name = array_shift($path);
                if (!$action_name) $action_name = 'index';
            }
        }

        /**
         * action.type
         * Array
         * (
         *   [0] => action.type
         *   [1] => action
         *   [2] => type
         * )
         */
        if (preg_match('#^(.+?)\.([^\.]+)$#', $action_name, $tmp)) {
            $action_name = $tmp[1];
            $req = $tmp[2];
        } elseif (preg_match('#^(.+?)\.([^\.]+)#', $path[count($path) - 1], $tmp)) {
            $path[count($path) - 1] = $tmp[1];
            $req = $tmp[2];
        }
        $result['req'] = $req;
        $result['exts'] = $path;
        $result['action'] = $action_name;
        $result['package'] = $package_name;

        return $result;
    }

    public static function getHttpProtocol() {
        /*apache + variants specific way of checking for https*/
        if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)
        ) {
            return 'https';
        }
        /*nginx way of checking for https*/
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            return 'https';
        }
        return 'http';
    }

    public static function getCorrectPaging($paging, $total_page) {
        $aafwObject = new aafwObject();

        if (!$aafwObject->isNumeric($paging)
            || $aafwObject->isEmpty($paging)
            || $paging <= 0
        ) {

            return 1;
        }

        if ($paging > $total_page) {
            return $total_page;
        }

        return $paging;
    }

    public static function getFormatDateString($date_str) {

        $date_time = strtotime($date_str);
        $w = self::$week[date('w', $date_time)];
        $date = date('Y/m/d', $date_time);

        return $date . "（" . $w . "）";
    }

    public static function getFormatDateTimeString($date_str) {

        $date_time = strtotime($date_str);
        $w = self::$week[date('w', $date_time)];
        $time = date('H:i', $date_time);
        $date = date('Y/m/d', $date_time);

        return $date . " (". $w .") ".$time;
    }

    public static function isSmartPhone() {
        if (array_key_exists('sp_mode', $_GET) && $_GET['sp_mode'] == 'on') return true;

        $ua = array(
            'Mobile',
            'iPhone', // Apple iPhone
            'iPod', // Apple iPod touch
            'dream', // Pre 1.5 Android
            'CUPCAKE', // 1.5+ Android
            'blackberry9500', // Storm
            'blackberry9530', // Storm
            'blackberry9520', // Storm v2
            'blackberry9550', // Storm v2
            'blackberry9800', // Torch
            'webOS', // Palm Pre Experimental
            'incognito', // Other iPhone browser
            'webmate', // Other iPhone browser

            'FromIosMoniplaApp' //application install campaign
        );
        $ua_tablet = array(
            'iPad' // Apple iPad
        );
        //MobileにiPadひっかかるので、その時はfalseを返す（ブサイクだけど、例外処理）
        $extra = '/' . implode('|', $ua_tablet) . '/i';
        if (preg_match($extra, $_SERVER['HTTP_USER_AGENT'])) return false;
        $pattern = '/' . implode('|', $ua) . '/i';
        return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * @param $template
     * @param $params
     * @return mixed
     */
    public static function applyParameter($template, $params) {
        foreach ($params as $key => $value) {
            $template = str_replace($key, $value, $template);
        }
        return $template;
    }

    /**
     * @return bool
     */
    public static function isAcceptRemote() {
        $client_ip = self::getClientIP();
        if (!trim($client_ip)) return false;
        $decline_ip = aafwApplicationConfig::getInstance()->query('DeclineIP');
        if ($decline_ip && count($decline_ip) > 0) {
            foreach ($decline_ip as $item) {
                list($ip, $len) = explode('/', $item);
                if (is_numeric($len)) {
                    if (self::checkIpInSubnet($client_ip, $ip, $len)) {
                        return false;
                    }
                } else {
                    if ($client_ip == $item) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public static function cutTextByWidth($text, $max_width, $adding_text = '...') {
        $character_array = Util::mbStrSplit($text);
        $adding_text_width = 0;
        $text_width = 0;
        $characters = '';

        if($adding_text == '...') {
            $adding_text_width = 14;
        } else {
            $adding_text_array = Util::mbStrSplit($adding_text);
            foreach($adding_text_array as $adding_text_char) {
                $adding_text_width += Util::getCharacterWidth($adding_text_char);
            }
        }

        foreach($character_array as $char) {
            $characters .= $char;
            $text_width += Util::getCharacterWidth($char);

            // 最大幅を超えた場合に、「保持した値...」で出力するために、中間で値を保持しておく
            if ($text_width <= $max_width - $adding_text_width) {
                $temp_text = $characters;
            }
        }
        if ($text_width <= $max_width) {
            $ret_text = $characters;
        } else {
            $ret_text = $temp_text . $adding_text;
        }
        return $ret_text;
    }

    public static function mbStrSplit($str) {

        if (mb_internal_encoding() != 'UTF-8') mb_internal_encoding('UTF-8');
        if (mb_regex_encoding() != 'UTF-8') mb_regex_encoding('UTF-8');
        $strlen = mb_strlen($str, 'UTF-8');
        $ret = array();

        for ($i = 0; $i < $strlen; $i += 1) {
            $ret[] = mb_substr($str, $i, 1);
        }
        return $ret;
    }

    public static function getCharacterWidth($char) {
        if (preg_match('/^[il]/', $char)) {
            $char_width = 4;
        } elseif(preg_match('/^[fjt\.\s]/', $char)) {
            $char_width = 5;
        } elseif(preg_match('/^[rIJ\[\]rI\|]\(\)\-\//', $char)) {
            $char_width = 6;
        } elseif(preg_match('/^[csz\?]/', $char)) {
            $char_width = 7;
        } elseif(preg_match('/^[abdegkopqvxyFLP\{\}]/', $char)) {
            $char_width = 8;
        } elseif(preg_match('/^[0-9hnuABCEKRSTVXYZ\\\*\$]/', $char)) {
            $char_width = 9;
        } elseif(preg_match('/^[DGHNOQU&]/', $char)) {
            $char_width = 10;
        } elseif(preg_match('/^[wM#\^~=<>\+]/', $char)) {
            $char_width = 11;
        } elseif(preg_match('/^[m]/', $char)) {
            $char_width = 13;
        } elseif(preg_match('/^[W%@]/', $char)) {
            $char_width = 14;
        } elseif($char == ' ') {
            $char_width = 5;
        } else {
            $char_width = 13;
        }
        return $char_width;
    }

    public static function getIpAddress() {
        // 本番用と開発用でIPアドレスを取得するキーが違う
        return self::getClientIP();
    }

    /**
     * 指定したIPアドレスが、指定したネットワークアドレスとサブネットの範囲内であるかどうかを確認する
     * Broadcast IPアドレスと最小のIPアドレスを含まない
     * @param $check_ip - 例：192.168.1.1
     * @param $base_ip - 例：192.168.1.0
     * @param $subnet_mask - 例：29
     * @return bool
     */
    public static function checkIpInSubnet($check_ip, $base_ip, $subnet_mask) {

        if ((($min = ip2long($base_ip))) !== false && (($check_ip_long = ip2long($check_ip)) != false)) {
            $max = ($min | (1<<(32-$subnet_mask))-1);
            if ($check_ip_long > $min && $check_ip_long < $max) {
                return true;
            }
        }

        return false;
    }

    /**
     * proxyを介したURLに変換する
     *
     * @param $url
     * @return string
     */
    public static function convertProxyURL($url) {
        return self::getBaseUrl(true) . 'proxy?u=' . base64_encode($url);
    }

    /**
     * @return string
     */
    public static function getRequestURI() {
        $mapped_brand_id = Util::getMappedBrandId();

        if ($mapped_brand_id === Util::NOT_MAPPED_BRAND) {
            return $_SERVER['REQUEST_URI'];
        }

        $brand_service = new BrandService();
        AAFW::import('jp.aainc.classes.BrandInfoContainer');
        $brand = BrandInfoContainer::getInstance()->getBrand();
        if ($brand === null) {
            $brand = $brand_service->getBrandById($mapped_brand_id);
        }

        return '/' . $brand->directory_name . $_SERVER['REQUEST_URI'];
    }

    /**
     * BaseUrlか
     * @return bool
     */
    public static function isBaseUrl() {
        $result = Util::parseRequestUri(Util::getRequestURI());
        return $result['directory_name'] && $result['action'] == 'index' && $result['package'] ==  'brandco/';
    }

    /**
     * Check if Category Url
     * @return bool
     */
    public static function isSnsCategoryUrl() {
        $result = Util::parseRequestUri(Util::getRequestURI());
        return $result['directory_name'] && $result['package'] == 'brandco/sns' && $result['action'] == 'category';
    }

    /**
     * 今年か
     * @return bool
     */
    public static function isPresentYear($date) {
        return date("Y", strtotime($date)) === date("Y");
    }

    public static function isPersonalMachine()
    {
        $ip_address = self::getClientIP();
        $personal_ip = aafwApplicationConfig::getInstance()->query('PersonalMachineIp');
        if ($personal_ip && count($personal_ip) > 0) {
            foreach ($personal_ip as $item) {
                list($ip, $len) = explode('/', $item);
                if (is_numeric($len)) {
                    if (self::checkIpInSubnet($ip_address, $ip, $len)) {
                        return true;
                    }
                } else {
                    if ($ip_address == $item) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function cutStringByLineBreak($string) {
        // 改行コード置換用
        $line_break = array("\r\n", "\r");
        // 改行コードを統一
        $replaced_string = str_replace($line_break, "\n", $string);

        return explode("\n", $replaced_string);
    }

    public static function isClosedPage() {
        $result = Util::parseRequestUri(Util::getRequestURI());

        return $result['directory_name'] && $result['package'] == 'brandco/' && $result['action'] == 'closed';
    }

    public static function isClosedBrandPreviewMode() {
        return $_GET['preview'] && self::isClosedPage();
    }

    public static function getDefaultBRANDCoDomain() {
        if (self::$defaultDomain === null) {
            self::$defaultDomain = aafwApplicationConfig::getInstance()->query('Domain.brandco');
        }
        return self::$defaultDomain;
    }

    public static function isExternalDomain($host) {
        return $host !== Util::getDefaultBRANDCoDomain();
    }

    public static function isDefaultBRANDCoDomain() {
        return $_SERVER['HTTP_HOST'] === Util::getDefaultBRANDCoDomain();
    }

    public static function isDefaultManagerDomain() {
        if ( self::$defaultManagerDomain === null ) {
            self::$defaultManagerDomain = aafwApplicationConfig::getInstance()->query('Domain.brandco_manager');
        }
        return $_SERVER['HTTP_HOST'] === self::$defaultManagerDomain;
    }

    /**
     * サーバ名を取得します。
     *
     * もしWebからのアクセスでかつドメインがBRANDCo標準のもの、あるいはmanagerからのアクセスならば、
     * BRANDCo標準のドメイン名を返します。
     *
     * もしWebからのアクセスでない、あるいはBRANDCo標準のドメイン名からのアクセスでない場合は
     * 引数のブランドIDを元に、マッピングすべきドメイン名を解決します。
     * BRANDCoの構成上、多くのクラスがWeb(user, manager), batchで共有なので、
     * なるべく$brand_idを渡すようにしてください。
     * (アクション、ビュー、セッション操作など、100% Webからしか呼ばれないものは別)
     *
     * @param  $brand_id ブランドのID。
     * @return mixed リクエストに対応した適切なドメイン名。
     */
    public static function getMappedServerName($brand_id = -1) {
        // For Web application.
        if ( $brand_id === -1 && Util::isDefaultBRANDCoDomain() ) {
            return $_SERVER['HTTP_HOST'];
        }

        // For manager. If the brand_id is specified, use domain mapping directly.
        if ( $brand_id === -1 && Util::isDefaultManagerDomain() ) {
            return Util::getDefaultBRANDCoDomain();
        }

        // If the caller is not web or has a domain other than standard BRANDCo's one,
        // check the domain mapping definition.
        if ( self::$domainMapping === null ) {
            self::$domainMapping = aafwApplicationConfig::getInstance()->query('DomainMapping');
        }
        foreach (self::$domainMapping as $id => $mapped_domain) {
            // For batch and services.
            // Don't compare brand ids using the "===" operator because the query function returns a number type value.
            if ( $brand_id == $id ) {
                return $mapped_domain;
            }

            // For web
            if ( $mapped_domain === $_SERVER['HTTP_HOST'] ) {
                return $mapped_domain;
            }
        }

        // fall back that considers batch and 40x status codes.
        return Util::getDefaultBRANDCoDomain();
    }


    /**
     * 対象のリクエストがドメイン・マッピングの対象かどうかを取得します。
     * @param null $http_host
     * @return int|string
     */
    public static function getMappedBrandId($http_host = null) {
        if ($http_host == null) {
            $http_host = $_SERVER['HTTP_HOST'];
        }

        if ( self::$domainMapping === null ) {
            self::$domainMapping = aafwApplicationConfig::getInstance()->query('DomainMapping');
        }

        foreach (self::$domainMapping as $id => $mapped_domain) {
            if ( $mapped_domain === $http_host ) {
                return $id;
            }
        }

        return -1;
    }

    public static function constructBaseURL($brand_id, $directory_name, $secure = false) {
        $protocol = $secure ? config('Protocol.Secure') : 'http';
        return "${protocol}://" . Util::getMappedServerName($brand_id) . "/" . self::resolveDirectoryPath($brand_id, $directory_name);
    }

    /**
     * 対象ブランドの適切なディレクトリ名を解決します。
     *
     * もし対象ブランドがドメイン・マッピング対応で、かつディレクトリ名がマッピング後のドメイン名と同一ならば、
     * ディレクトリ名として空文字を返します。
     * それ以外のケースでは渡されたディレクトリ名の末尾に「/」を追加して返します。
     *
     * @param $brand_id
     * @param $directory_name
     * @return string
     */
    public static function resolveDirectoryPath($brand_id, $directory_name) {
        $mapped_server_name = Util::getMappedServerName($brand_id);
        if ($directory_name === $mapped_server_name) {
            return '';
        } else {
            return $directory_name . '/';
        }
    }

    public static function getCpURL($brand_id, $directory_name, $cp_id) {
        return self::constructBaseURL($brand_id, $directory_name) . "campaigns/{$cp_id}";
    }

    public static function haveDirectoryName(Brand $brand) {
        if (self::$domainMapping === null) {
            self::$domainMapping = aafwApplicationConfig::getInstance()->query('DomainMapping');
        }

        foreach (self::$domainMapping as $id => $mapped_domain) {
            if ($brand->id == $id) {
                return $brand->directory_name !== $mapped_domain;
            }
        }

        return true;
    }

    public static function clearCaches() {
        self::$defaultManagerDomain = null;
        self::$domainMapping = null;
        self::$defaultDomain = null;
    }

    public static function getEncode($string) {
        foreach(array('UTF-8','SJIS','EUC-JP','ASCII','JIS') as $encode){
            if(mb_convert_encoding($string, $encode, $encode) == $string){
                return $encode;
            }
        }

        return null;
    }

    public static function convertEncoding($string) {
        $encode = Util::getEncode($string);
        if ($encode !='UTF-8') {
            $string = mb_convert_encoding($string, 'UTF-8', $encode);
        }

        return $string;
    }

    public static function isMatchArray($arr1, $arr2) {
        if (count($arr1) > count($arr2)) {
            return array_diff($arr1, $arr2) ? false : true;
        }else{
            return array_diff($arr2, $arr1) ? false : true;
        }
    }

    public static function isIncludeArray($src_arr, $target_arr) {
        if (!$src_arr || !$target_arr || !is_array($src_arr) || !is_array($target_arr)) return false;

        $lower_target_arr = array();
        foreach ($target_arr as $target) {
            $lower_target_arr[] = mb_strtolower($target);
        }

        foreach ($src_arr as $src) {
            $lower_src = mb_strtolower($src);
            if (!in_array($lower_src, $lower_target_arr)) return false;
        }
        return true;
    }

    public static function isBot() {
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function isNullOrEmpty($value) {
        return $value === null || $value === '';
    }

    public static function existNullOrEmpty() {
        if (func_get_args() === null) {
            return true;
        }
        foreach (func_get_args() as $value) {
            if (self::isNullOrEmpty($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 二重起動防止（バッチ用）
     * また、二重起動ではない場合は、バッチ用のNew Relicを起動します。
     *
     * @param $object
     * @return bool
     */
    public static function lockFile($object) {
        $class_name = get_class($object);
        $pointer = fopen("/tmp/{$class_name}.lock", 'c');
        if (!flock($pointer, LOCK_EX | LOCK_NB)) {
            return null;
        }
        self::startNewRelicForBatch($class_name);
        return $pointer;
    }

    /**
     * 二重起動防止（バッチ用）
     * また、二重起動ではない場合は、バッチ用のNew Relicを起動します。
     * @param $name
     * @return bool
     */
    public static function lockFileByName($name) {
        $pointer = fopen("/tmp/{$name}.lock", 'c');
        if (!flock($pointer, LOCK_EX | LOCK_NB)) {
            return null;
        }
        self::startNewRelicForBatch($name);
        return $pointer;
    }

    public static function isInvalidBrandName($brand_name) {
        $first_pos = strpos($brand_name, ' ');
        if ($first_pos === false) {
            return false;
        }
        $sub_str = substr($brand_name, 0, $first_pos);
        return preg_match('/[^\w\p{P}\d{D}]/', $sub_str) === 1;
    }

    public static function startNewRelicForBatch($name) {
        if (extension_loaded('newrelic')) {
            $config = aafwApplicationConfig::getInstance();
            if($config->NewRelic['use']) {
                newrelic_set_appname($config->NewRelic['batchApplicationName']);
                newrelic_name_transaction($name);
            }
        }
    }

    /**
     * @param $html
     * @return mixed
     */
    public static function sanitizeOutput($html) {
        $search = array(
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );
        $replace = array(
            '>',
            '<',
            '\\1'
        );
        $html = preg_replace($search, $replace, $html);
        return $html;
    }

    /**
     * 代理ログインで使用するトークンを生成
     *
     * @param $value
     * @param $salt
     * @return string
     */
    public static function generateBackdoorLoginToken($value, $salt) {
        AAFW::import('jp.aainc.classes.util.Hash');
        AAFW::import('jp.aainc.classes.util.TokenWithoutSimilarCharGenerator');

        if (!$value || !$salt) {
            return null;
        }
        $hash = new Hash();
        $token_generator = new TokenWithoutSimilarCharGenerator();
        $token = $hash->doHash($value, $salt, 5000);

        return $token;
    }


    /**
     * 配列の内、ランダムで{$n_choice}個選択する
     * @param $array
     * @param $n_choice
     * @return array
     */
    public static function chooseAtRandom($array, $n_choice) {
        if ($array && is_array($array)) {
            $rand_keys = array_rand($array, min(count($array), $n_choice));

            $array_rand = array();
            foreach ($rand_keys as $key) {
                $array_rand[] = $array[$key];
            }

            return $array_rand;
        }

        return array();
    }

    /**
     * @param $birthday
     * @return float
     *
     */
    public static function getUserAge($birthday) {
        $birthday_time = new DateTime($birthday);
        $birthday = $birthday_time->format('Ymd');

        return floor( ( date('Ymd') - $birthday ) / 10000 );
    }

    /**
     * @param $date
     * @return bool
     *
     */
    public static function isValidDate($date){
        if(!Util::isNullOrEmpty($date) && $date != '0000-00-00 00:00:00'){
            return true;
        }
        return false;
    }
}
