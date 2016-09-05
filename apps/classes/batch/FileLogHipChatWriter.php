<?php

class FileLogHipChatWriter {

    const RESULT_SUCCESS = 0;
    const RESULT_FAILURE = 1;

    public function doProcess($argv) {
        if ($argv === null || count($argv) !== 2) {
            $this->logError("The arguments are invalid!: FileLogHipChatWriter");
            return self::RESULT_FAILURE;
        }

        $file_path = $argv[1];
        if (!file_exists($file_path)) {
            $this->logError("The file \"$file_path\" does not exist!: FileLogHipChatWriter");
            return self::RESULT_FAILURE;
        }

        $execution_log = '';
        try {
            $pointer = fopen($file_path, "r");
            if ($pointer === false) {
                $this->logError("The file \"$file_path\" could not be opened!: FileLogHipChatWriter");
                return self::RESULT_FAILURE;
            }
            while ($line = fgets($pointer)) {
                $execution_log .= $line . '\n';
            }
        } catch(Exception $e) {
            $this->logError($e);
            return self::RESULT_FAILURE;
        } finally {
            if ($pointer !== null) {
                fclose($pointer);
            }
        }

        $this->logInfo("The file path=" . $file_path . "\n" . $execution_log);
        return self::RESULT_SUCCESS;
    }

    private function logError($msg) {
        aafwLog4phpLogger::getHipChatLogger()->error($msg);
        aafwLog4phpLogger::getDefaultLogger()->error($msg);
    }

    private function logInfo($msg) {
        aafwLog4phpLogger::getHipChatLogger()->info($msg);
        aafwLog4phpLogger::getDefaultLogger()->info($msg);
    }
}
