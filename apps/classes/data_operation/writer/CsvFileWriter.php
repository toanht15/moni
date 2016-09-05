<?php

/**
 * Class CsvFileWriter
 */
class CsvFileWriter {

    /**
     * @param $filepath
     * @param array|generator $data
     */
    public function write($filepath, $data) {

        $handle = fopen($filepath, 'W');

        // CSVをエクセルで開くことを想定して文字コードをSJISへ変換する設定を行う
        stream_filter_append($handle, 'convert.iconv.UTF-8/CP932', STREAM_FILTER_WRITE);

        try {
            foreach($data as $row) {
                fputcsv($handle, $row);
            }
        } finally {
            fclose($handle);
        }
    }
}