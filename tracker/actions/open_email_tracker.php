<?php
require_once(dirname(__FILE__) . '/base/TrackerBase.php');
require_once(dirname(__FILE__) . '/../../apps/classes/Util.php');

class OpenEmailTracker extends TrackerBase{

    private $table_name = "open_email_tracking_logs";

    public function __construct() {
        $app_yml = file_get_contents(dirname(__FILE__) . '/../../apps/config/app.yml');
        preg_match('/Log4php:.*appenders:.*[\s\t]+emailTracker:.*[\s\t]+name:([^\r\n]+)/s', $app_yml, $matches);
        $this->log_file = trim(str_replace("%s", date("Ymd"), $matches[1]));
        preg_match('/DBInfo:.*main:.[\s\t]+w:([^\r\n]+)/s', $app_yml, $matches);
        $this->database_dns = $matches[1];
    }

    public function run() {
        try {
            if (!array_key_exists('params', $_GET)){
                $this->createResponseImage();
                return;
            }

            $params = json_decode(base64_decode($_GET['params']), true);
            
            if (!is_array($params)) {
                $this->createResponseImage();
                return;
            }

            $cp_action_id = $params['cp_action_id'];
            $bc_user_id = $params['user_id'];

            if (!$bc_user_id || !$cp_action_id) {
                $this->createResponseImage();
                return;
            }

            if ($this->database_connection = $this->connectDB()) {

                $log = $this->getTrackingLogByCpActionIdAndUserId($cp_action_id, $bc_user_id);

                if ($log) {
                    mysqli_close($this->database_connection);
                    $this->createResponseImage();
                    return;
                }

                $this->writeNewTrackingLog($cp_action_id, $bc_user_id);

                mysqli_close($this->database_connection);
            }

        } catch (Exception $e) {
            error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." OpenEmailTracker @run]: " . $e->getMessage()."\n", 3, $this->log_file);
        }

        $this->createResponseImage();
    }

    public function getTrackingLogByCpActionIdAndUserId($cp_action_id, $user_id) {
        $sql = "SELECT id FROM ".$this->table_name." WHERE cp_action_id = ".$cp_action_id." AND user_id = ".$user_id;
        $query = mysqli_escape_string($this->database_connection, $sql);
        $rs = mysqli_query($this->database_connection, $query);
        return mysqli_fetch_assoc($rs);
    }

    public function writeNewTrackingLog($cp_action_id, $user_id) {

        $param = array(
            "user_id" => $user_id,
            "cp_action_id" => $cp_action_id,
            "user_agent" => getenv('HTTP_USER_AGENT'),
            "remote_ip" => Util::getClientIP(),
            "referer_url" => getenv('HTTP_REFERER'),
            "device" => Util::isSmartPhone(),
            "language" => getenv('HTTP_ACCEPT_LANGUAGE'),
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s')
        );

        $sql = $this->createInsertSql($param, $this->table_name);

        mysqli_autocommit($this->database_connection,FALSE);

        mysqli_query($this->database_connection, $sql);

        if (!mysqli_commit($this->database_connection)) {
            mysqli_rollback($this->database_connection);
            error_log("\n------------------------\n"."[".date("Y/m/d H:i:s")." OpenEmailTracker @writeNewTrackingLog]: cant execute query: ".$sql."\n", 3, $this->log_file);
        }
    }
}
