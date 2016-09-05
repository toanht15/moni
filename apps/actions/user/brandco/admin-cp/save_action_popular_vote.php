<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');

class save_action_popular_vote extends SaveActionBase {
    protected $ContainerName = 'save_action_popular_vote';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    private $logger;

    /** @var CpPopularVoteActionService cp_popular_vote_action_service */
    protected $cp_popular_vote_action_service;
    /** @var CpFlowService cp_flow_service */

    protected $ValidatorDefinition = array(
        'auth' => array()
    );

    protected $file_info = array();
    protected $image_files;

    public function doThisFirst() {
        $this->cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');

        if ($this->POST['file_type'] == CpPopularVoteAction::FILE_TYPE_IMAGE && $this->FILES['candidate_image']) {
            for ($i = 0; $i < count($this->POST['candidate_id']); $i++) {
                $image_file = array();
                $image_file['name'] = ($this->FILES['candidate_image']['name'][$i]) ? : '';
                $image_file['type'] = ($this->FILES['candidate_image']['type'][$i]) ? : '';
                $this->image_files[] = $image_file;
            }
        }

        $this->fetchDeadLineValidator();

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function validate() {
        $this->brand = $this->getBrand();
        $validatorService = new CpValidator($this->brand->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        if (trim($this->POST['title']) === '' && $this->POST['save_type'] != CpAction::STATUS_DRAFT) {
            $this->Validator->setError('title', 'NOT_INPUT_TEXT');
        } else {
            if (!is_string($this->POST['title'])) {
                $this->Validator->setError('title', 'INPUT_STRING');
            } else if (mb_strlen($this->POST['title'], 'utf-8') > 50) {
                $this->Validator->setError('title', 'INPUT_WITHIN_50');
            }
        }

        if (!$this->validateDeadLine()) return false;

        if (trim($this->POST['moduleImage']) === '' && $this->POST['save_type'] != CpAction::STATUS_DRAFT) {
            $this->Validator->setError('moduleImage', 'NOT_CHOOSE');
        } else {
            if (!is_string($this->POST['moduleImage']) || !in_array($this->POST['moduleImage'], ['0', '1', '2'])) {
                $this->Validator->setError('moduleImage', 'NOT_CHOOSE');
            }
        }

        if (trim($this->POST['file_type']) === '' && $this->POST['save_type'] != CpAction::STATUS_DRAFT) {
            $this->Validator->setError('file_type', 'NOT_CHOOSE');
        } else {
            if (!is_string($this->POST['file_type']) || !in_array($this->POST['file_type'], ['1', '2'])) {
                $this->Validator->setError('file_type', 'NOT_CHOOSE');
            }
        }

        if (trim($this->POST['text']) === '' && $this->POST['save_type'] != CpAction::STATUS_DRAFT) {
            $this->Validator->setError('text', 'NOT_INPUT_TEXT');
        } else {
            if (!is_string($this->POST['text'])) {
                $this->Validator->setError('text', 'INPUT_STRING');
            } else if (mb_strlen($this->POST['text'], 'utf-8') > CpValidator::MAX_TEXT_LENGTH) {
                $this->Validator->setError('text', 'INPUT_WITHIN_20000');
            }
        }

        if ($this->FILES['image_file']) {
            $fileValidator = new FileValidator($this->FILES['image_file'], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('image_file', 'NOT_MATCHES');
            } else {
                $this->file_info['image_file'] = $fileValidator->getFileInfo();
            }
        }

        if (trim($this->POST['share_url_type']) === '') {
            $this->Validator->setError('share_url_type', 'NOT_CHOOSE');
        } else {
            if (!is_string($this->POST['share_url_type']) || !in_array($this->POST['share_url_type'], ['1', '2'])) {
                $this->Validator->setError('share_url_type', 'NOT_CHOOSE');
            }
        }

        if (count($this->POST['candidate_id']) < 2 && $this->POST['save_type'] != CpAction::STATUS_DRAFT) {
            $this->Validator->setError('n_candidates', 'NOT_EXIST_CANDIDATE');
        }

        foreach ($this->POST['candidate_id'] as $key => $value) {
            if (trim($this->POST['candidate_title'][$key]) === '') {
                $this->Validator->setError('candidate_title_' . $key, 'NOT_INPUT_TEXT');
            } else {
                if (!is_string($this->POST['candidate_title'][$key])) {
                    $this->Validator->setError('candidate_title_' . $key, 'INPUT_STRING');
                } else if (mb_strlen($this->POST['candidate_title'][$key], 'utf-8') > 33) {
                    $this->Validator->setError('candidate_title_' . $key, 'INPUT_WITHIN_33');
                }
            }

            if (mb_strlen($this->POST['candidate_description'][$key], 'utf-8') > 2000) {
                $this->Validator->setError('candidate_description_' . $key, 'INPUT_WITHIN_2000');
            }

            if ($this->POST['file_type'] == CpPopularVoteAction::FILE_TYPE_IMAGE) {
                $fileValidator = new FileValidator($this->image_files[$key], FileValidator::FILE_TYPE_IMAGE);
                if (!$fileValidator->isValidFile()) {
                    if (!$this->POST['candidate_original_url'][$key]) {
                        if ($fileValidator->getErrorCode() == FileValidator::ERROR_FILE_NOT_EXIST && $this->POST['candidate_id'][$key] != 0) {
                            $cp_popular_vote_candidate = $this->cp_popular_vote_action_service->getCpPopularVoteCandidateById($this->POST['candidate_id'][$key]);
                            if (!$cp_popular_vote_candidate || $cp_popular_vote_candidate->thumbnail_url === '') {
                                $this->Validator->setError('candidate_image_' . $key, 'ERROR_FILE_NOT_EXIST');
                            }
                        } else {
                            $this->Validator->setError('candidate_image_' . $key, $fileValidator->getErrorMessageKey());
                        }
                    }
                } else {
                    $this->file_info['candidate_image'][$key] = $fileValidator->getFileInfo();
                }
            } else if($this->POST['file_type'] == CpPopularVoteAction::FILE_TYPE_MOVIE) {
                if (trim($this->POST['candidate_movie'][$key]) === '') {
                    $this->Validator->setError('candidate_movie_' . $key, 'NOT_INPUT_TEXT');
                } else {
                    if (!is_string($this->POST['candidate_movie'][$key])) {
                        $this->Validator->setError('candidate_movie_' . $key, 'INPUT_STRING');
                    } else if (mb_strlen($this->POST['candidate_movie'][$key], 'utf-8') > 15) {
                        $this->Validator->setError('candidate_movie_' . $key, 'INPUT_WITHIN_15');
                    }
                }
            } else {
                $this->Validator->setError('file_type', 'NOT_CHOOSE');
            }
        }

        // 投票候補はValidation後保存する必要がある
        if ($this->is_fan_list_page && $this->is_cp_action_fixed && $this->Validator->getErrorCount()) {
            return false;
        } else {
            foreach ($this->POST['candidate_del'] as $value) {
                $this->cp_popular_vote_action_service->deleteCpPopularVoteCandidateById($value);
            }

            if ($this->POST['file_type'] != $this->getConcreteAction()->file_type) {
                $this->cp_popular_vote_action_service->deleteCpPopularVoteCandidateByCpPopularVoteActionId($this->getConcreteAction()->id);
                $this->cp_popular_vote_action_service->updateFileTypeByCpPopularVoteAction($this->POST['file_type'], $this->getConcreteAction());
            }
            foreach ($this->POST['candidate_id'] as $key => $value) {
                if ($this->POST['candidate_id'][$key] == 0) {
                    $cp_popular_vote_candidate = $this->cp_popular_vote_action_service->createEmptyCpPopularVoteCandidate();
                } else {
                    $cp_popular_vote_candidate = $this->cp_popular_vote_action_service->getCpPopularVoteCandidateById($this->POST['candidate_id'][$key]);
                }

                if ($this->POST['file_type'] == CpPopularVoteAction::FILE_TYPE_IMAGE &&
                        !$this->Validator->getError('candidate_title_' . $key) &&
                        !$this->Validator->getError('candidate_image_' . $key)) {

                    $this->uploadImage($key);

                    // 画像の場合
                    $cp_popular_vote_candidate->title = $this->POST['candidate_title'][$key];
                    $cp_popular_vote_candidate->description = $this->POST['candidate_description'][$key];
                    $cp_popular_vote_candidate->thumbnail_url = $this->POST['thumbnail_url'][$key];
                    $cp_popular_vote_candidate->original_url = $this->POST['original_url'][$key];
                } else if ($this->POST['file_type'] == CpPopularVoteAction::FILE_TYPE_MOVIE &&
                    !$this->Validator->getError('candidate_title_' . $key) &&
                    !$this->Validator->getError('candidate_movie_' . $key)) {

                    // 動画の場合
                    $cp_popular_vote_candidate->title = $this->POST['candidate_title'][$key];
                    $cp_popular_vote_candidate->description = $this->POST['candidate_description'][$key];

                    $cp_popular_vote_candidate->thumbnail_url = $this->uploadYoutubeHQImage($this->POST['candidate_movie'][$key]);
                    $cp_popular_vote_candidate->original_url = YoutubeStream::EMBED_URL_PREFIX . $this->POST['candidate_movie'][$key];
                    $cp_popular_vote_candidate->order_no = $key + 1;
                    $cp_popular_vote_candidate->del_flg = 0;
                }

                $cp_popular_vote_candidate->cp_popular_vote_action_id = $this->getConcreteAction()->id;
                $cp_popular_vote_candidate->order_no = $key + 1;
                $cp_popular_vote_candidate->del_flg = 0;

                $this->cp_popular_vote_action_service->updateCpPopularVoteCandidate($cp_popular_vote_candidate);
            }
        }

        return !$this->Validator->getErrorCount();
    }

    function doAction() {
        $this->getCpAction()->status = $this->POST['save_type'];
        $this->renewDeadLineData();
        $this->saveCpPopularVoteAction($this->POST);

        $this->Data['saved'] = 1;
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }

    protected function saveCpPopularVoteAction($params) {
        $data = array();
        $data['title'] = $params['title'];
        $data['title_required'] = $params['title_required'] ? $params['title_required'] : 0;
        $data['button_label_text'] = $params['button_label_text'];
        $data['file_type'] = $params['file_type'];
        $data['text'] = $params['text'];
        $data['html_content'] = Michelf\Markdown::defaultTransform($data['text']);
        $data['share_placeholder'] = $params['share_placeholder'];
        $data['share_url_type'] = $params['share_url_type'];
        $data['fb_share_required'] = ($params['fb_share_required']) ? : 0;
        $data['tw_share_required'] = ($params['tw_share_required']) ? : 0;
        $data['random_flg'] = $params['random_flg'];
        $data['show_ranking_flg'] = ($params['show_ranking_flg']) ? 1 : 0;

        if ($this->FILES['image_file']) {
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info['image_file'], "cp_action_popular_vote");
        } else {
            $data['image_url'] = $params['image_url'];
        }

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);
    }

    protected function uploadImage($key) {
        if (isset($this->file_info['candidate_image'][$key])) {
            $object_key = StorageClient::toHash('brand/' . $this->Data['brand']->id . '/cp_action_popular_vote/' . $this->getCp()->id . '/module' . StorageClient::getUniqueId());
            $storage_client = StorageClient::getInstance();

            $this->POST['original_url'][$key] = $storage_client->putObject($object_key, $this->file_info['candidate_image'][$key]);

            $clone_image_m = ImageCompositor::cloneImage($this->FILES['candidate_image']['name'][$key], 'm');
            $file_validator_m = new FileValidator($clone_image_m, FileValidator::FILE_TYPE_IMAGE);
            $file_validator_m->isValidFile();
            list ($img_width, $img_height) = ImageCompositor::getSize($clone_image_m['name']);
            if ($img_width !== 1000 || $img_height !== 524) {
                ImageCompositor::scaleImageAspectRetained($clone_image_m['name'], 1000, 524, 238, 238, 238);
            }
            $this->POST['thumbnail_url'][$key] = $storage_client->putObject($object_key . ImageCompositor::SUFFIX_REGULAR, $file_validator_m->getFileInfo());
        }
    }

    protected function uploadYoutubeHQImage($youtube_id) {
        $suffix_array = YoutubeStream::$img_names;

        foreach ($suffix_array as $suffix) {
            $url = YoutubeStream::IMG_URL_PREFIX . $youtube_id . '/' . $suffix;
            if (($data = @file_get_contents($url))) {
                $object_key = StorageClient::toHash('brand/' . $this->Data['brand']->id . '/cp_action_popular_vote/' . $this->getCp()->id . '/module' . StorageClient::getUniqueId());
                $storage_client = StorageClient::getInstance();

                $file_info = array(
                    'path'      => '/tmp/' . uniqid(),
                    'extension' => 'jpg'
                );
                file_put_contents($file_info['path'], $data);
                ImageCompositor::scaleImageAspectRetained($file_info['path'], 1000, 524, 238, 238, 238);

                $thumbnail_url =  $storage_client->putObject($object_key . ImageCompositor::SUFFIX_REGULAR, $file_info);
                unlink($file_info);

                return $thumbnail_url;
            }
        }

        return '';
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpPopularVoteActionManager');
    }
}
