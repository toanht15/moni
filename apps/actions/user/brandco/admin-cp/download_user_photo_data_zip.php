<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.DownloadDataActionBase');
AAFW::import('jp.aainc.vendor.PHPExcel.Classes.PHPExcel');
AAFW::import('jp.aainc.vendor.PHPExcel.Classes.PHPExcel.IOFactory');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');


/**
 * 写真投稿の画像とユーザデータをまとめてExcelファイルでダウンロードするクラス
 * Class download_user_photo_data_zip
 */
class download_user_photo_data_zip extends DownloadDataActionBase {

    const ZIP_FILE_NAME = "USER_PHOTO";
    const CHUNK_SIZE = 1572864; // チャンクサイズ (bytes)

    private $page_info;

    public function doThisFirst() {
        parent::doThisFirst();
        $this->page_info = array(
            'cp_id' => $this->Data['cp_id'],
            'brand_id' => $this->getBrand()->id,
            'tab_no' => CpCreateSqlService::TAB_PAGE_PROFILE
        );
    }

    public function doAction() {
        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = $this->createService('PhotoUserService');
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');

        $logger = aafwLog4phpLogger::getDefaultLogger();

        try {
            // Zipにアーカイブ
            $zip = new ZipArchive();
            $zip_dir = $this->temp_folder_name . $this->zip_file_name;

            if ($zip->open($zip_dir, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE)) {
                $empty_flg = true;
                $cp_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($this->Data['cp_id'], CpAction::TYPE_PHOTO);
                $common_zip_folder_name = self::ZIP_FILE_NAME . '_' . $this->Data['cp_id'];

                foreach ($cp_actions as $cp_action) {
                    // 写真投稿一覧取得
                    //TODO 未承認画像もダウンロードする
                    $photo_user_list = $photo_user_service->getPhotoUsersByActionIds($cp_action->id);
                    if (!$photo_user_list) continue;

                    $cp_photo_action = $photo_user_service->getCpPhotoActionByCpActionId($cp_action->id);

                    $sub_folder_name = $common_zip_folder_name . '/' . mb_convert_encoding($cp_action->id . '_' . $cp_photo_action->title, "SJIS", "UTF-8");
                    $cur_temp_folder_name = $this->temp_folder_name . $sub_folder_name . '/';

                    $zip->addEmptyDir($sub_folder_name);
                    if (!file_exists($cur_temp_folder_name)) {
                        mkdir($cur_temp_folder_name, 0777, true);
                    }

                    $this->page_info['action_id'] = $cp_action->id;

                    $excel_file_name = $cp_action->id . '_user_photo_data.xlsx';
                    $excel_file_dir = $cur_temp_folder_name . $excel_file_name;

                    //Excelファイルを作成する
                    $php_excel = new PHPExcel();
                    $php_excel->getProperties()->setTitle($cp_photo_action->title)->setDescription('none');
                    $php_excel->setActiveSheetIndex(0);
                    $php_excel->getActiveSheet()->setTitle('写真投稿');

                    //Headerを作成する
                    $excel_file_header = $this->createExcelHeaderByPhotoAction($cp_photo_action);
                    $col = 0;
                    $row = 1;
                    foreach ($excel_file_header as $key => $value) {
                        $column_name = PHPExcel_Cell::stringFromColumnIndex($key);
                        $php_excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);

                        if ($value == '投稿画像') { //投稿画像カラムの幅をセットする
                            $php_excel->getActiveSheet()->getColumnDimension($column_name)->setWidth(15);
                        }

                        if ($value == 'タイトル') { //タイトルカラムの幅をセットする
                            $php_excel->getActiveSheet()->getColumnDimension($column_name)->setWidth(20);
                        }

                        if ($value == 'コメント') { //コメントカラムの幅をセットする
                            $php_excel->getActiveSheet()->getColumnDimension($column_name)->setWidth(50);
                        }
                        //Headerに太字フォントをセットする
                        $php_excel->getActiveSheet()->getStyle($column_name.$row)->getFont()->setBold(true);

                        $col++;
                    }

                    //ユーザープロファイルデータを取得する
                    $users_profile_data = $this->getUsersInfoByPhotoUserList($photo_user_list);

                    //データをExcelファイルに入れる
                    $row = 2;
                    foreach ($photo_user_list as $photo_user) {
                        //ユーザープロファイルデータをExcelに保存
                        $prepare_data = $this->prepareDataForExcel($excel_file_header, $users_profile_data, $photo_user);

                        //データをセールに入れる
                        foreach($prepare_data as $key => $value) {
                            $php_excel->getActiveSheet()->setCellValueByColumnAndRow($key, $row, $value);
                        }

                        //タイトルやコメントカラムのセールスタイルをセットする
                        if ($cp_photo_action->title_required) {
                            $column_name = PHPExcel_Cell::stringFromColumnIndex(array_search('タイトル', $excel_file_header));
                            $php_excel->getActiveSheet()->getStyle($column_name . $row)->getAlignment()->setWrapText(true);
                        }
                        if ($cp_photo_action->comment_required) {
                            $column_name = PHPExcel_Cell::stringFromColumnIndex(array_search('コメント', $excel_file_header));
                            $php_excel->getActiveSheet()->getStyle($column_name . $row)->getAlignment()->setWrapText(true);
                        }

                        //投稿画像をExcelに追加する
                        $image_column_name = PHPExcel_Cell::stringFromColumnIndex(array_search('投稿画像', $excel_file_header));
                        $this->insertImageToExcel($php_excel, $photo_user, $row, $image_column_name);

                        $row++;
                        $empty_flg = false;
                    }

                    $php_excel->setActiveSheetIndex(0);

                    //save to file
                    $obj_writer = PHPExcel_IOFactory::createWriter($php_excel, 'Excel2007');
                    $obj_writer->save($excel_file_dir);

                    $zip->addFile($excel_file_dir, $sub_folder_name . '/' . $excel_file_name);
                }
                $zip->close();

                if ($empty_flg) {
                    return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'photo-download-failed'));
                }

                // ストリームに出力
                $logger->error('download_user_photo_data@doAction Start downloading');
                header('Content-Type: application/zip; name="' . $this->zip_file_name . '"');
                header('Content-Disposition: attachment; filename="' . $this->zip_file_name . '"');
                header('Content-Length: ' . filesize($zip_dir));

                $this->readFileByChunk($zip_dir, self::CHUNK_SIZE);
            }
        } catch (Exception $e) {
            $logger->error('download_user_photo_data@doAction Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'photo-download-failed'));
        }

        // 一時ファイルを削除しておく
        $this->rmTempDir($this->temp_folder_name);
        exit();
    }

    /**
     * ExcelフィアルのHeaderを作成
     * @param $cp_photo_action
     * @return array
     */
    public function createExcelHeaderByPhotoAction($cp_photo_action) {
        $excel_file_header = array(
            '会員No',
            '性別',
            '年齢',
            '都道府県',
            'Facebook友達数',
            'Twitterフォロー数',
            'Instagramフォロー',
            '投稿画像',
            'ステータス'
        );

        if ($cp_photo_action->title_required) {
            $excel_file_header[] = "タイトル";
        }

        if ($cp_photo_action->comment_required) {
            $excel_file_header[] = "コメント";
        }

        return $excel_file_header;
    }

    /**
     * Excelファイルのデータを準備する
     * @param $excel_header
     * @param $users_profile_data
     * @param $photo_user
     * @return array
     */
    private function prepareDataForExcel($excel_header, $users_profile_data, $photo_user) {
        $cp_user = $this->cp_user_service->getCpUserById($photo_user->cp_user_id);
        $data = array();

        foreach ($excel_header as $key => $value) {
            if($value === '会員No') {
                $data[$key] = $users_profile_data[$cp_user->user_id]['no'];
            }
            if($value === '性別') {
                $data[$key] = $users_profile_data[$cp_user->user_id]['sex'];
            }
            if($value === '年齢') {
                $data[$key] = $users_profile_data[$cp_user->user_id]['age'];
            }
            if($value === '都道府県') {
                $data[$key] = $users_profile_data[$cp_user->user_id]['pref_name'];
            }
            if($value === 'Facebook友達数') {
                $data[$key] = $users_profile_data[$cp_user->user_id]['sa1_friend_count'];
            }
            if($value === 'Twitterフォロー数') {
                $data[$key] = $users_profile_data[$cp_user->user_id]['sa3_friend_count'];
            }
            if($value === 'Instagramフォロー') {
                $data[$key] = $users_profile_data[$cp_user->user_id]['sa7_friend_count'];
            }
            if($value === 'ステータス') {
                $data[$key] = $photo_user->getApprovalStatus();
            }
            if($value === 'タイトル') {
                $data[$key] = $photo_user->photo_title;
            }
            if($value === 'コメント') {
                $data[$key] = $photo_user->photo_comment;
            }
        }

        return $data;
    }

    /**
     * @param $photo_user_list
     * @return array
     * @throws Exception
     */
    private function getUsersInfoByPhotoUserList($photo_user_list) {
        /** @var FanListDownloadService $fan_list_download_service */
        $fan_list_download_service = $this->getService('FanListDownloadService');
        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->getService('CpUserListService');

        $action_data = $fan_list_download_service->getActionData(
            FanListDownloadService::TYPE_PROFILE,
            $this->page_info
        );

        $user_ids = array();
        foreach ($photo_user_list as $photo_user) {
            $cp_user = $this->cp_user_service->getCpUserById($photo_user->cp_user_id);
            $user_ids[] = $cp_user->user_id;
        }

        return $cp_user_list_service->getFanListProfileForActionDataDownLoad($user_ids, $this->page_info['brand_id'], $action_data);
    }

    /**
     * 写真投稿をExcelに追加
     * @param $php_excel
     * @param $photo_user
     * @param $current_row
     * @param $column_name
     * @throws PHPExcel_Exception
     */
    private function insertImageToExcel($php_excel, $photo_user, $current_row, $column_name) {
        $image_url = $photo_user->getMiddlePhoto();
        if (!file_get_contents($image_url)) {
            $image_url = $photo_user->photo_url;
        }

        //temp photo direction
        $tmp_image_dir = '/tmp/' . uniqid();
        file_put_contents($tmp_image_dir, file_get_contents($image_url));
        $image_resource = $this->createImageResource($tmp_image_dir);

        if (!$image_resource) {
            return;
        }

        //写真にExcelに入れる
        $obj_drawing = new PHPExcel_Worksheet_MemoryDrawing();
        $obj_drawing->setDescription($photo_user->photo_title);
        $obj_drawing->setImageResource($image_resource);
        $obj_drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $obj_drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $obj_drawing->setHeight(100);
        $obj_drawing->setWidth(100);
        $obj_drawing->setCoordinates($column_name . $current_row);
        $obj_drawing->setWorksheet($php_excel->getActiveSheet());
        $php_excel->getActiveSheet()->getRowDimension($current_row)->setRowHeight(100);

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

    public function getZipFileName($cp_id) {
        return self::ZIP_FILE_NAME . '_' . $cp_id . '.zip';
    }
}