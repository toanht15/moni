<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.DownloadDataActionBase');

class download_photo_image_zip extends DownloadDataActionBase {

    const ZIP_FILE_NAME = "PHOTO";
    const CHUNK_SIZE = 1572864; // チャンクサイズ (bytes)

    function doAction() {
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

                $cp = $cp_flow_service->getCpById($this->Data['cp_id']);

                foreach ($cp_actions as $cp_action) {
                    // 写真投稿一覧取得
                    if ($this->Data['cp_id'] == 4021 || $cp->type == Cp::TYPE_MESSAGE) {
                        $logger->error('download_photo_image_zip@doAction Start fetching cp_action_id: ' . $cp_action->id);
                        $photo_user_list = $photo_user_service->getPhotoUsersByActionIds($cp_action->id);
                    } else {
                        $photo_user_list = $photo_user_service->getPhotoUsersByCpActionIdAndApprovalStatus($cp_action->id, PhotoUser::APPROVAL_STATUS_APPROVE);
                    }
                    if (!$photo_user_list) continue;

                    $cp_photo_action = $photo_user_service->getCpPhotoActionByCpActionId($cp_action->id);

                    $sub_folder_name = $common_zip_folder_name . '/' . mb_convert_encoding($cp_action->id . '_' . $cp_photo_action->title, "SJIS", "UTF-8");
                    $cur_temp_folder_name = $this->temp_folder_name . $sub_folder_name . '/';

                    $zip->addEmptyDir($sub_folder_name);
                    if (!file_exists($cur_temp_folder_name)) {
                        mkdir($cur_temp_folder_name, 0777, true);
                    }

                    $csv_file_name = $cp_action->id . '_title_comment.csv';
                    $csv_file_dir = $cur_temp_folder_name . $csv_file_name;
                    $csv_fp = fopen($csv_file_dir, 'w');

                    $header = $this->csv_file_header;

                    if ($cp_photo_action->title_required) {
                        $header[] = 'タイトル';
                    }

                    if ($cp_photo_action->comment_required) {
                        $header[] = 'コメント';
                    }

                    if ($cp_photo_action->fb_share_required) {
                        $header[] = 'Facebookシェア';
                    }

                    if ($cp_photo_action->tw_share_required) {
                        $header[] = 'Twitterシェア';
                    }

                    if (!$cp_photo_action->panel_hidden_flg || $cp->type == Cp::TYPE_MESSAGE) {
                        $header[] = '検閲';
                    }

                    $this->putCsv($csv_fp, $header);

                    foreach ($photo_user_list as $photo_user) {
                        $photo_name = $this->getPhotoNameByPhotoUser($photo_user);
                        $photo_dir = $cur_temp_folder_name . $photo_name;
                        $photo_dir_in_zip = $sub_folder_name . '/' . $photo_name;

                        $image_url = $photo_user->getMiddlePhoto();
                        if (!file_get_contents($image_url)) {
                            $image_url = $photo_user->photo_url;
                        }

                        file_put_contents($photo_dir, file_get_contents($image_url));
                        $zip->addFile($photo_dir, $photo_dir_in_zip);

                        // Add data to csv file
                        $val = array($cp_photo_action->title, $this->brands_users_relation->no, $photo_name);

                        if ($cp_photo_action->title_required) {
                            $val[] = $photo_user->photo_title;
                        }

                        if ($cp_photo_action->comment_required) {
                            $val[] = $photo_user->photo_comment;
                        }

                        if ($cp_photo_action->fb_share_required) {
                            if ($photo_user->getPhotoUserShare(array('social_media_type' => SocialAccount::SOCIAL_MEDIA_FACEBOOK))) {
                                $val[] = '○';
                            } else {
                                $val[] = '';
                            }
                        }

                        if ($cp_photo_action->tw_share_required) {
                            if ($photo_user->getPhotoUserShare(array('social_media_type' => SocialAccount::SOCIAL_MEDIA_TWITTER))) {
                                $val[] = '○';
                            } else {
                                $val[] = '';
                            }
                        }

                        if (!$cp_photo_action->panel_hidden_flg || $cp->type == Cp::TYPE_MESSAGE) {
                            $val[] = $photo_user->getApprovalStatus();
                        }

                        $this->putCsv($csv_fp, $val);
                        $empty_flg = false;
                    }

                    fclose($csv_fp);
                    $zip->addFile($csv_file_dir, $sub_folder_name . '/' . $csv_file_name);
                }
                $zip->close();

                if ($empty_flg) {
                    return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'photo-download-failed'));
                }

                // ストリームに出力
                $logger->error('download_photo_image_zip@doAction Start downloading');
                header('Content-Type: application/zip; name="' . $this->zip_file_name . '"');
                header('Content-Disposition: attachment; filename="' . $this->zip_file_name . '"');
                header('Content-Length: ' . filesize($zip_dir));

                if ($this->Data['cp_id'] == 4021) {
                    $this->readFileByChunkWithCleanBuffer($zip_dir, self::CHUNK_SIZE);
                } else {
                    $this->readFileByChunk($zip_dir, self::CHUNK_SIZE);
                }
            }
        } catch (Exception $e   ) {
            $logger->error('download_photo_image_zip@doAction Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'photo-download-failed'));
        }

        // 一時ファイルを削除しておく
        $this->rmTempDir($this->temp_folder_name);
        exit();
    }

    /**
     * @param $photo_user
     * @return string
     */
    private function getPhotoNameByPhotoUser($photo_user) {
        $cp_user = $this->cp_user_service->getCpUserById($photo_user->cp_user_id);
        $this->brands_users_relation = $this->brands_users_relation_service->getBrandsUsersRelation($this->getBrand()->id, $cp_user->user_id);

        $extension = pathinfo($photo_user->photo_url, PATHINFO_EXTENSION);

        return $this->brands_users_relation->no . '.' . $extension;
    }

    public function getZipFileName($cp_id){
        return self::ZIP_FILE_NAME . '_' . $cp_id . '.zip';
    }
}
