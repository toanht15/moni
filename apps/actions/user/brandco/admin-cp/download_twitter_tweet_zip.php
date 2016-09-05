<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.DownloadDataActionBase');

class download_twitter_tweet_zip extends DownloadDataActionBase {

    public $NeedManagerLogin = true;

    const ZIP_FILE_NAME = "TWITTER_TWEET";
    const CHUNK_SIZE = 1048576;

    private $db;

    public function validate() {

        if(!$this->isLoginManager()){
            return false;
        }

        $validatorService = new CpValidator($this->getBrand()->id);
        return $validatorService->isOwnerOfAction($this->Data['cp_action_id']);
    }

    function doAction() {

        $this->db = aafwDataBuilder::newBuilder();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        /** @var CpTweetActionService $cp_tweet_action_service */
        $cp_tweet_action_service = $this->createService('CpTweetActionService');

        try {
            // Zipにアーカイブ
            $zip = new ZipArchive();
            $zip_dir = $this->temp_folder_name . $this->zip_file_name;

            if ($zip->open($zip_dir, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE)) {
                $empty_flg = true;
                $cp_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($this->Data['cp_id'], CpAction::TYPE_TWEET);
                $common_zip_folder_name = self::ZIP_FILE_NAME . '_' . $this->Data['cp_id'];

                foreach ($cp_actions as $cp_action) {

                    $tweet_action = $cp_tweet_action_service->getCpTweetAction($cp_action->id);

                    $tweet_messages = $this->getTweetPostByTweetActionId($tweet_action->id);

                    if(!$tweet_messages) continue;

                    $sub_folder_name = $common_zip_folder_name . '/' . mb_convert_encoding($cp_action->id . '_' . $tweet_action->title, "SJIS", "UTF-8");
                    $cur_temp_folder_name = $this->temp_folder_name . $sub_folder_name . '/';

                    $zip->addEmptyDir($sub_folder_name);
                    if (!file_exists($cur_temp_folder_name)) {
                        mkdir($cur_temp_folder_name, 0777, true);
                    }

                    $csv_file_name = $cp_action->id . '.csv';
                    $csv_file_dir = $cur_temp_folder_name . $csv_file_name;
                    $csv_fp = fopen($csv_file_dir, 'w');

                    //CsvファイルHeaderを作成する
                    $header = $this->csv_file_header;
                    $header[] = "ツイートID";
                    $header[] = "ツイートURL";
                    $header[] = "ツイート内容";
                    $header[] = "公開状況";
                    $header[] = "ツイート画像";
                    $header[] = "ユーザツイートID";
                    $header[] = "スクリーンネーム";
                    $header[] = "プロファイURL";
                    $header[] = "検閲状態";
                    $header[] = "作成日時";

                    $this->putCsv($csv_fp, $header);

                    $user_tweet_messages = $this->getUserTweetMessages($tweet_messages);

                    foreach ($user_tweet_messages as $user_id => $tweet_message) {

                        $tweet_image_files = null;

                        $this->brands_users_relation = $this->brands_users_relation_service->getBrandsUsersRelation($this->getBrand()->id, $user_id);

                        $tweet_image_files = $this->saveTweetImage($tweet_message['image_url'],$cur_temp_folder_name, $sub_folder_name, $zip);

                        //データをCSVファイルに入れる
                        $val = array($tweet_action->title, $this->brands_users_relation->no, $tweet_image_files);
                        $val[] = $tweet_message['tweet_id'];
                        $val[] = $tweet_message['tweet_content_url'];
                        $val[] = $tweet_message['tweet_text'] . ($tweet_action->tweet_fixed_text ? "\r\n" . $tweet_action->tweet_fixed_text : "");
                        $val[] = $tweet_message['tweet_status'];
                        $val[] = implode(',', $tweet_message['image_url']);
                        $val[] = $tweet_message['user_twitter_id'];
                        $val[] = $tweet_message['screen_name'];
                        $val[] = $tweet_message['profile_page_url'];
                        $val[] = $tweet_message['approval_status'];
                        $val[] = $tweet_message['created_at'];
                        $this->putCsv($csv_fp, $val);

                        $empty_flg = false;
                    }

                    fclose($csv_fp);
                    $zip->addFile($csv_file_dir, $sub_folder_name . '/' . $csv_file_name);
                }

                $zip->close();

                if ($empty_flg) {
                    return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'tweet-download-failed'));
                }

                // ストリームに出力
                header('Content-Type: application/zip; name="' . $this->zip_file_name . '"');
                header('Content-Disposition: attachment; filename="' . $this->zip_file_name . '"');
                header('Content-Length: ' . filesize($zip_dir));
                $this->readFileByChunk($zip_dir,self::CHUNK_SIZE);
            }
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('download_twitter_tweet_zip@doAction Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'photo-download-error'));
        }

        // 一時ファイルを削除しておく
        $this->rmTempDir($this->temp_folder_name);
        exit();
    }

    private function getTweetPostByTweetActionId($tweet_action_id){

        $params = array(
            'cp_tweet_action_id' => $tweet_action_id
        );

        $tweets = $this->db->getTwitterTweetInfoByCpTweetActionId($params);

        return $tweets;
    }

    private function getUserTweetMessages($tweet_messages) {
        $user_tweet_array = array();

        $retweet_msg_service = $this->createService('CpRetweetMessageService');

        foreach ($tweet_messages as $tweet_message) {

            $user_id = $tweet_message['user_id'];

            if (!$user_tweet_array[$user_id]) {
                $user_tweet_array[$user_id]['screen_name'] = $tweet_message['screen_name'];
                $user_tweet_array[$user_id]['profile_page_url'] = $tweet_message['profile_page_url'];
                $user_tweet_array[$user_id]['user_twitter_id'] = $tweet_message['user_twitter_id'];
                $user_tweet_array[$user_id]['tweet_status'] = TweetMessage::getStaticTweetStatus($tweet_message['tweet_status']);
                $user_tweet_array[$user_id]['tweet_content_url'] = $tweet_message['tweet_content_url'];
                $user_tweet_array[$user_id]['tweet_id'] = $retweet_msg_service->getTweetIdByTweetUrl($tweet_message['tweet_content_url']);
                $user_tweet_array[$user_id]['tweet_text'] = $tweet_message['tweet_text'];
                $user_tweet_array[$user_id]['approval_status'] = TweetMessage::getStaticApprovalStatus($tweet_message['approval_status']);
                $user_tweet_array[$user_id]['created_at'] = $tweet_message['created_at'];
            }

            if(!Util::isNullOrEmpty($tweet_message['image_url'])){
                $user_tweet_array[$user_id]['image_url'][] = $tweet_message['image_url'];
            }
        }

        return $user_tweet_array;
    }

    private function saveTweetImage($tweet_images, $cur_temp_folder_name, $sub_folder_name, $zip) {

        $img_file_names = array();
        $i = 1;

        foreach ($tweet_images as $tweet_image) {
            $img_file_name = $this->getTweetImageFileNameByUserId($tweet_image, $i);
            $photo_dir = $cur_temp_folder_name . $img_file_name;
            $photo_dir_in_zip = $sub_folder_name . '/' . $img_file_name;

            file_put_contents($photo_dir, file_get_contents($tweet_image));
            $zip->addFile($photo_dir, $photo_dir_in_zip);
            $img_file_names[] = $img_file_name;
            $i++;
        }

        $tweet_image_files = implode(',', $img_file_names);

        return $tweet_image_files;
    }

    private function getTweetImageFileNameByUserId($image_url, $increment = 1) {

        // GETパラメータの削除
        $path_without_request_params = strtok($image_url, '?');
        $extension = pathinfo($path_without_request_params, PATHINFO_EXTENSION);

        return $this->brands_users_relation->no . '(' . $increment . ').' . $extension;
    }

    public function getZipFileName($cp_id){
        return self::ZIP_FILE_NAME . '_' . $cp_id . '.zip';
    }
}