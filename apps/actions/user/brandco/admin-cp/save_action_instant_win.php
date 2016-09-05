<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.services.instant_win.InstantWinPrizeService');

use Michelf\Markdown;

class save_action_instant_win extends SaveActionBase {
    protected $ContainerName = 'save_action_instant_win';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $brand = null;
    private $file_info_0 = array();
    private $file_info_1 = array();
    /** @var InstantWinPrizeService instant_win_prize_service */
    private $instant_win_prize_service = null;

    protected $ValidatorDefinition = array(
        'title' => array(
            'type' => 'str',
            'length' => 50
        ),
        'image_type_0' => array(
            'type' => 'num'
        ),
        'image_type_1' => array(
            'type' => 'num'
        ),
        'image_url' => array(
            'type' => 'str',
            'length' => 512,
            'validator' => array('URL')
        ),
        'image_file_0' => array(
            'type' => 'file',
            'size' => '5MB'
        ),
        'image_file_1' => array(
            'type' => 'file',
            'size' => '5MB'
        ),
        'text' => array(
            'type' => 'str',
            'length' => CpValidator::MAX_TEXT_LENGTH,
        ),
        'win_text' => array(
            'type' => 'str',
            'length' => CpValidator::MAX_TEXT_LENGTH,
        ),
        'lose_text' => array(
            'type' => 'str',
            'length' => CpValidator::MAX_TEXT_LENGTH,
        ),
        'winning_rate_1' => array(
            'type' => 'num',
            'range' => array(
                '>=' => 0.001,
                '<=' => 99.999,
            )
        ),
        'time_value' => array(
            'type' => 'num',
            'range' => array(
                '>=' => 1,
                '<=' => 100,
            )
        ),
        'time_measurement' => array(
            'type' => 'num',
        ),
        'once_flg' => array(
            'type' => 'num',
        )
    );

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['title']['required'] = true;
            if ($this->POST['logic_type'] == CpInstantWinActions::LOGIC_TYPE_RATE) {
                $this->ValidatorDefinition['winning_rate_1']['required'] = true;
            }
            if ($this->POST['once_flg'] == InstantWinPrizes::ONCE_FLG_OFF) {
                $this->ValidatorDefinition['time_value']['required'] = true;
                $this->ValidatorDefinition['time_measurement']['required'] = true;
            }
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        $this->brand = $this->getBrand();
        $validatorService = new CpValidator($this->brand->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        if ($this->FILES['image_file_0']) {
            $fileValidator = new FileValidator($this->FILES['image_file_0'],FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('image_file_0', 'NOT_MATCHES');
                return false;
            }else{
                $this->file_info_0 = $fileValidator->getFileInfo();
            }
        }
        if ($this->FILES['image_file_1']) {
            $fileValidator = new FileValidator($this->FILES['image_file_1'],FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('image_file_1', 'NOT_MATCHES');
                return false;
            } else {
                $this->file_info_1 = $fileValidator->getFileInfo();
            }
        }

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {
        $this->instant_win_prize_service = $this->createService('InstantWinPrizeService');

        $this->updateInstantWinPrizeLose();
        $this->updateInstantWinPrizeWin();

        $this->getCpAction()->status = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $this->POST);

        $this->Data['saved'] = 1;
        if ($this->save_type == CpAction::STATUS_FIX) {
            $this->callback = $this->callback.'?mid=action-saved';
        } elseif ($this->POST['']) {
            $this->callback = $this->callback.'?mid=action-draft';
        }

        $return = 'redirect: ' . $this->callback;

        return $return;
    }

    private function updateInstantWinPrizeLose() {
        $filter = array(
            'cp_instant_win_action_id' => $this->getConcreteAction()->id,
            'prize_status' => InstantWinPrizes::PRIZE_STATUS_STAY
        );
        $instant_win_prize_lose = $this->instant_win_prize_service->getInstantWinPrize($filter);
        if($this->FILES['image_file_0'] && $this->image_type_0 == InstantWinPrizes::IMAGE_UPLOAD){
            $instant_win_prize_lose->image_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/'. $this->Data['brand']->id . '/instant_win_stay/' . StorageClient::getUniqueId()), $this->file_info_0
            );
            $instant_win_prize_lose->image_type = $this->image_type_0;
        } elseif($this->image_type_0 == InstantWinPrizes::IMAGE_DEFAULT){
            $instant_win_prize_lose->image_url = config('Static.Url').'/img/module/instantWin/animeLucky_lose1.gif';
            $instant_win_prize_lose->image_type = $this->image_type_0;
        }
        $instant_win_prize_lose->text = $this->text_0;
        $instant_win_prize_lose->html_content = Markdown::defaultTransform($this->text_0);
        $this->instant_win_prize_service->updateInstantWinPrize($instant_win_prize_lose);
    }

    private function updateInstantWinPrizeWin() {
        $filter = array(
            'cp_instant_win_action_id' => $this->getConcreteAction()->id,
            'prize_status' => InstantWinPrizes::PRIZE_STATUS_PASS
        );
        $instant_win_prize_win = $this->instant_win_prize_service->getInstantWinPrize($filter);
        if($this->FILES['image_file_1'] && $this->image_type_0 == InstantWinPrizes::IMAGE_UPLOAD){
            $instant_win_prize_win->image_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/'. $this->Data['brand']->id . '/instant_win_pass/' . StorageClient::getUniqueId()), $this->file_info_1
            );
            $instant_win_prize_win->image_type = $this->image_type_1;
        } elseif($this->image_type_1 == InstantWinPrizes::IMAGE_DEFAULT){
            $instant_win_prize_win->image_url = config('Static.Url').'/img/module/instantWin/animeLucky_win1.gif';
            $instant_win_prize_win->image_type = $this->image_type_1;
        }
        $instant_win_prize_win->text = $this->text_1;
        $instant_win_prize_win->html_content = Markdown::defaultTransform($this->text_1);
        $instant_win_prize_win->winning_rate = $this->winning_rate_1;

        $instant_win_prize_win->max_winner_count = $this->getCp()->winner_count;
        $this->instant_win_prize_service->updateInstantWinPrize($instant_win_prize_win);
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpInstantWinActionManager');
    }
}
