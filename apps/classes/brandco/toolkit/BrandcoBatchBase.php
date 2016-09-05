<?php
AAFW::import('jp.aainc.lib.base.aafwObject');

abstract class BrandcoBatchBase {

    protected $logger;
    protected $service_factory;
    protected $execute_class;
    protected $execute_count;
    protected $data_info;
    protected $argv;

    public function __construct($argv = null) {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->execute_class = get_class($this);
        if ($argv) {
            $this->argv = $this->parseArgs($argv);
        }
    }

    public function doProcess() {
        try {
            $this->logger->info("start batch: class=" . $this->execute_class);
            $start_time = date("Y-m-d H:i:s");
            $this->executeProcess();
            $end_time = date("Y-m-d H:i:s");

            if ($this->execute_count) {
                $this->logger->info($this->execute_class . ' Status:Success Start_Time:' . $start_time . ' End_Time:' . $end_time . ' Count:' . $this->getExecuteCount());
            } else {
                $this->logger->info($this->execute_class . ' Status:Success Start_Time:' . $start_time . ' End_Time:' . $end_time);
            }
        } catch (Exception $e) {
            $end_time = date("Y-m-d H:i:s");
            $this->hipchat_logger->error($this->execute_class . ' Status:Error Start_Time:' . $start_time . ' End_Time:' . $end_time . ' Detail:' . $this->getDataInfo() . ',' . $e);
            $this->logger->error($this->execute_class . ' Status:Error Start_Time:' . $start_time . ' End_Time:' . $end_time . ' Detail:' . $this->getDataInfo() . ',' . $e);
        }
    }

    abstract function executeProcess();

    public function setExecuteCount($count) {
        $this->execute_count = $count;
    }

    public function getExecuteCount() {
        return $this->execute_count;
    }

    public function setDataInfo($data_info) {
        $this->data_info = $data_info;
    }

    public function getDataInfo() {
        return $this->data_info;
    }

    public function setArgv($argv)
    {
        $this->argv = $argv;
    }

    public function getArgv()
    {
        return $this->argv;
    }

    public function parseArgs($argv) {
        $result = array();
        foreach ($argv as $arg) {
            $strlen = mb_strlen($arg, 'UTF8');
            $i = 0;
            $key = '';
            for ($i = 0; $i < $strlen; $i++) {
                $char = mb_substr($arg, $i, 1, 'UTF8');
                if ($char == '=') break;
                $key .= $char;
            }

            $val = '';
            for ($i += 1; $i < $strlen; $i++) {
                $char = mb_substr($arg, $i, 1, 'UTF8');
                $val .= $char;
            }

            if ($key && $val) {
                $result[$key] = $val;
            }
        }
        return $result;
    }
}