<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.DownloadDataActionBase');
AAFW::import('jp.aainc.vendor.PHPExcel.Classes.PHPExcel');
AAFW::import('jp.aainc.vendor.PHPExcel.Classes.PHPExcel.IOFactory');

class download_instagram_hashtag_post_image_zip extends DownloadDataActionBase {

    const ZIP_FILE_NAME_CSV = "INSTAGRAM_HASHTAG_CSV";
    const ZIP_FILE_NAME_EXCEL = "INSTAGRAM_HASHTAG_EXCEL";

    const CHUNK_SIZE = 1048576; // チャンクサイズ (bytes)

    const FILE_TYPE_CSV = 1;
    const FILE_TYPE_EXCEL = 2;

    /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
    private $cp_instagram_hashtag_action_service;
    private $file_type;
    private $zip;
    private $empty_flg; //データがあるかどうかチェックフラグ

    public function doThisFirst() {
        parent::doThisFirst();
        $this->file_type = $this->GET['file_type'];
    }

    function doAction() {
        /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
        $instagram_hashtag_user_service = $this->createService('InstagramHashtagUserService');

        $this->cp_instagram_hashtag_action_service = $this->createService('CpInstagramHashtagActionService');

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');

        try {
            // Zipにアーカイブ
            $this->zip = new ZipArchive();
            $zip_dir = $this->temp_folder_name . $this->zip_file_name;

            if ($this->zip->open($zip_dir, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE)) {
                $this->empty_flg = true;
                $cp_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($this->Data['cp_id'], CpAction::TYPE_INSTAGRAM_HASHTAG);
                $common_zip_folder_name = $this->file_type == self::FILE_TYPE_CSV ? self::ZIP_FILE_NAME_CSV . '_' . $this->Data['cp_id'] : self::ZIP_FILE_NAME_EXCEL . '_' . $this->Data['cp_id'];

                foreach ($cp_actions as $cp_action) {
                    // 投稿一覧取得
                    $instagram_hashtag_users = $instagram_hashtag_user_service->getInstagramHashtagUsersByCpActionId($cp_action->id);
                    if (!$instagram_hashtag_users) continue;

                    if ($this->file_type == self::FILE_TYPE_CSV) {  //出力ファイルフォーマットはCSVの場合は
                        $this->exportToCsvFile($cp_action->id, $common_zip_folder_name, $instagram_hashtag_users);
                    } elseif ($this->file_type == self::FILE_TYPE_EXCEL) { //出力ファイルフォーマットはExcelの場合は
                        $this->exportToExcelFile($cp_action->id, $common_zip_folder_name, $instagram_hashtag_users);
                    }
                }

                $this->zip->close();

                if ($this->empty_flg) {
                    return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'photo-download-failed'));
                }

                // ストリームに出力
                header('Content-Type: application/zip; name="' . $this->zip_file_name . '"');
                header('Content-Disposition: attachment; filename="' . $this->zip_file_name . '"');
                header('Content-Length: ' . filesize($zip_dir));
                $this->readFileByChunk($zip_dir,self::CHUNK_SIZE);
            }
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('download_instagram_hashtag_post_image_zip@doAction Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'photo-download-error'));
        }

        // 一時ファイルを削除しておく
        $this->rmTempDir($this->temp_folder_name);
        exit();
    }

    /**
     * CSVフィアルでデータを出力し、投稿画像は別のファイルを出力する
     * @param $cp_action_id
     * @param $common_zip_folder_name
     * @param $instagram_hashtag_users
     */
    private function exportToCsvFile($cp_action_id, $common_zip_folder_name, $instagram_hashtag_users) {
        $cp_instagram_hashtag_action = $this->cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($cp_action_id);

        $sub_folder_name = $common_zip_folder_name . '/' . mb_convert_encoding($cp_action_id . '_' . $cp_instagram_hashtag_action->title, "SJIS", "UTF-8");
        $cur_temp_folder_name = $this->temp_folder_name . $sub_folder_name . '/';

        $this->zip->addEmptyDir($sub_folder_name);
        if (!file_exists($cur_temp_folder_name)) {
            mkdir($cur_temp_folder_name, 0777, true);
        }

        $csv_file_name = $cp_action_id . '.csv';
        $csv_file_dir = $cur_temp_folder_name . $csv_file_name;
        $csv_fp = fopen($csv_file_dir, 'w');

        $header = $this->csv_file_header;
        $header[] = '投稿写真URL';
        $header[] = 'コメント';
        if (empty($this->is_hide_personal_info)) {
            $header[] = 'ユーザネーム';
        }
        $header[] = 'ユーザネーム重複';
        $header[] = '登録投稿順序';
        $header[] = '検閲';
        $header[] = '登録日時';
        $header[] = '投稿日時';

        $this->putCsv($csv_fp, $header);

        foreach ($instagram_hashtag_users as $instagram_hashtag_user) {
            $i = 1;

            if (!$instagram_hashtag_user->isExistsInstagramHashtagUserPosts()) continue;

            foreach ($instagram_hashtag_user->getInstagramHashtagUserPosts() as $instagram_hashtag_user_post) {
                $instagram_hashtag_user = $instagram_hashtag_user_post->getInstagramHashtagUser();

                $instagram_hashtag_name = $this->getInstagramHashtagNameByInstagramHashtagUser($instagram_hashtag_user, $instagram_hashtag_user_post, $i);
                $photo_dir = $cur_temp_folder_name . $instagram_hashtag_name;
                $photo_dir_in_zip = $sub_folder_name . '/' . $instagram_hashtag_name;

                file_put_contents($photo_dir, file_get_contents($instagram_hashtag_user_post->standard_resolution));
                $this->zip->addFile($photo_dir, $photo_dir_in_zip);

                // Add data to csv file
                $val = array($cp_instagram_hashtag_action->title, $this->brands_users_relation->no, $instagram_hashtag_name);

                $val[] = $instagram_hashtag_user_post->standard_resolution;
                $val[] = json_decode($instagram_hashtag_user_post->detail_data)->caption->text;
                if (empty($this->is_hide_personal_info)) {
                    $val[] = $instagram_hashtag_user_post->user_name;
                }
                $val[] = $instagram_hashtag_user->duplicate_flg ? 'あり' : 'なし';
                $val[] = $instagram_hashtag_user_post->getReversePostTimeStatus();
                $val[] = $instagram_hashtag_user_post->getApprovalStatus();
                $val[] = date('Y/m/d H:i', strtotime($instagram_hashtag_user->created_at));
                $val[] = date('Y/m/d H:i', json_decode($instagram_hashtag_user_post->detail_data)->created_time);

                $this->putCsv($csv_fp, $val);
                $this->empty_flg = false;
                $i++;
            }
        }

        fclose($csv_fp);
        $this->zip->addFile($csv_file_dir, $sub_folder_name . '/' . $csv_file_name);
    }

    /**
     * エクセルファイルでデータと投稿画像をまとめて出力する
     * @param $cp_action_id
     * @param $common_zip_folder_name
     * @param $instagram_hashtag_users
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    private function exportToExcelFile($cp_action_id, $common_zip_folder_name, $instagram_hashtag_users) {
        $cp_instagram_hashtag_action = $this->cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($cp_action_id);

        $sub_folder_name = $common_zip_folder_name . '/' . mb_convert_encoding($cp_action_id . '_' . $cp_instagram_hashtag_action->title, "SJIS", "UTF-8");
        $cur_temp_folder_name = $this->temp_folder_name . $sub_folder_name . '/';

        $this->zip->addEmptyDir($sub_folder_name);
        if (!file_exists($cur_temp_folder_name)) {
            mkdir($cur_temp_folder_name, 0777, true);
        }

        //Excelフィアルを作成する
        $excel_file_name = $cp_action_id . '.xlsx';
        $excel_file_dir = $cur_temp_folder_name . $excel_file_name;

        $php_excel = new PHPExcel();
        $php_excel->getProperties()->setTitle($cp_instagram_hashtag_action->title)->setDescription('none');
        $php_excel->setActiveSheetIndex(0);
        $php_excel->getActiveSheet()->setTitle('Instagram投稿写真');

        //ExcelファイルのHeaderを作成する
        $excel_header = $this->buildHeaderForExcel();
        $col = 0;
        $row = 1;
        foreach ($excel_header as $key => $value) {
            //セールのサイズをセットする
            $excel_column_name = PHPExcel_Cell::stringFromColumnIndex($key);
            $php_excel->getActiveSheet()->getColumnDimension($excel_column_name)->setWidth(15);

            //エクセルのheaderをセットする
            $php_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);

            //Headerに太字フォントをセットする
            $php_excel->getActiveSheet()->getStyle($excel_column_name.$row)->getFont()->setBold(true);
            $col++;
        }

        //データをExcelファイルに保存する
        $row = 2;
        foreach ($instagram_hashtag_users as $instagram_hashtag_user) {
            if (!$instagram_hashtag_user->isExistsInstagramHashtagUserPosts()) continue;

            foreach ($instagram_hashtag_user->getInstagramHashtagUserPosts() as $instagram_hashtag_user_post) {
                //テキストデータをExcelファイルに入れる
                $excel_data = $this->prepareDataForExcel($excel_header, $instagram_hashtag_user_post, $cp_instagram_hashtag_action->title);
                foreach ($excel_data as $key => $value) {
                    $php_excel->getActiveSheet()->setCellValueByColumnAndRow($key, $row, $value);
                }

                //投稿画像をExcelに追加する
                $image_column_name = PHPExcel_Cell::stringFromColumnIndex(array_search('投稿画像', $excel_header));
                $this->insertImageToExcel($php_excel, $instagram_hashtag_user_post, $row, $image_column_name);

                $row++;
                $this->empty_flg = false;
            }
        }
        $php_excel->setActiveSheetIndex(0);

        //ファイルを保存する
        $obj_writer = PHPExcel_IOFactory::createWriter($php_excel, 'Excel2007');
        $obj_writer->save($excel_file_dir);

        $this->zip->addFile($excel_file_dir, $sub_folder_name . '/' . $excel_file_name);
    }

    /**
     * エクセルファイルのデータを準備する
     * @param $excel_header
     * @param $instagram_hashtag_user_post
     * @param $instagram_hashtag_action_title
     * @return array
     */
    private function prepareDataForExcel ($excel_header, $instagram_hashtag_user_post, $instagram_hashtag_action_title) {
        $instagram_hashtag_user = $instagram_hashtag_user_post->getInstagramHashtagUser();
        $cp_user = $this->cp_user_service->getCpUserById($instagram_hashtag_user->cp_user_id);
        $brands_users_relation = $this->brands_users_relation_service->getBrandsUsersRelation($this->getBrand()->id, $cp_user->user_id);

        $data = array();
        foreach ($excel_header as $key => $value) {
            if($value === 'モジュールタイトル') {
                $data[$key] = $instagram_hashtag_action_title;
            }
            if($value === '会員No') {
                $data[$key] = $brands_users_relation->no;
            }
            if($value === '投稿写真URL') {
                $data[$key] = $instagram_hashtag_user_post->standard_resolution;
            }
            if($value === 'コメント') {
                $data[$key] = json_decode($instagram_hashtag_user_post->detail_data)->caption->text;
            }
            if($value === 'ユーザネーム') {
                $data[$key] = $instagram_hashtag_user_post->user_name;
            }
            if($value === 'ユーザネーム重複') {
                $data[$key] = $instagram_hashtag_user->duplicate_flg ? 'あり' : 'なし';
            }
            if($value === '登録投稿順序') {
                $data[$key] = $instagram_hashtag_user_post->getReversePostTimeStatus();
            }
            if($value === '検閲') {
                $data[$key] = $instagram_hashtag_user_post->getApprovalStatus();
            }
            if($value === '登録日時') {
                $data[$key] = date('Y/m/d H:i', strtotime($instagram_hashtag_user->created_at));
            }
            if($value === '投稿日時') {
                $data[$key] = date('Y/m/d H:i', json_decode($instagram_hashtag_user_post->detail_data)->created_time);
            }
        }

        return $data;
    }


    /**
     * ExcelファイルのHeaderを作成する
     * @return array
     */
    private function buildHeaderForExcel() {
        $header = array(
            'モジュールタイトル',
            '会員No',
            '投稿画像',
            '投稿写真URL',
            'コメント'
        );
        if (empty($this->is_hide_personal_info)) {
            $header[] = 'ユーザネーム';
        }
        $header[] = 'ユーザネーム重複';
        $header[] = '登録投稿順序';
        $header[] = '検閲';
        $header[] = '登録日時';
        $header[] = '投稿日時';

        return $header;
    }

    /**
     * 投稿画像をExcelに追加
     * @param $php_excel
     * @param $instagram_hashtag_user_post
     * @param $current_row
     * @param $image_column
     * @throws PHPExcel_Exception
     */
    private function insertImageToExcel($php_excel, $instagram_hashtag_user_post, $current_row, $image_column) {
        $image_url = $instagram_hashtag_user_post->thumbnail;

        //temp image direction
        $tmp_image_dir = '/tmp/' . uniqid();
        file_put_contents($tmp_image_dir, file_get_contents($image_url));
        $image_resource = $this->createImageResource($tmp_image_dir);

        if (!$image_resource) {
            return;
        }

        //写真にExcelに入れる
        $obj_drawing = new PHPExcel_Worksheet_MemoryDrawing();
        $obj_drawing->setImageResource($image_resource);
        $obj_drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $obj_drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $obj_drawing->setHeight(100);
        $obj_drawing->setWidth(100);
        $obj_drawing->setCoordinates($image_column . $current_row);
        $obj_drawing->setWorksheet($php_excel->getActiveSheet());
        $php_excel->getActiveSheet()->getRowDimension($current_row)->setRowHeight(100);

        //tempファイルを削除する
        unlink($tmp_image_dir);
    }

    /**
     * 画像リソースを作成
     * @param $image_dir
     * @return null|resource
     */
    private function createImageResource($image_dir) {
        switch (exif_imagetype($image_dir)) {
            case IMAGETYPE_GIF:
                return imagecreatefromgif($image_dir);
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($image_dir);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($image_dir);
            default:
                return null;
        }
    }

    /**
     * @param $instagram_hashtag_user_post
     * @return string
     */
    private function getInstagramHashtagNameByInstagramHashtagUser($instagram_hashtag_user, $instagram_hashtag_user_post, $increment = 1) {
        $cp_user = $this->cp_user_service->getCpUserById($instagram_hashtag_user->cp_user_id);
        $this->brands_users_relation = $this->brands_users_relation_service->getBrandsUsersRelation($this->getBrand()->id, $cp_user->user_id);

        // GETパラメータの削除
        $path_without_request_params = strtok($instagram_hashtag_user_post->standard_resolution, '?');
        $extension = pathinfo($path_without_request_params, PATHINFO_EXTENSION);

        return $this->brands_users_relation->no . '(' . $increment . ').' . $extension;
    }

    public function getZipFileName($cp_id){
        return $this->file_type == self::FILE_TYPE_CSV ? self::ZIP_FILE_NAME_CSV . '_' . $cp_id . '.zip' : self::ZIP_FILE_NAME_EXCEL . '_' . $cp_id . '.zip';
    }
}
