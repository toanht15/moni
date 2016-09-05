<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.clients.UtilityApiClient');

/**
 * Class ImportConversionFromAccessLog
 * Read line by line from log file and create sql to insert conversion
 * コンバージョンログの取得が止まる障害が発生した時に、nginxのアクセスログからconversionデータを復旧するためのバッチです。
 */
class ImportConversionFromAccessLog extends BrandcoBatchBase {

    const LOG_DIR_PATH = '/../../batch/tmp/access_log';

    private $start_date;
    private $end_date;
    private $database_connection;
    private $db_api_connection;

    const LIMIT_QUERY_RECORD = 100;

    function executeProcess() {

        $this->start_date = DateTime::createFromFormat('Y-m-d H:i:s','2016-06-16 19:28:00');
        $this->end_date = DateTime::createFromFormat('Y-m-d H:i:s','2016-07-04 20:42:00');

        $this->database_connection = $this->connectTrackerDB();
        $this->db_api_connection = $this->connectApiDB();
        
        if(isset($this->argv['target_file'])) {

            $file_path = dirname(__FILE__) . self::LOG_DIR_PATH . '/' . $this->argv['target_file'];

            $this->logger->info('ImportConversionFromAccessLog: START_IMPORT_FILE=' . $this->argv['target_file']);

            $this->importData($file_path);

            $this->logger->info('ImportConversionFromAccessLog: FINISH_IMPORT_FILE=' . $this->argv['target_file']);

        } else {

            $log_files = $this->scanAllFileInDir(self::LOG_DIR_PATH);

            foreach($log_files as $log_file) {

                $file_path = dirname(__FILE__) . self::LOG_DIR_PATH . '/' . $log_file;

                $this->logger->info('ImportConversionFromAccessLog: START_IMPORT_FILE=' . $log_file);

                try {
                    $this->importData($file_path);
                } catch (Exception $e) {

                    $this->logger->info('ImportConversionFromAccessLog: ERROR_IMPORT_FILE=' . $log_file);

                    continue;
                }

                $this->logger->info('ImportConversionFromAccessLog: FINISH_IMPORT_FILE=' . $log_file);
            }

        }
    }

    /**
     * Read Data from Log and Insert To Db
     * @param $log_file_path
     *
     */
    public function importData($log_file_path) {

        $handle = gzopen($log_file_path, 'r');

        if (!$handle) {
            return false;
        }

        $count = 0;
        $conversion_log_array = array();

        while (!gzeof($handle)) {

            $line = gzgets($handle);

            if (strpos($line, '/tracker?') === false) {
                continue;
            }

            $conversion_log = $this->parserLog($line);



            if($conversion_log === null) {
                continue;
            }

            if($count == self::LIMIT_QUERY_RECORD) {
                $this->saveConversionLog($conversion_log_array);
                $conversion_log_array = array();
                $count = 0;
            } else {
                $conversion_log_array[] = $conversion_log;
                $count++;
            }
        }

        if(count($conversion_log_array) > 0) {
            $this->saveConversionLog($conversion_log_array);
        }

        gzclose($handle);
    }

    /**
     * @param $log
     * @return array|null
     */
    public function parserLog($log) {

        preg_match('/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")$/',$log, $matches);

        $create_date = DateTime::createFromFormat('d/M/Y H:i:s',$matches[4]. ' ' . $matches[5]);

        if($create_date < $this->start_date || $create_date > $this->end_date) {
            return null;
        }

        $url_query = parse_url(urldecode($matches[8]))['query'];

        preg_match('/(request_uri=)(.*)(&referrer=.*$)/', $url_query, $request_uri_matches);
        $request_uri = $request_uri_matches[2];

        preg_match('/(&referrer=)(.*)(&brand_id=.*$)/', $url_query, $referrer_uri_matches);
        $referrer = $referrer_uri_matches[2];

        parse_str($url_query,$url_parameters);

        $parser_data = array(
            'admin_flg'     => isset($url_parameters['admin_flg']) ? $url_parameters['admin_flg'] : 0,
            'brand_id'      => isset($url_parameters['brand_id']) ? $url_parameters['brand_id'] : null,
            'page_url'      => $request_uri,
            'conversion_id' => isset($url_parameters['conversion_id']) ? $url_parameters['conversion_id'] : null,
            'referrer'      => $referrer,
            'order_no'      => isset($url_parameters['order_no']) ? $url_parameters['order_no'] : null,
            'order_price'   => isset($url_parameters['order_price']) ? $url_parameters['order_price'] : null,
            'order_count'   => isset($url_parameters['order_count']) ? $url_parameters['order_count'] : null,
            'free1'         => isset($url_parameters['free1']) ? $url_parameters['free1'] : null,
            'free2'         => isset($url_parameters['free2']) ? $url_parameters['free2'] : null,
            'free3'         => isset($url_parameters['free3']) ? $url_parameters['free3'] : null,
            'free4'         => isset($url_parameters['free4']) ? $url_parameters['free4'] : null,
        );

        $parser_data['aa_user_id'] = 0;

        if(!Util::isNullOrEmpty($url_parameters['_mp_uid']) && $url_parameters['_mp_uid'] != 'null') {

            $uid = $this->findUserId($url_parameters['_mp_uid']);

            if($uid != null) {
                $parser_data['aa_user_id'] = $uid;
            }
        }

        $parser_data['remote_address'] = $matches[1];
        $parser_data['remote_host'] = null;
        $parser_data['language'] = 'ja-JP';
        $parser_data['user_agent'] = str_replace('"','',$matches[13]);

        $parser_data['date_created'] = $create_date->format('Y-m-d H:i:s');
        $parser_data['date_created_ymdh'] = $create_date->format('YmdH');

        return $parser_data;
    }

    /**
     * @param $params
     * @throws Exception
     */
    public function saveConversionLog($conversion_log_array) {

        try {

            $columns = array();

            foreach (array_keys($conversion_log_array[0]) as $value) {
                $columns[] = $value;
            }

            $query = 'INSERT INTO conversion_logs (';
            foreach ($columns as $column) {
                $query .= $column . ', ';
            }

            $query = rtrim($query, ', ');
            $query .= ') VALUES ';

            foreach ($conversion_log_array as $conversion_log) {

                $query .= ' (';

                foreach($conversion_log as $value) {
                    $query .= '"' . mysqli_escape_string($this->database_connection, $value) . '", ';
                }

                $query = rtrim($query, ', ');
                $query .= '),';
            }

            $query = rtrim($query, ',');

            mysqli_autocommit($this->database_connection,FALSE);

            mysqli_query($this->database_connection, $query);

            if (!mysqli_commit($this->database_connection)) {
                mysqli_rollback($this->database_connection);
                $this->logger->error("\n------------------------\n"."[".date("Y/m/d H:i:s")." ImportConversionFromAccessLog.php @saveConversionLog] cant execute query:".$query ."\n------------------------\n");
                throw new Exception('Query Error');
            }

        } catch (Exception $e) {
            $this->logger->error("\n------------------------\n"."[".date("Y/m/d H:i:s")." ImportConversionFromAccessLog.php @saveConversionLog] cant execute query:".$query ."\n------------------------\n");
            throw $e;
        }
    }

    /**
     * Get All Log File
     * @param $dir_path
     * @return array
     */
    public function scanAllFileInDir($dir_path) {

        $files = scandir(dirname(__FILE__) . $dir_path);

        //Remove Unix file (., ..) from array
        $files = array_diff($files, array('.', '..'));

        return $files;
    }

    public function connectTrackerDB() {
        try {
            $appConfig = aafwApplicationConfig::getInstance();
            $db_info = $appConfig->query('@app.DBInfo.tracker.w');

            preg_match('#([^:/]+)://([^:/]+):?([^@]*)@([^/]+)/([^:/]+)#', $db_info, $matches);

            $user = $matches[2];
            $password = $matches[3];
            $server = $matches[4];
            $database = $matches[5];

            //mysql接続
            $connection = mysqli_connect($server, $user, $password, $database);
            // Check connection
            if (mysqli_connect_errno()) {
              echo "Failed to connect to MySQL: " . mysqli_connect_error();
              exit;
            }

        } catch (Exception $e) {
            throw $e;
        }

        return $connection;
    }

    public function connectApiDB() {

        try {

            $user = 'bc_api';
            $password = 'vV83lmvtI';
            $server = '192.168.4.242';
            $database = 'db_brandco_api';

            //mysql接続
            $connection = mysqli_connect($server, $user, $password, $database);
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to Api DB: " . mysqli_connect_error();
                exit;
            }

        } catch (Exception $e) {

            throw $e;
        }

        return $connection;
    }

    public function findUserId($token) {

        $user_id = null;

        $sql = 'Select user_id from users WHERE client_id = 5 AND del_flg = 0 AND token = "'. $token . '"';

        try {

            $result = mysqli_query($this->db_api_connection, $sql);

            if($result != false) {

                while($obj = $result->fetch_object()){
                    $user_id = $obj->user_id;
                    break;
                }

            }

            $result->close();
        }catch (Exception $e) {
            $this->logger->error("\n------------------------\n"."[".date("Y/m/d H:i:s")." ImportConversionFromAccessLog.php @findUserId] cant execute query:".$sql ."\n------------------------\n");
        }

        return $user_id;
    }
}