<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.DownloadDataTrait');
AAFW::import('jp.aainc.classes.brandco.segment.trait.SegmentActionTrait');
AAFW::import('jp.aainc.classes.services.SegmentCreateSqlService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class download_provision_data extends BrandcoGETActionBase {
    use DownloadDataTrait;
    use SegmentActionTrait;

    const CHUNK_SIZE = 1048576; // チャンクサイズ (bytes)

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    public $NeedAdminLogin = true;

    private $sp_ids_array;

    public function doThisFirst() {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 3600);
    }

    public function validate() {

        if (!$_GET) {
            return '404';
        }

        $is_valid = false;
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'sp_ids_') !== false) {
                $is_valid = true;
                $temp_rs = explode('sp_ids_', $key);
                $this->sp_ids_array[$temp_rs[1]] = $value;
            }
        }

        if (!$is_valid) {
            return '404';
        }
        
        if($this->isContainInvalidSegment(array_keys($this->sp_ids_array), $this->getBrand()->id)) {
            return '404';
        }

        return true;
    }

    public function doAction() {
        $segment_service = $this->getService('SegmentService');
        $create_sql_service = $this->getService('SegmentCreateSqlService');
        $segmenting_data_service = $this->getService('SegmentingUserDataService');
        $fan_list_download_service = $this->getService('FanListDownloadService');

        $this->saveSegmentActionLog(SegmentActionLog::TYPE_ACTION_DOWNLOAD);

        $date = date('Ymd');
        $zip_file_name = $date . '_segment_data.zip';
        $file_type = FanListDownloadService::TYPE_PROFILE;
        $temp_folder_name = '/tmp/temp_segment_' . $this->getBrand()->id . '/';

        $this->initFolder($temp_folder_name);

        // Zipにアーカイブ
        $zip = new ZipArchive();
        $zip_dir = $temp_folder_name . $zip_file_name;

        if ($zip->open($zip_dir, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE)) {
            $common_zip_file_name = $date . '_segment_data';
            mkdir($temp_folder_name . $common_zip_file_name, 0777, true);

            $page_info = array('brand_id' => $this->getBrand()->id);
            $entity_store = aafwEntityStoreFactory::create('Segments');

            // headerとrowsの取得に必要なデータを初回に用意
            $action_data = $fan_list_download_service->getActionData($file_type, $page_info);
            // headerを出力
            $header = $fan_list_download_service->getActionHeader($file_type, $action_data);
            mb_convert_variables('SJIS-win', 'UTF-8', $header);

            foreach (array_keys($this->sp_ids_array) as $s_id) {
                $cur_segment = $segment_service->getSegmentById($s_id);
                $cur_segment_provisions = $segment_service->getRawSegmentProvisionsBySegmentId($s_id);

                $segmenting_data_service->createTmpSegmentingUsers();

                try {
                    $entity_store->begin();

                    foreach ($cur_segment_provisions as $cur_segment_provision) {
                        $create_sql_service->resetCurrentParameter();

                        $cur_provision = json_decode($cur_segment_provision['provision'], true);
                        $segmenting_users = $segmenting_data_service->getSegmentingUsers($page_info, $cur_provision);

                        if (in_array($cur_segment_provision['id'], $this->sp_ids_array[$s_id])) {
                            $csv_file_name = $cur_segment->name . '_' . $cur_segment_provision['name'] . '_' . $cur_segment_provision['id'] . '.csv';
                            $csv_file_dir = $temp_folder_name . $common_zip_file_name . '/' . $csv_file_name;
                            $csv_fp = fopen($csv_file_dir, 'w');

                            fputcsv($csv_fp, $header);

                            // あらゆるidでのデータ抽出が必要なので、並び順となるtmp_idとそれぞれのidを紐付けておく
                            $join_users = array();
                            foreach ($segmenting_users as $segmenting_user) {
                                $join_users['user_id'][$segmenting_user['user_id']] = $segmenting_user['user_id'];
                                $join_users['relation_id'][$segmenting_user['user_id']] = $segmenting_user['brands_users_relations_id'];
                            }

                            $rows = $fan_list_download_service->getActionRows(FanListDownloadService::TYPE_PROFILE, $join_users, $page_info, $action_data);

                            mb_convert_variables('SJIS-win', 'UTF-8', $rows);
                            foreach ($rows as $row) {
                                fputcsv($csv_fp, $row);
                            }

                            // 完成したファイルを追加
                            fclose($csv_fp);
                            $zip->addFile($csv_file_dir, $csv_file_name);
                        }

                        $segmenting_data_service->insertTmpSegmentingUsersByQuery($segmenting_users);
                    }

                    $entity_store->commit();
                } catch (Exception $e) {
                    $entity_store->rollback();
                }

                $segmenting_data_service->dropTmpSegmentingUsers();
            }

            $zip->close();
            // ストリームに出力
            header('Content-Type: application/zip; name="' . $zip_file_name . '"');
            header('Content-Disposition: attachment; filename="' . $zip_file_name . '"');
            header('Content-Length: ' . filesize($zip_dir));
            $this->readFileByChunk($zip_dir, self::CHUNK_SIZE);

            $this->rmTempDir($temp_folder_name);
        }
        exit();
    }
}