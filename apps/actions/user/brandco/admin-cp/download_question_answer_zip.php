<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.DownloadDataActionBase');

class download_question_answer_zip extends DownloadDataActionBase {

    const ZIP_FILE_NAME = "QUESTION_ANSWER";
    const CHUNK_SIZE = 1048576; // チャンクサイズ (bytes)

    private $cp_questionnaire_service;

    protected $csv_file_header = array(
        'モジュールタイトル',
        '会員No'
    );

    public function validate() {

        if(!$this->isLoginManager()){
            return false;
        }

        $validatorService = new CpValidator($this->getBrand()->id);
        return $validatorService->isOwnerOfAction($this->Data['cp_action_id']);
    }

    function doAction() {

        /** @var QuestionnaireUserAnswerService $questionnaire_user_answer_service */
        $questionnaire_user_answer_service = $this->createService('QuestionnaireUserAnswerService');

        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $this->cp_questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_CP_QUESTION);

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $logger = aafwLog4phpLogger::getDefaultLogger();

        try {
            // Zipにアーカイブ
            $zip = new ZipArchive();
            $zip_dir = $this->temp_folder_name . $this->zip_file_name;

            if ($zip->open($zip_dir, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE)) {
                $empty_flg = true;
                $cp_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($this->Data['cp_id'], CpAction::TYPE_QUESTIONNAIRE);
                $common_zip_folder_name = self::ZIP_FILE_NAME . '_' . $this->Data['cp_id'];

                foreach ($cp_actions as $cp_action) {

                    $user_answers = $questionnaire_user_answer_service->getUserAnswersByCpActionId($cp_action->id);

                    if(!$user_answers) continue;

                    // アンケートの設問を並び順通りに取得
                    $questionnaire_action = $this->cp_questionnaire_service->getCpQuestionnaireAction($cp_action->id);

                    $sub_folder_name = $common_zip_folder_name . '/' . mb_convert_encoding($cp_action->id . '_' . $questionnaire_action->title, "SJIS", "UTF-8");
                    $cur_temp_folder_name = $this->temp_folder_name . $sub_folder_name . '/';

                    $zip->addEmptyDir($sub_folder_name);
                    if (!file_exists($cur_temp_folder_name)) {
                        mkdir($cur_temp_folder_name, 0777, true);
                    }

                    $csv_file_name = $cp_action->id . '_question_answer.csv';
                    $csv_file_dir = $cur_temp_folder_name . $csv_file_name;
                    $csv_fp = fopen($csv_file_dir, 'w');

                    $header = $this->csv_file_header;

                    $relations = $this->cp_questionnaire_service->getRelationsByQuestionnaireActionId($questionnaire_action->id);

                    foreach ($relations as $relation) {
                        $question = $this->cp_questionnaire_service->getQuestionById($relation->question_id);
                        array_push($header, 'Q' . $relation->number . '.' . $question->question);
                    }

                    $header[] = 'ステータス';
                    $header[] = '回答日時';

                    $this->putCsv($csv_fp, $header);

                    foreach($user_answers as $user_answer){

                        $this->brands_users_relation = $this->brands_users_relation_service->getBrandsUsersRelationById($user_answer->brands_users_relation_id);

                        $val = array($questionnaire_action->title, $this->brands_users_relation->no);

                        foreach ($relations as $relation) {
                            $question = $this->cp_questionnaire_service->getQuestionById($relation->question_id);

                            if (QuestionTypeService::isChoiceQuestion($question->type_id)) {
                                $answer = $this->getChoiceQuestionAnswer($question,$user_answer->brands_users_relation_id, $relation);

                            } else {
                                $answer = $this->cp_questionnaire_service->getFreeAnswer($user_answer->brands_users_relation_id, $relation->id)->answer_text;
                            }

                            $val[] = $answer !== '' ? $answer : '';
                        }

                        $val[] = $user_answer->getApprovalStatus($user_answer->approval_status);
                        $val[] = $user_answer->finished_answer_at;

                        $this->putCsv($csv_fp, $val);
                        $empty_flg = false;
                    }

                    fclose($csv_fp);
                    $zip->addFile($csv_file_dir, $sub_folder_name . '/' . $csv_file_name);
                }
                $zip->close();

                if ($empty_flg) {
                    return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'question-answer-download-failed'));
                }

                // ストリームに出力
                $logger->error('download_question_answer_zip@doAction Start downloading');
                header('Content-Type: application/zip; name="' . $this->zip_file_name . '"');
                header('Content-Disposition: attachment; filename="' . $this->zip_file_name . '"');
                header('Content-Length: ' . filesize($zip_dir));

                $this->readFileByChunk($zip_dir,self::CHUNK_SIZE);
            }
        } catch (Exception $e) {
            $logger->error('download_question_answer_zip@doAction Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'], $this->Data['cp_action_id']), array('mid' => 'download-error'));
        }

        // 一時ファイルを削除しておく
        $this->rmTempDir($this->temp_folder_name);
        exit();
    }

    public function getZipFileName($cp_id){
        return self::ZIP_FILE_NAME . '_' . $cp_id . '.zip';
    }

    private function getChoiceQuestionAnswer($question, $brands_users_relation_id,$question_relation){

        if($question->type_id == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE){
            $answer = $this->cp_questionnaire_service->getChoiceImageAnswer($brands_users_relation_id, $question_relation->id);
        }else{
            $answer = $this->cp_questionnaire_service->getChoiceAnswer($brands_users_relation_id, $question_relation->id);
        }

        return $answer;
    }
}
