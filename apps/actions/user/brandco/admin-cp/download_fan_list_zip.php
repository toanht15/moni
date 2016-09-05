<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.services.FanListDownloadService');
AAFW::import('jp.aainc.classes.brandco.cp.trait.DownloadDataTrait');

class download_fan_list_zip extends BrandcoGETActionBase {

    Use DownloadDataTrait;

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $date;
    private $zip_file_name;
    private $temp_folder_name;
    /** @var aafwDataBuilder $db */
    private $db;
    private $max_id;
    private $page_info;

    /** @var CpFlowService $cp_flow_service */
    private $cp_flow_service;
    private $cp_actions;
    private $cp_entry_action;

    private $is_cp_data_download_mode = false;
    private $can_download_brand_fan_list;

    const EXPORT_COUNT = 500;
    const CHUNK_SIZE = 1048576; // チャンクサイズ (bytes)

    public function doThisFirst() {
        $this->date = date('Ymd');
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 3600);
        $this->cp_id    = $this->GET['exts'][0];
        $this->brand    = $this->getBrand();
        $this->file_ids = $this->GET['file_ids'];

        /** BrandGlobalSettingService $brand_global_settings_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $this->can_download_brand_fan_list = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_DOWNLOAD_BRAND_FAN_LIST);

        if ($this->cp_id) {
            $this->searchCondition = $this->getSearchConditionSession($this->cp_id);
            $this->is_cp_data_download_mode = true;
            $this->initForCpActionDownloadType($this->cp_id);

        } else {
            $this->searchCondition = $this->getBrandSession('searchBrandCondition');
        }
        $this->orderCondition = $this->getBrandSession('orderCondition');

        $this->page_info = array(
            'cp_id'     => $this->cp_id,
            'action_id' => $this->cp_entry_action->id,
            'brand_id'  => $this->brand->id,
            'tab_no'    => CpCreateSqlService::TAB_PAGE_PROFILE
        );

        $this->db = aafwDataBuilder::newBuilder();
    }

    public function beforeValidate() {

        if (!$this->brand) {
            return '404';
        }

        if (!$this->is_cp_data_download_mode && !$this->can_download_brand_fan_list){
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'fan_list_download');
        }

        if (!$this->file_ids) {
            if ($this->is_cp_data_download_mode) {
                return 'redirect: ' . Util::rewriteUrl('admin-cp', 'fan_list_download', array($this->cp_id));
            }
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'fan_list_download');
        }

        if (!$this->is_cp_data_download_mode && $this->file_ids[0] != FanListDownloadService::TYPE_PROFILE) {
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'fan_list_download', array(),array('mid' => 'invalid-file-ids'));
        }

        foreach ($this->file_ids as $file_id) {
            if ($this->is_cp_data_download_mode && $file_id != FanListDownloadService::TYPE_PROFILE && !$this->isThisCpActionId($file_id)) {
                return 'redirect: ' . Util::rewriteUrl('admin-cp', 'fan_list_download', array($this->cp_id), array('mid' => 'invalid-file-ids'));
            }
        }
    }

    public function validate() {
        $validatorService = new CpValidator($this->brand->id);

        if($this->is_cp_data_download_mode){
            return $validatorService->isOwner($this->cp_id);
        }

        return true;
    }

    function doAction() {

        $relation = $this->getBrandsUsersRelation();
        /** @var FanListDownloadService $fan_list_download_service */
        $fan_list_download_service = $this->getService('FanListDownloadService');
        $history = $fan_list_download_service->createFanListDlHistory($relation->user_id, $this->brand->id, $this->searchCondition, $this->file_ids);

        try {
            $this->zip_file_name = $this->getZipFileName();

            if($this->is_cp_data_download_mode) {
                $this->temp_folder_name = $this->getTempFolderName($this->cp_id);
            }else{
                $this->temp_folder_name = $this->getTempFolderName($this->brand->id);
            }

            $this->initFolder($this->temp_folder_name);

            // Zipにアーカイブ
            $zip = new ZipArchive();
            $zip_dir = $this->temp_folder_name . $this->zip_file_name;

            if ($zip->open($zip_dir, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE)) {

                if($this->is_cp_data_download_mode){
                    $common_zip_file_name = $this->date . '_monipla_' . $this->cp_id;
                }else{
                    $common_zip_file_name = $this->date . '_brand_' . $this->brand->id;
                }
                mkdir($this->temp_folder_name . $common_zip_file_name, 0777, true);

                // 絞り込み条件でユーザIDを一時テーブルに抽出
                $this->createTmpJoinUsers();
                $this->insertTmpJoinUsers();
                $this->max_id = $this->getMaxId();

                $this->page_info['is_manager'] = $this->Data['pageStatus']['manager']->id;

                // ファイル単位で取得
                foreach ($this->file_ids as $file_id) {
                    if ($file_id == FanListDownloadService::TYPE_PROFILE) {
                        if ($this->cp_entry_action) {
                            $this->page_info['action_id'] = $this->cp_entry_action->id;
                        }
                        $file_type = FanListDownloadService::TYPE_PROFILE;
                        $csv_file_name = $this->date . "_" . FanListDownloadService::$download_file_name[$file_type] . '.csv';
                    } else {
                        if (!$this->isThisCpActionId($file_id)) {
                            continue;
                        }
                        $cp_action = $this->cp_flow_service->getCpActionById($file_id);
                        $this->page_info['action_id'] = $cp_action->id;
                        $file_type = $cp_action->type;
                        $step_no = $cp_action->getStepNo();
                        $csv_file_name = $this->date . "_Step" . $step_no . "_" . FanListDownloadService::$download_file_name[$file_type] . '.csv';
                    }

                    $csv_file_dir = $this->temp_folder_name . $common_zip_file_name . '/' . $csv_file_name;
                    $csv_fp = fopen($csv_file_dir, 'w');

                    // headerとrowsの取得に必要なデータを初回に用意
                    $action_data = $fan_list_download_service->getActionData(
                        $file_type,
                        $this->page_info
                    );

                    // headerを出力
                    $header = $fan_list_download_service->getActionHeader(
                        $file_type,
                        $action_data
                    );
                    mb_convert_variables('SJIS-win', 'UTF-8', $header);
                    fputcsv($csv_fp, $header);

                    // rowsを出力
                    $i = 1;
                    $offset = 0;
                    while ($offset < $this->max_id) {
                        $offset += self::EXPORT_COUNT;
                        $rs = $this->getTmpJoinUsers($i);

                        // あらゆるidでのデータ抽出が必要なので、並び順となるtmp_idとそれぞれのidを紐付けておく
                        $join_users = array();
                        while ($tmp_join_user = $this->db->fetch($rs)) {
                            $join_users['user_id'][$tmp_join_user['id']] = $tmp_join_user['user_id'];
                            $join_users['relation_id'][$tmp_join_user['id']] = $tmp_join_user['relation_id'];
                            $join_users['cp_user_id'][$tmp_join_user['id']] = $tmp_join_user['cp_user_id'] ? : 0;
                        }

                        $rows = $fan_list_download_service->getActionRows(
                            $file_type,
                            $join_users,
                            $this->page_info,
                            $action_data
                        );

                        foreach($join_users['user_id'] as $tmp_id => $user_id) {
                            $data_csv[$tmp_id][] = $rows[$user_id]['no'];
                        }

                        mb_convert_variables('SJIS-win', 'UTF-8', $rows);
                        foreach ($rows as $row) {
                            fputcsv($csv_fp, $row);
                        }
                        ++$i;
                    }

                    // 完成したファイルを追加
                    fclose($csv_fp);
                    $zip->addFile($csv_file_dir, $csv_file_name);
                }
                $zip->close();
            }

            // ストリームに出力
            header('Content-Type: application/zip; name="' . $this->zip_file_name . '"');
            header('Content-Disposition: attachment; filename="' . $this->zip_file_name . '"');
            header('Content-Length: ' . filesize($zip_dir));
            $this->readFileByChunk($zip_dir, self::CHUNK_SIZE);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            if($this->is_cp_data_download_mode){
                $logger->error('download_join_user_zip@doAction Error! cp_id = '.$this->cp_id.' Error: ' . $e);
                return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array(
                    $this->cp_id,
                    $this->cp_action_id),
                    array('mid' => 'download-failed')
                );
            }

            $logger->error('download_join_user_zip@doAction Error! brand_id = '.$this->brand->id.' Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'fan_list_download', array(), array('mid' => 'download-failed'));
        }

        $fan_list_download_service->completeFanListDlHistory($history);
        // 一時ファイルを削除しておく
        $this->rmTempDir($this->temp_folder_name);
        exit();
    }

    /**
     * CPアクションダウンロードタイプを用意する
     * @param $cp_id
     */
    private function initForCpActionDownloadType($cp_id){
        $this->cp_flow_service = $this->getService('CpFlowService');
        $this->cp_actions = $this->cp_flow_service->getCpActionsByCpId($cp_id);
        $this->cp_entry_action = $this->cp_flow_service->getEntryActionByCpId($cp_id);
    }

    /**
     * @param $i
     * @return array
     */
    private function getTmpJoinUsers($i) {
        if ($this->is_cp_data_download_mode) {
            $query = "/* download_join_user_zip SELECT tmp_join_user */
                SELECT id, user_id, relation_id, cp_user_id FROM tmp_join_users";
        } else {
            $query = "/* download_join_user_zip SELECT tmp_join_user */
                SELECT id, user_id, relation_id FROM tmp_join_users";
        }

        $args = array(array('__NOFETCH__' => true), array(), array('page' => $i, 'count' => self::EXPORT_COUNT));
        $tmp_join_users = $this->db->getBySQL($query, $args);
        return $tmp_join_users;
    }

    /**
     * @throws aafwException
     */
    private function createTmpJoinUsers() {
        $query = "/* download_join_user_zip CREATE tmp_join_users */
                    CREATE TEMPORARY TABLE tmp_join_users(
                        id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        user_id int(20),
                        relation_id int(10))";

        if ($this->is_cp_data_download_mode) {
            $query = substr($query, 0, strlen($query) - 1);
            $query .= ",cp_user_id int(10))";
        }

        $result = $this->db->executeUpdate($query);
        if (!$result) {
            throw new aafwException("createTempJoinUsers FAILED!: " . $result);
        }
    }

    /**
     * @return mixed
     * @throws aafwException
     */
    private function insertTmpJoinUsers() {
        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->getService('CpUserListService');
        $select_query = $cp_user_list_service->getAllFanListSQL(
            $this->page_info,
            $this->searchCondition,
            $this->orderCondition
        );

        if ($this->is_cp_data_download_mode) {
            $query = "/* download_join_user_zip INSERT tmp_join_users */
                    INSERT INTO tmp_join_users(user_id, relation_id, cp_user_id) ";
        } else {
            $query = "/* download_join_user_zip INSERT tmp_join_users */
                    INSERT INTO tmp_join_users(user_id, relation_id) ";
        }
        $query .= $select_query;
        $result = $this->db->executeUpdate($query);
        if (!$result) {
            throw new aafwException("insertTmpJoinUsers FAILED!: ".$result);
        };
    }

    private function getMaxId() {
        $rs = $this->db->executeUpdate('SELECT MAX(id) max_id FROM tmp_join_users');
        return $this->db->fetchResultSet($rs)['max_id'];
    }

    private function isThisCpActionId($file_id) {
        if (!ctype_digit($file_id)) return false;

        foreach ($this->cp_actions as $cp_action) {
            if ($cp_action->id == $file_id) {
                return true;
            }
        }
        return false;
    }

    public function getZipFileName() {
        if ($this->is_cp_data_download_mode) {
            return $this->date . '_monipla_' . $this->cp_id . '.zip';
        }
        return $this->date . '_brand_' . $this->brand->id . '.zip';
    }
}
