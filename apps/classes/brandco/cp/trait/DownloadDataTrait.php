<?php

trait DownloadDataTrait {

    public function initFolder($folderPath) {
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    }

    /**
     * @param $temp_dir
     * @return bool
     */
    public function rmTempDir($temp_dir) {
        $files = array_diff(scandir($temp_dir), array('.', '..'));

        foreach ($files as $file) {
            $cur_dir = $temp_dir . '/' . $file;
            is_dir($cur_dir) ? $this->rmTempDir($cur_dir) : unlink($cur_dir);
        }

        return rmdir($temp_dir);
    }

    /**
     * @param $file_pointer
     * @param $data
     */
    function putCsv($file_pointer, $data) {
        mb_convert_variables('SJIS-win', 'UTF-8', $data);
        fputcsv($file_pointer, $data);
    }

    /**
     * @param $filename
     * @param $chunkSize
     * @return bool|int
     */
    function readFileByChunk($filename, $chunkSize) {
        $buffer = "";
        $cnt = 0;
        $handle = fopen($filename, "rb");

        if ($handle === false) return false;

        while (!feof($handle)) {
            $buffer = fread($handle, $chunkSize);
            echo $buffer;
            ob_flush();
            flush();
            $cnt += strlen($buffer);
        }

        $status = fclose($handle);

        return $status && $cnt ? $cnt : false;
    }

    function readFileByChunkWithCleanBuffer($filename, $chunkSize) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $buffer = "";
        $cnt = 0;
        $handle = fopen($filename, "rb");

        if ($handle === false) return false;

        ob_start();

        while (!feof($handle)) {
            $buffer = fread($handle, $chunkSize);
            echo $buffer;
            ob_flush();
            flush();
            $cnt += strlen($buffer);
        }

        ob_flush();
        $status = fclose($handle);
        ob_end_clean();
        
        return $status && $cnt ? $cnt : false;
    }

    public function getTempFolderName($cp_id){
        return '/tmp/temp_download' . '_' . $cp_id . '/';
    }
}
