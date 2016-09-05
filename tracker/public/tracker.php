<?php
//トラッカーのトラフィックが多いのでベースクラスを使わない
class Tracker {

    /** @var  Redis $redis */
    private $redis;
    /** @var  resource $database_connection */
    private $database_connection;
    private $track_DbId;
    private $database_dns;
    private $log_file;

    const CONVERSION_KEY = "conversion";

    public function __construct() {
        $app_yml = file_get_contents(dirname(__FILE__) . '/../../apps/config/app.yml');
        preg_match('/Log4php:.*appenders:.*[\s\t]+cv:.*[\s\t]+name:([^\r\n]+)/s', $app_yml, $matches);
        $this->log_file = trim(str_replace("%s", date("Ymd"), $matches[1]));
        preg_match('/DBInfo:.*tracker:.*[\s\t]+w:([^\r\n]+)/s', $app_yml, $matches);
        $this->database_dns = $matches[1];
    }

    public function run() {
        try {
            $uid = null;

            if($_GET['_mp_uid']) {
                require_once(dirname(__FILE__).'/../../apps/config/define.php');
                AAFW::import ( 'jp.aainc.classes.clients.UtilityApiClient' );

                $uid = UtilityApiClient::getInstance()->getUser(UtilityApiClient::TRACKER, $_GET['_mp_uid'])->userId;
            }

            if(!$uid && isset($_COOKIE[session_name()])){
                require_once(dirname(__FILE__).'/../../apps/config/define.php');
                AAFW::import ( 'jp.aainc.aafw.session.session_handler.RedisSessionHandler' );
                session_start();

                $uid = $_SESSION['pl_monipla_userInfo']['id'];
            }

            $params = $this->getConversionParams(array('aa_user_id'=> $uid));
            if (!$params['conversion_id'] || !$params['brand_id']) {
                $this->putImageAndExit();
            }

            $this->redis = $this->connectRedis();
            $redis_push = $this->redis->lPush(self::CONVERSION_KEY.':brand_'.$_GET['brand_id'], json_encode($params));

            if (!$redis_push) {
                $this->database_connection = $this->connectTrackerDB();
                $this->saveConversionLog($params);
                mysqli_close($this->database_connection);
            }

            $this->redis->close();

        } catch (Exception $e) {
            error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." Tracker.php @run]".$e, 3, $this->log_file);
        }
        $this->putImageAndExit();
    }

    /**
     * @return mysqli
     * @throws Exception
     */
    public function connectTrackerDB() {
        try {
            preg_match('#([^:/]+)://([^:/]+):?([^@]*)@([^/]+)/([^:/]+)#', $this->database_dns, $matches);
            $dbtype = $matches[1];
            $user = $matches[2];
            $password = $matches[3];
            $server = $matches[4];
            $database = $matches[5];

            //mysql接続
            $connection = mysqli_connect($server, $user, $password, $database);
            // Check connection
            if (mysqli_connect_errno()) {
//            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
        } catch (Exception $e) {
            error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." Tracker.php @connectTrackerDB]".$e, 3, $this->log_file);
            throw $e;
        }
        return $connection;
    }

    /**
     * @return Redis
     * @throws Exception
     * @throws RedisException
     */
    public function connectRedis() {
        try {
            $app_yml = file_get_contents( dirname( __FILE__ ) . '/../../apps/config/redis.yml' );
            preg_match_all( '/Host: ([^\r\n]+)/s', $app_yml, $matches );
            $host = $matches[1][1];
            preg_match_all( '/Port: ([^\r\n]+)/s', $app_yml, $matches );
            $port = $matches[1][1];
            preg_match( '/TrackerDbId: ([^\r\n]+)/s', $app_yml, $matches );
            $this->track_DbId = $matches[1];
            $redis = new Redis();

            $redis->connect($host, $port);
            $redis->select($this->track_DbId);
            return $redis;
        } catch (RedisException $e) {
            error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." Tracker.php @connectRedis]".$e, 3, $this->log_file);
            throw $e;
        }
    }

    public function getConversionParams($param = array()) {

        $insArray = array(
            'remote_address' => $this->getIpAddress(),
            'remote_host' => getenv('REMOTE_HOST'),
            'user_agent'    => getenv('HTTP_USER_AGENT'),
            'language'      => getenv('HTTP_ACCEPT_LANGUAGE'),
            'admin_flg'     => isset($_GET['admin_flg']) ? $_GET['admin_flg'] : 0,
            'brand_id'      => isset($_GET['brand_id']) ? $_GET['brand_id'] : null,
            'page_url'      => isset($_GET['request_uri']) ? $_GET['request_uri'] : null,
            'conversion_id' => isset($_GET['conversion_id']) ? $_GET['conversion_id'] : null,
            'referrer'      => isset($_GET['referrer']) ? $_GET['referrer'] : null,
            'order_no'      => isset($_GET['order_no']) ? $_GET['order_no'] : null,
            'order_price'   => isset($_GET['order_price']) ? $_GET['order_price'] : null,
            'order_count'   => isset($_GET['order_count']) ? $_GET['order_count'] : null,
            'free1'         => isset($_GET['free1']) ? $_GET['free1'] : null,
            'free2'         => isset($_GET['free2']) ? $_GET['free2'] : null,
            'free3'         => isset($_GET['free3']) ? $_GET['free3'] : null,
            'free4'         => isset($_GET['free4']) ? $_GET['free4'] : null,
            'date_created' => date("Y-m-d H:i:s"),
            'date_created_ymdh' => date("YmdH")
        );

        return array_merge($insArray, $param);
    }

    /**
     * @param $params
     * @throws Exception
     */
    public function saveConversionLog($params) {
        try {
            $columns = array();
            $values = array();
            foreach ($params as $key => $value) {
                $columns[] = $key;
                $values[] = $value;
            }
            $query = 'INSERT INTO conversion_log (';
            foreach ($columns as $column) {
                $query .= $column . ', ';
            }
            $query = rtrim($query, ', ');
            $query .= ') VALUES(';
            foreach ($values as $value) {
                $query .= '"' . mysqli_escape_string($this->database_connection, $value) . '", ';
            }
            $query = rtrim($query, ', ');
            $query .= ')';

            mysqli_autocommit($this->database_connection,FALSE);

            mysqli_query($this->database_connection, $query);

            if (!mysqli_commit($this->database_connection)) {
                mysqli_rollback($this->database_connection);
                error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." Tracker.php @saveConversionLog] cant execute query:".$query, 3, $this->log_file);
            }
        } catch (Exception $e) {
            error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." Tracker.php @saveConversionLog]".$e, 3, $this->log_file);
            mysqli_close($this->database_connection);
            throw $e;
        }
    }

    /**
     *
     */
    public function putImageAndExit(){
        header("Content-type: image/gif");
        $img = @imagecreate(1, 1); //空の画像を作成
        $bgc = @ImageColorAllocate($img, 0, 0, 0);
        $bgc = @imagecolortransparent($img, $bgc); //透明色を指定
        @ImageFilledRectangle($img, 0, 0, 0, 0, $bgc);
        @ImageGif($img);
        exit();
    }

    private function getIpAddress() {
        // 本番用と開発用でIPアドレスを取得するキーが違う
        $ip_address = '';
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && $_SERVER["HTTP_X_FORWARDED_FOR"]) {
            $ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $ip_address = $_SERVER["REMOTE_ADDR"];
        }
        return $ip_address;
    }
}

$tracker = new Tracker();
$tracker->run();