<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.services.QuestionTypeService');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpProfileQuestionnaireTrait');

class save_action_questionnaire extends SaveActionBase {

    use CpProfileQuestionnaireTrait;

    protected $ContainerName = 'save_action_questionnaire';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $file_info = array();
    /** @var $cp_questionnaire_actions CpQuestionnaireActions */
    protected $cp_questionnaire_actions = '';
    /** @var CpQuestionnaireService $cp_questionnaire_service */
    protected $cp_questionnaire_service;
    protected $data = array();
    protected $question_choice_array = array();
    protected $question_array = array();

    protected $ValidatorDefinition = array(
        'auth' => array()
    );

    protected $question_error;

    public function doThisFirst () {
        $this->cp_questionnaire_actions = aafwEntityStoreFactory::create('CpQuestionnaireActions');
        $this->cp_questionnaire_service = $this->createService('CpQuestionnaireService');

        // POSTしてきた設問、選択肢を配列に格納
        foreach($this->POST as $key => $value) {
            if(preg_match('/^choice_id_/', $key)) {
                // 選択肢のinputのname構成は、choice_id_設問ID_選択肢IDとなるので、choice_info[2]は設問ID、choice_info[3]は選択肢IDとなる
                $choice_info = explode('_', $key);
                $this->question_choice_array[$choice_info[2]][$choice_info[3]] = $value;
            }
            if(preg_match('/^question_id_/', $key)) {
                // 設問のinputのname構成は、question_id_設問IDとなるので、question_info[2]は設問IDとなる
                $question_info = explode('_', $key);
                $this->question_array[$question_info[2]] = $value;
            }
        }

        if ($this->getCpAction()->isOpeningCpAction()) {
            $this->setCheckedProfileQuestionnaireIds($this->POST);
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        $this->Data['brand'] = $this->getBrand();

        $validatorService = new CpValidator($this->Data['brand']->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        // 画像使用のラジオボタンチェック
        if(trim($this->POST['moduleImage']) === "") {
            if($this->POST['save_type'] == CpAction::STATUS_FIX) {
                // エラー情報をセッションに格納
                $this->Validator->setError('moduleImage', 'NOT_INPUT_TEXT');
            }
        } else {
            if ($this->FILES['image_file']) {
                $fileValidator = new FileValidator($this->FILES['image_file'], FileValidator::FILE_TYPE_IMAGE);
                if (!$fileValidator->isValidFile()) {
                    $this->Validator->setError('image_file', 'NOT_MATCH_TYPE');
                } else {
                    $this->file_info['image_file'] = $fileValidator->getFileInfo();
                    if($this->file_info['image_file']['size'] > FileValidator::IMAGE_FILE_MAX_FILE_SIZE) {
                        $this->Validator->setError('image_file', 'ERROR_FILE_SIZE_OVER_5MB');
                    }
                }
            }
            if($this->POST['moduleImage'] && $this->POST['image_url'] !== "") {
                if(!is_string($this->POST['image_url'])) {
                    $this->Validator->setError('image_url', 'INPUT_STRING');
                }
                if(mb_strlen($this->POST['image_url'], 'UTF-8') > 512) {
                    $this->Validator->setError('image_url', 'INPUT_WITHIN_512');
                }
                if(!preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $this->POST['image_url'])) {
                    $this->Validator->setError('image_url', 'INPUT_URL');
                }
            }
        }

        // テキストのチェック
        if(trim($this->POST['text']) === "") {
            if($this->POST['save_type'] == CpAction::STATUS_FIX) {
                $this->Validator->setError('text', 'NOT_INPUT_TEXT');
            }
        } else {
            if(!is_string($this->POST['text'])) {
                $this->Validator->setError('text', 'INPUT_STRING');
            }
            if(mb_strlen($this->POST['text'], 'UTF-8') > CpValidator::MAX_TEXT_LENGTH) {
                $this->Validator->setError('text', 'INPUT_WITHIN_20000');
            }
        }

        // タイトルのチェック
        if(trim($this->POST['title']) === "") {
            if($this->POST['save_type'] == CpAction::STATUS_FIX) {
                $this->Validator->setError('title', 'NOT_INPUT_TEXT');
            }
        } else {
            if(!is_string($this->POST['title'])) {
                $this->Validator->setError('title', 'INPUT_STRING');
            }
            if(mb_strlen($this->POST['title'], 'UTF-8') > 50) {
                $this->Validator->setError('title', 'INPUT_WITHIN_50');
            }
        }

        // ボタン設定のチェック
        if(trim($this->POST['button_label_text']) === "") {
            $this->Validator->setError('button_label_text', 'NOT_INPUT_TEXT');
        } else {
            if(!is_string($this->POST['button_label_text'])) {
                $this->Validator->setError('button_label_text', 'INPUT_STRING');
            }
            if(mb_strlen($this->POST['button_label_text'], 'UTF-8') > 80) {
                $this->Validator->setError('button_label_text', 'INPUT_WITHIN_80');
            }
        }

        if(!$this->question_array) {
            $this->Validator->setError('questionnaire', 'NOT_EXIST_QUESTIONNAIRE');
        } else {
            foreach($this->question_array as $question_id => $value) {
                // 選択式のアンケートなのに、選択肢がPOSTされていない
                if((array_key_exists('multi_answer_'.$question_id, $this->POST)) && (!array_key_exists($question_id, $this->question_choice_array))) {
                    $this->Validator->setError('question_id_'.$question_id, 'NOT_EXIST_CHOICE');
                }

                if(Util::trimEmSpace($value) === "") {
                    $this->Validator->setError('question_id_'.$question_id, 'NOT_INPUT_TEXT');
                } elseif(!is_string($value)) {
                    $this->Validator->setError('question_id_'.$question_id, 'INPUT_STRING');
                } elseif(mb_strlen($value, 'UTF-8') > 1024) {
                    $this->Validator->setError('question_id_'.$question_id, 'INPUT_WITHIN_1024');
                }

                // 画像タイプの場合
                if(QuestionTypeService::isChoiceQuestion($this->POST['type_'.$question_id])) {

                    //選択式プルダウンの初回保存
                    if ($this->POST['type_'.$question_id] == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE
                        && (!is_array($this->question_choice_array[$question_id]) || count($this->question_choice_array[$question_id]) <= 0)) {
                        if (trim($this->POST['pulldown_choice_'.$question_id]) === "") {
                            $this->Validator->setError('pulldown_choice_'.$question_id, 'NOT_INPUT_TEXT');
                        } else {
                            $choice_info = Util::cutStringByLineBreak($this->POST['pulldown_choice_'.$question_id]);
                            $prov_choice_id = 1; //仮置きのchoice_id
                            foreach($choice_info as $choice) {
                                if(Util::trimEmSpace($choice) !== '') {
                                    if (mb_strlen($choice, 'UTF-8') > 12) {
                                        $this->Validator->setError('pulldown_choice_' . $question_id, 'INPUT_WITHIN_12_PER_LINE');
                                        break;
                                    }
                                    $this->question_choice_array[$question_id]['a'.$prov_choice_id] = $choice;
                                    $prov_choice_id += 1;
                                }
                            }
                        }
                        continue;
                    }

                    foreach($this->question_choice_array[$question_id] as $choice_id => $value) {
                        if(Util::trimEmSpace($value) === "") {
                            $this->Validator->setError('choice_id_'.$question_id.'_'.$choice_id, 'NOT_INPUT_TEXT');
                        } elseif(!is_string($value)) {
                            $this->Validator->setError('choice_id_'.$question_id.'_'.$choice_id, 'INPUT_STRING');
                        } elseif(mb_strlen($value, 'UTF-8') > 512) {
                            $this->Validator->setError('choice_id_'.$question_id.'_'.$choice_id, 'INPUT_WITHIN_1024');
                        }

                        if ($this->POST['type_'.$question_id] == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                            if (mb_strlen($value, 'UTF-8') > 12) {
                                $this->Validator->setError('choice_id_'.$question_id.'_'.$choice_id, 'INPUT_WITHIN_12_PER_LINE');
                            }
                        } elseif($this->POST['type_'.$question_id] == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE) {
                            $fileValidator = new FileValidator($this->FILES['choice_image_file_'.$question_id.'_'.$choice_id], FileValidator::FILE_TYPE_IMAGE);
                            if (!$fileValidator->isValidFile()) {
                                if($fileValidator->getErrorCode() == FileValidator::ERROR_FILE_NOT_EXIST) {
                                    // 画像の保存有無を確認し、保存されていなかった場合にチェック
                                    $choice = $this->cp_questionnaire_service->getChoiceByQuestionIdAndChoiceId($question_id, $choice_id);
                                    if(!$choice || $choice->image_url === '') {
                                        $this->Validator->setError('choice_image_file_'.$question_id.'_'.$choice_id, 'ERROR_FILE_NOT_EXIST');
                                    }
                                } else {
                                    $this->Validator->setError('choice_image_file_'.$question_id.'_'.$choice_id, $fileValidator->getErrorMessageKey());
                                }
                            } else {
                                $imageValidator = new ImageValidator($this->FILES['choice_image_file_'.$question_id.'_'.$choice_id]['name']);
                                if($imageValidator->isLargerSize(100, 100)) {
                                    $this->file_info['choice_image_file_'.$question_id.'_'.$choice_id] = $fileValidator->getFileInfo();
                                } else {
                                    $this->Validator->setError('choice_image_file_'.$question_id.'_'.$choice_id, 'ERROR_FILE_WITHIN_100');
                                }
                            }
                        }
                    }
                } else {
                    //Free answer場合、NG設問チェック
                    /** @var QuestionNgWordService $questionNgWordService */
                    $questionNgWordService = $this->createService('QuestionNgWordService');
                    if($questionNgWordService->isNgQuestion($value,$this->brand->id)) {
                        $this->Validator->setError('question_id_'.$question_id, 'NG_QUESTION');
                    }
                }
            }
        }

        if ($this->getCpAction()->isOpeningCpAction()) {
            $is_valid_choice = $this->isValidChoice();
            if (!$is_valid_choice) {
                $this->Validator->setError('prefill_flg', 'INVALID_VALUE');
            } elseif ($this->canUpdateQuestionnaires()) {
                $service_factory = new aafwServiceFactory();
                /** @var CpEntryProfileQuestionnaireService $cp_profile_questionnaire_service */
                $cp_profile_questionnaire_service = $service_factory->create('CpEntryProfileQuestionnaireService');

                $cp_profile_questionnaire_service->clearQuestionnairesByCpActionId($this->getCpAction()->id);
                $checked_profile_questionnaire_ids = $this->getCheckedProfileQuestionnaireIds();

                foreach ($checked_profile_questionnaire_ids as $qst_id) {
                    $cp_profile_questionnaire_service->addQuestionnaire($this->getCpAction()->id, $qst_id);
                }

                if (count($checked_profile_questionnaire_ids) > 0) {
                    $this->getCpAction()->prefill_flg = $this->POST['prefill_flg'] == CpAction::PREFILL_FLG_FILL ? CpAction::PREFILL_FLG_FILL : CpAction::PREFILL_FLG_IGNORE;
                }
            }
        }

        $this->validateDeadLine();

        // 参加者一覧の「メッセージ作成」でエラーが発生した場合は、保存処理を行わない
        if($this->is_fan_list_page && $this->Validator->getErrorCount()) {
            $this->Validator->setError('is_fan_list_page', 'SAVE_ERROR');
            return false;
        } else {
            // アンケートでは、例えvalidateエラーが発生した場合でも、エラーが発生していない項目に関してはDBに保存処理を行う。
            $questionnaire_questions = $this->cp_questionnaire_service->getQuestionsByQuestionnaireActionId($this->getConcreteAction()->id);

            try {
                $this->cp_questionnaire_actions->begin();

                $this->deleteQuestionAndChoice($questionnaire_questions, $this->getConcreteAction()->id);

                $this->setQuestionAndChoice($this->getConcreteAction()->id);

                $this->cp_questionnaire_actions->commit();
            } catch(Exception $e) {
                $this->cp_questionnaire_actions->rollback();
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error('save_action_questionnaire error.' . $e);
                return 'redirect:' . $this->POST['callback'].'?mid=failed';
            }

            if ((($this->file_info['image_file'] && $this->Validator->getError('image_file')) || !$this->file_info['image_file'] ) && !$this->Validator->getError('image_url')) {
                $data['image_url'] = $this->POST['image_url'];
            }

            if(!$this->Validator->getError('text')) {
                $data['text'] = $this->POST['text'];
            }
            if(!$this->Validator->getError('title')) {
                $data['title'] = $this->POST['title'];
            }
            if(!$this->Validator->getError('button_label_text')) {
                $data['button_label_text'] = $this->POST['button_label_text'];
            }

            $this->renewDeadLineData();

            // エラーが含まれている場合は下書き保存扱いにする
            $this->getCpAction()->status = $this->Validator->getErrorCount() ? CpAction::STATUS_DRAFT : $this->POST['save_type'];
            $this->getActionManager()->updateCpActions($this->getCpAction(), $data);
        }

        return !$this->Validator->getErrorCount();
    }

    function doAction() {
        //メインバナー画像 保存
        $data = $this->saveBannerImage();
        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        $this->assign('saved',1);

        if($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }
        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }

    /**
     * 削除処理。DBにのみ存在するものについては削除する。
     * @param $questionnaire_questions
     * @param $questionnaire_action_id
     */
    private function deleteQuestionAndChoice($questionnaire_questions, $questionnaire_action_id) {
        foreach($questionnaire_questions as $question) {
            if(!array_key_exists($question->id, $this->question_array)) {
                // アンケートアクションと設問の相関データ(QuestionnairesQuestionsRelations)の削除
                $question_relation = $this->cp_questionnaire_service->getRelationByQuestionnaireActionIdAndQuestionId($questionnaire_action_id, $question->id);
                $this->cp_questionnaire_service->deleteQuestionnairesQuestionsRelation($question_relation);

                // 上記の他に設問が紐付けテーブルに存在しない場合、設問も削除する(存在する場合は、他のアクションから紐づけられているので削除しない)
                $question_relations = $this->cp_questionnaire_service->getRelationsByQuestionId($question->id);
                if(!$question_relations) {
                    if(QuestionTypeService::isChoiceQuestion($question->type_id)) {
                        $question_requirement = $this->cp_questionnaire_service->getRequirementByQuestionId($question->id);
                        $choices = $this->cp_questionnaire_service->getChoicesByQuestionId($question->id);
                        foreach ($choices as $choice) {
                            $this->cp_questionnaire_service->deleteChoice($choice);
                        }
                        // 外部キーの関係で、選択肢、要件、設問の順に削除
                        $this->cp_questionnaire_service->deleteQuestionRequirement($question_requirement);
                    }
                    $this->cp_questionnaire_service->deleteQuestion($question);
                }
            }
            if(QuestionTypeService::isChoiceQuestion($question->type_id)) {
                $question_choices = $this->cp_questionnaire_service->getChoicesByQuestionId($question->id);
                foreach($question_choices as $choice) {
                    if(!array_key_exists($choice->id, $this->question_choice_array[$question->id]) && $choice->other_choice_flg == CpQuestionnaireService::NOT_USE_OTHER_CHOICE) {
                        $this->cp_questionnaire_service->deleteChoice($choice);
                    }
                }
            }
        }
    }

    /**
     * アンケート関連のテーブルの登録処理
     * @param $questionnaire_action_id
     */
    private function setQuestionAndChoice($questionnaire_action_id) {
        $new_question_num = 1;
        $change_question_num = 0;

        foreach($this->question_array as $question_id => $value) {
            $type_id = $this->POST['type_' . $question_id];
            $requirement_flg = $this->POST['requirement_' . $question_id];

            if($this->Validator->getError('question_id_'.$question_id)) {
                $new_question = $this->cp_questionnaire_service->setQuestion($type_id, '', $question_id);
                // 新規で作成した設問はIDが変更されるので、新しいIDでエラーメッセージを挿入
                $this->Validator->setError('question_id_'.$new_question->id, $this->Validator->getError('question_id_'.$question_id));
            } else {
                $new_question = $this->cp_questionnaire_service->setQuestion($type_id, $value, $question_id);
            }

            //ActionFormの値を新しいidへ移動する
            $buff = $this->POST['question_id_'.$question_id];
            unset($this->POST['question_id_'.$question_id]);
            $this->POST['question_id_'.$new_question->id] = $buff;

            $this->cp_questionnaire_service->setQuestionnairesQuestionsRelation($questionnaire_action_id, $new_question->id, $requirement_flg, $new_question_num);

            if($type_id == QuestionTypeService::CHOICE_ANSWER_TYPE || $type_id == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE) {
                $use_other_choice_flg = $this->POST['use_other_choice_' . $question_id] ? $this->POST['use_other_choice_' . $question_id] : CpQuestionnaireService::NOT_USE_OTHER_CHOICE;
                $random_order_flg = $this->POST['random_order_' . $question_id];
                $multi_answer_flg = $this->POST['multi_answer_' . $question_id];
                $this->cp_questionnaire_service->setRequirement($new_question->id, $use_other_choice_flg, $random_order_flg, $multi_answer_flg);
            } else if ($type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                $random_order_flg = $this->POST['random_order_' . $question_id];
                $this->cp_questionnaire_service->setRequirement($new_question->id, CpQuestionnaireService::NOT_USE_OTHER_CHOICE, $random_order_flg, CpQuestionnaireService::SINGLE_ANSWER);
            }

            //プルダウンのTextAreaのエラーがある場合は選択肢を保存しない
            if ($type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE && $this->Validator->getError('pulldown_choice_'.$question_id)) {
                $this->Validator->setError('pulldown_choice_'.$new_question->id, $this->Validator->getError('pulldown_choice_'.$question_id));

                //ActionFormの値を新しいidへ移動する
                $buff = $this->POST['pulldown_choice_'.$question_id];
                unset($this->POST['pulldown_choice_'.$question_id]);
                $this->POST['pulldown_choice_'.$new_question->id] = $buff;

                continue;
            }

            foreach($this->question_choice_array[$question_id] as $choice_id => $choice) {

                if($change_question_num != $new_question_num) {
                    $new_choice_num = 1;
                }
                if($this->POST['type_'.$question_id] == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE) {
                    if($this->file_info['choice_image_file_'.$question_id.'_'.$choice_id] && !$this->Validator->getError('choice_image_file_'.$question_id.'_'.$choice_id)) {
                        $choice_image_url = StorageClient::getInstance()->putObject(
                            StorageClient::toHash('brand/'.$this->Data['brand']->id.'/cp_action_questionnaire/'.StorageClient::getUniqueId()), $this->file_info['choice_image_file_'.$question_id.'_'.$choice_id]
                        );
                    } else {
                        $choice_image_url = '';
                    }
                }
                if($this->Validator->getError('choice_id_'.$question_id.'_'.$choice_id)) {
                    $new_choice = $this->cp_questionnaire_service->setChoices($new_question->id, '', $choice_id, $new_choice_num, $choice_image_url);
                    // 新規で作成した選択肢はIDが変更されるので、新しいIDでエラーメッセージを挿入
                    $this->Validator->setError('choice_id_'.$new_question->id.'_'.$new_choice->id, $this->Validator->getError('choice_id_'.$question_id.'_'.$choice_id));
                } else {
                    $new_choice = $this->cp_questionnaire_service->setChoices($new_question->id, $choice, $choice_id, $new_choice_num, $choice_image_url);
                }

                if($this->Validator->getError('choice_image_file_'.$question_id.'_'.$choice_id)) {
                    // 新規で作成した画像選択肢はIDが変更されるので、新しいIDでエラーメッセージを挿入
                    $this->Validator->setError('choice_image_file_'.$new_question->id.'_'.$new_choice->id, $this->Validator->getError('choice_image_file_'.$question_id.'_'.$choice_id));
                }

                //ActionFormの値を新しいidへ移動する
                $buff = $this->POST['choice_id_'.$question_id.'_'.$choice_id];
                unset($this->POST['choice_id_'.$question_id.'_'.$choice_id]);
                $this->POST['choice_id_'.$new_question->id.'_'.$new_choice->id] = $buff;

                $new_choice_num += 1;
                $change_question_num = $new_question_num;
            }
            $new_question_num += 1;

            if($type_id == QuestionTypeService::CHOICE_ANSWER_TYPE) {
                if($use_other_choice_flg) {
                    $this->cp_questionnaire_service->setOtherChoice($new_question->id);
                } else {
                    $this->cp_questionnaire_service->deleteOtherChoice($question_id);
                }
            }
        }
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpQuestionnaireActionManager');
    }

    private function saveBannerImage() {
        $data = array();

        if ($this->file_info['image_file'] && !$this->Validator->getError('image_file')) {
            // メインバナー画像 保存
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info['image_file'], "cp_action_questionnaire");
        }

        return $data;
    }
}
