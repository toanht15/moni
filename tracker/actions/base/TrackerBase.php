<?php
abstract class TrackerBase {
    protected $database_dns;
    protected $log_file;
    protected $database_connection;
    /** @var  Redis $redis */
    protected $redis;
    /** @var  resource $database_connection */
    protected $session_DbId;
    protected $track_DbId;

    public function createResponseImage(){
        header("Content-type: image/gif");
        $img = @imagecreate(1, 1); //空の画像を作成
        $bgc = @ImageColorAllocate($img, 0, 0, 0);
        $bgc = @imagecolortransparent($img, $bgc); //透明色を指定
        @ImageFilledRectangle($img, 0, 0, 0, 0, $bgc);
        @ImageGif($img);
    }

    /**
     * @return mysqli
     * @throws Exception
     */
    public function connectDB() {
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
                error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." TrackerBase @connectDB]Failed to connect to MySQL: " . mysqli_connect_error()."\n", 3, $this->log_file);
                return false;
            }
        } catch (Exception $e) {
            error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." TrackerBase @connectDB]".$e."\n", 3, $this->log_file);
            return false;
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
            preg_match_all( '/DbId: ([^\r\n]+)/s', $app_yml, $matches );
            $this->session_DbId = $matches[1][1];
            preg_match( '/TrackerDbId: ([^\r\n]+)/s', $app_yml, $matches );
            $this->track_DbId = $matches[1];
            $redis = new Redis();

            $redis->connect($host, $port);
            $redis->select($this->session_DbId);
            return $redis;
        } catch (RedisException $e) {
            error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." TrackerBase @connectRedis]".$e."\n", 3, $this->log_file);
            return false;
        }
    }

    public function createInsertSql($params, $table_name) {
        $columns = array();
        $values = array();
        foreach ($params as $key => $value) {
            $columns[] = $key;
            $values[] = $value;
        }
        $query = 'INSERT INTO '.$table_name.' (';
        foreach ($columns as $column) {
            $query .= $column . ', ';
        }
        $query = rtrim($query, ', ');
        $query .= ') VALUES(';
        foreach ($values as $value) {
            $query .= '"' . mysqli_real_escape_string($this->database_connection,$value) . '", ';
        }
        $query = rtrim($query, ', ');
        $query .= ')';

        return $query;
    }

    abstract function run();
}