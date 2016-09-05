<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserInstantWinActionValidator');
AAFW::import('jp.aainc.classes.brandco.cp.CpInstantWinActionManager');
AAFW::import('jp.aainc.classes.services.instant_win.InstantWinUserService');
AAFW::import('jp.aainc.classes.services.instant_win.SynInstantWinService');
AAFW::import('jp.aainc.classes.services.CpFlowService');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpTrait');
AAFW::import('jp.aainc.classes.services.monipla.UpdateMoniplaCpInfo');
AAFW::import('jp.aainc.classes.services.monipla.SendCpInfoForMonipla');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class api_pre_execute_draw_instant_win_action extends ExecuteActionBase {

    use CpTrait;

    public $NeedOption = array();
    protected $ContainerName = 'api_pre_execute_draw_instant_win_action';

    protected $AllowContent = array('JSON');

    protected $cp_instant_win_action;

    /** @var CpInstantWinActionManager $cp_instant_win_action_manager*/
    protected $cp_instant_win_action_manager;

    /** @var InstantWinPrizeService $instant_win_prize_service */
    protected $instant_win_prize_service;

    /** @var InstantWinUserService $instant_win_user_service */
    protected $instant_win_user_service;

    /** @var SynInstantWinService $syn_instant_win_service */
    protected $syn_instant_win_service;

    /** @var Cp $cp */
    protected $cp;


    public function beforeValidate () {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();

        $this->cp_instant_win_action_manager = new CpInstantWinActionManager();
        $this->cp_instant_win_action = $this->cp_instant_win_action_manager->getCpConcreteActionByCpActionId($this->cp_action_id);

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $cp_action = CpInfoContainer::getInstance()->getCpActionById($this->cp_action_id);
        $this->cp = $cp_flow_service->getCpByCpAction($cp_action);

        $this->instant_win_user_service = $this->getService('InstantWinUserService');
        $this->instant_win_prize_service = $this->getService('InstantWinPrizeService');
        $this->syn_instant_win_service = $this->getService('SynInstantWinService');
    }

    public function validate() {

        $validator = new UserInstantWinActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        if ($this->cp->isOverLimitWinner()) {
            $json_data = $this->createAjaxResponse("ng", array(), $this->canDrawAgain());
            $this->assign('json_data', $json_data);
            return false;
        }

        if (count($this->canDrawAgain())) {
            $json_data = $this->createAjaxResponse("ng", array(), $this->canDrawAgain());
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    /**
     * 抽選可能な状態か確認
     * @return array
     */
    public function canDrawAgain() {
        $errors = array();
        $drawableSecondChallengeLog = $this->syn_instant_win_service->findDrawableSecondChallengeLog($this->user_id,$this->cp->getSynCp()->id);
        if (!$drawableSecondChallengeLog && $this->remainWaitingTime()) {
            $errors['cp_action_id'][] = "次回までお待ちください";
        }
        if ($this->isWinner()) {
            $errors['cp_action_id'][] = "すでに当選しています";
        }
        return $errors;
    }

    /**
     * 前回参加した日付の確認
     * @return bool
     */
    public function remainWaitingTime() {
        $instant_win_user = $this->instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($this->cp_action_id, $this->cp_user_id);
        if ($this->cp_instant_win_action->once_flg == InstantWinPrizes::ONCE_FLG_OFF) {
            $waiting_time = $this->cp_instant_win_action_manager->changeValueIntoTime($this->cp_instant_win_action);
            return strtotime($waiting_time, strtotime($this->convertLastJoinAt($instant_win_user->last_join_at))) > time();
        }
        return false;
    }

    /**
     * 当選済みか確認
     * @return bool
     */
    public function isWinner() {
        $instant_win_user = $this->instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($this->cp_action_id, $this->cp_user_id);
        return $instant_win_user->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS;
    }

    function doAction() {

        /** @var CpTransactions $cp_transaction */
        $cp_transaction = aafwEntityStoreFactory::create('CpTransactions');

        /** @var CpTransactionService $transaction_service */
        $transaction_service = $this->createService('CpTransactionService');

        // 確率方式で抽選or時間方式で抽選
        if ($this->cp_instant_win_action->logic_type == CpInstantWinActionManager::LOGIC_TYPE_RATE) {

            //ユーザーデータの取得
            $draw_result = array();
            $draw_result['device_type'] = $this->getDeviceType();
            $draw_result['join_count'] = $this->getJoinCount();

            //抽選結果(仮)の取得
            $instant_win_prizes = $this->instant_win_prize_service->getInstantWinPrizesByCpInstantWinActionId($this->cp_instant_win_action->id);

            foreach ($instant_win_prizes as $instant_win_prize) {
                if ($instant_win_prize->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS) {
                    $challengeCount = 1;
                    //Synブランド専用企業のスピードくじの場合は連続チャレンジで当選確率2倍になる
                    $isDoubleUpChallenge = $this->cp->isForSyndotOnly() && $this->syn_instant_win_service->isDoubleUpChallenge($this->user_id,$this->cp->getSynCp()->id);
                    if($isDoubleUpChallenge){
                        $challengeCount = 2;
                    }
                    $draw_result['prize_status'] = $this->draw($instant_win_prize,$challengeCount);
                } else {
                    $draw_result['instant_win_prize_id'] = $instant_win_prize->id;
                    $draw_result['lose_instant_win_prize_id'] = $instant_win_prize->id;
                }

                if ($draw_result['prize_status'] == InstantWinPrizes::PRIZE_STATUS_PASS) {
                    $draw_result['instant_win_prize_id'] = $instant_win_prize->id;
                    break;
                }
            }

            try {
                $cp_transaction->begin();

                //このタイミングで当選のみロックしてから当選枠の確認をして抽選結果（確）を取得
                if ($draw_result['prize_status'] == InstantWinPrizes::PRIZE_STATUS_PASS) {

                    $transaction_service->getCpTransactionByIdForUpdate($this->cp_action_id);

                    if (!$this->hasWinnerCapacity($draw_result['instant_win_prize_id'])) {
                        $draw_result['prize_status'] = InstantWinPrizes::PRIZE_STATUS_STAY;
                        $draw_result['instant_win_prize_id'] = $draw_result['lose_instant_win_prize_id'];
                    }
                }

                //抽選結果（確）を保存
                $this->updateUserData($draw_result);

                //賞の情報の更新
                if ($draw_result['instant_win_prize_id'] != $draw_result['lose_instant_win_prize_id']) {
                    $instant_win_prize = $this->instant_win_prize_service->getInstantWinPrizeById($draw_result['instant_win_prize_id']);
                    // 当選人数は毎回カウントし直す
                    $instant_win_prize->winner_count = $this->instant_win_user_service->countWinnerByCpActionId($this->cp_action_id);
                    $this->instant_win_prize_service->updateInstantWinPrize($instant_win_prize);
                }

                $cp_transaction->commit();

            } catch (Exception $e) {
                $cp_transaction->rollback();
                $this->logger->error('DrawRateMethod Error.' . $e);
            }


        } else {

            $instant_win_prize = $this->instant_win_prize_service->getInstantWinPrizeByPrizeStatus($this->cp_instant_win_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);

            //ユーザーデータの取得
            $draw_result = array();
            $draw_result['device_type'] = $this->getDeviceType();
            $draw_result['join_count'] = $this->getJoinCount();

            $start_time = strtotime($this->cp->start_date);
            $end_time = strtotime($this->cp->end_date);

            //このスピードくじCPの初参加者が来た際に当選予定時刻を先に設定する
            if ($instant_win_prize->win_time == '0000-00-00 00:00:00') {

                try {
                    $cp_transaction->begin();
                    $transaction_service->getCpTransactionByIdForUpdate($this->cp_action_id);
                    // 当選状況のデータを取りなおす
                    $instant_win_prize = $this->instant_win_prize_service->getInstantWinPrizeByPrizeStatus($this->cp_instant_win_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);

                    //次の当選者は開催期間を当選者数で割った秒数以下のランダム秒後
                    $left_seconds = round(($end_time - $start_time) / $instant_win_prize->max_winner_count);
                    $plus_time = mt_rand(1, $left_seconds);
                    $win_time = date('Y-m-d H:i:s', strtotime("+$plus_time seconds", $start_time));
                    $instant_win_prize->win_time = $win_time;
                    $this->instant_win_prize_service->updateInstantWinPrize($instant_win_prize);

                    $cp_transaction->commit();

                } catch (Exception $e) {
                    $cp_transaction->rollback();
                    $this->logger->error('FirstUpdateWinTime Error.' . $e);
                }
            }

            //ふるい落とし（ロックなし）
            $draw_result['prize_status'] = $this->getDrawResult($instant_win_prize);
            $draw_result['instant_win_prize_id'] = $this->instant_win_prize_service->getInstantWinPrizeByPrizeStatus($this->cp_instant_win_action->id, $draw_result['prize_status'])->id;

            if ($draw_result['prize_status'] == InstantWinPrizes::PRIZE_STATUS_PASS) {


                try {
                    $cp_transaction->begin();
                    $transaction_service->getCpTransactionByIdForUpdate($this->cp_action_id);
                    // 当選状況のデータを取りなおす
                    $instant_win_prize = $this->instant_win_prize_service->getInstantWinPrizeByPrizeStatus($this->cp_instant_win_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);

                    //ふるい落とし（ロックあり）
                    $draw_result['prize_status'] = $this->getDrawResult($instant_win_prize);
                    $draw_result['instant_win_prize_id'] = $this->instant_win_prize_service->getInstantWinPrizeByPrizeStatus($this->cp_instant_win_action->id, $draw_result['prize_status'])->id;

                    if ($draw_result['prize_status'] == InstantWinPrizes::PRIZE_STATUS_PASS) {

                        //当選した人たち
                        $this->updateUserData($draw_result);
                        //賞の更新
                        $buffer = $this->getTimeBuffer($start_time, $end_time);

                        //開催期間終了間際では処理を変える
                        if (strtotime("-$buffer seconds", $end_time) > time()) {

                            $count = $instant_win_prize->max_winner_count - $instant_win_prize->winner_count;

                            //一桁の当選人数なら完全ランダム、二桁人数以上なら等間隔
                            if ($instant_win_prize->max_winner_count < 10) {

                                //次の当選者は当選者数個のランダム秒数の最小値秒数後
                                $left_seconds = round(strtotime("-$buffer seconds", $end_time) - time());
                                $rand = array();
                                for ($i = 0; $i < $count; $i++) {
                                    $rand[] = mt_rand(1, $left_seconds);
                                }
                                $plus_time = min($rand);

                            } else {

                                //次の当選者は残り時間を残り当選者数で割った秒数後
                                $plus_time = round((strtotime("-$buffer seconds", $end_time) - time()) / $count);
                            }

                            $win_time = date('Y-m-d H:i:s', strtotime("+$plus_time seconds", time()));
                            $instant_win_prize->win_time = $win_time;
                            // 当選人数は毎回カウントし直す
                            $instant_win_prize->winner_count = $this->instant_win_user_service->countWinnerByCpActionId($this->cp_action_id);
                            $this->instant_win_prize_service->updateInstantWinPrize($instant_win_prize);

                        } else {

                            //残り当選枠を吐き出し切る
                            $instant_win_prize->win_time = date('Y-m-d H:i:s');
                            // 当選人数は毎回カウントし直す
                            $instant_win_prize->winner_count = $this->instant_win_user_service->countWinnerByCpActionId($this->cp_action_id);
                            $this->instant_win_prize_service->updateInstantWinPrize($instant_win_prize);
                        }

                        $cp_transaction->commit();

                    } else {

                        //僅かな差で外れた人
                        $cp_transaction->rollback();

                        try {
                            $cp_transaction->begin();
                            $this->updateUserData($draw_result);
                            $cp_transaction->commit();

                        } catch (Exception $e) {
                            $cp_transaction->rollback();
                            $this->logger->error('UpdateSlightDifferenceLoser Error.' . $e);
                        }
                    }

                } catch (Exception $e) {
                    $cp_transaction->rollback();
                    $this->logger->error('DrawTimeMethod Error.' . $e);
                }

            } else {

                //ふつーにハズレた人
                try {
                    $cp_transaction->begin();
                    $this->updateUserData($draw_result);
                    $cp_transaction->commit();

                } catch (Exception $e) {
                    $cp_transaction->rollback();
                    $this->logger->error('UpdateLoser Error.' . $e);
                }
            }

        }

        //ビューに返す抽選結果を取得
        $view_data = $this->getViewData($draw_result['instant_win_prize_id']);

        //参加１回きりのCPなら参加完了のステータスをモニプラに送る
        if ($this->isCanSendCpInfoForMonipla() && $this->cp_instant_win_action->once_flg == InstantWinPrizes::ONCE_FLG_ON) {
            /** @var SendCpInfoForMonipla $send_cp_info_for_monipla */
            $send_cp_info_for_monipla = $this->createService('SendCpInfoForMonipla');
            $send_cp_info_for_monipla->sendCpUserStatus($this->cp_user_id, $this->cp_action_id, $this->brand->app_id, CpAction::TYPE_INSTANT_WIN);
        }

        //ユーザーの参加状態をモニプラに送る
        if ($this->isCanSendCpInfoForMonipla()) {
            /** @var UpdateMoniplaCpInfo $update_monipla_cp_info */
            $update_monipla_cp_info = $this->createService('UpdateMoniplaCpInfo');
            $is_elected = $draw_result['prize_status'] == InstantWinUsers::PRIZE_STATUS_WIN ? 1 : 0;
            $update_monipla_cp_info->sendCpUserStatus($this->cp_user_id, $this->cp_action_id, $this->brand->app_id, CpAction::TYPE_INSTANT_WIN, $is_elected);
        }

        $json_data = $this->createAjaxResponse("ok", $view_data);
        $this->assign("json_data", $json_data);
    }

    function saveData() {

    }

    private function getDeviceType() {
        return Util::isSmartPhone() ? InstantWinUserLogs::FROM_SMART_PHONE : InstantWinUserLogs::FROM_PC;
    }

    private function getJoinCount() {
        $instant_win_user = $this->instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($this->cp_action_id, $this->cp_user_id);
        return $instant_win_user->join_count;
    }

    private function hasWinnerCapacity($instant_win_prize_id) {
        $instant_win_prize = $this->instant_win_prize_service->getInstantWinPrizeById($instant_win_prize_id);
        return $instant_win_prize->winner_count < $instant_win_prize->max_winner_count;
    }

    private function draw($instant_win_prize, $drawCount = 1){
        $weight = $instant_win_prize->winning_rate * 1000;
        for($i = 0 ;$i < $drawCount; $i++){
            $rand = mt_rand(1, 100000);
            if($weight >= $rand){
                return InstantWinPrizes::PRIZE_STATUS_PASS;
            }
        }
        return InstantWinPrizes::PRIZE_STATUS_STAY;
    }

    /**
     * 共通のユーザデータの更新
     * @param $draw_result
     */
    private function updateUserData($draw_result) {
        if (!$draw_result['join_count']) {
            $this->instant_win_user_service->createInstantWinUser($this->cp_action_id, $this->cp_user_id, $draw_result);
        } else {
            $this->instant_win_user_service->updateInstantWinUser($this->cp_action_id, $this->cp_user_id, $draw_result);
        }
        $this->instant_win_user_service->createInstantWinUserlog($this->cp_action_id, $this->cp_user_id, $draw_result);
        $this->syn_instant_win_service->saveChallengeLog($this->user_id,$this->cp->getSynCp()->id);
    }


    /**
     * 時間方式の抽選処理
     * @param $instant_win_prize
     * @return int
     */
    private function getDrawResult($instant_win_prize) {
        if (strtotime($instant_win_prize->win_time) > time() || $instant_win_prize->max_winner_count - $instant_win_prize->winner_count <= 0) {
            return InstantWinPrizes::PRIZE_STATUS_STAY;
        }

        return InstantWinPrizes::PRIZE_STATUS_PASS;
    }

    public function getTimeBuffer($start_time, $end_time) {
        $open_time = $end_time - $start_time;
        return $open_time < 3600 * 24 ? $open_time / 10 : 3600 * 3;
    }

    private function getViewData($instant_win_prize_id) {
        $instant_win_prize = $this->instant_win_prize_service->getInstantWinPrizeById($instant_win_prize_id);
        $view_data = array();
        $view_data['prize_status'] = $instant_win_prize->prize_status;
        $view_data['has_draw_chance'] = $this->hasDrawChance();
        $view_data['last_join_at'] = $this->getNextTime();
        //キャンペーン編集でアニgifを設定できないのでSyn.のとき落選画像は固定で入れます。
        if($this->cp->isForSyndotOnly() && $instant_win_prize->prize_status == InstantWinPrizes::PRIZE_STATUS_STAY){
            $view_data['image_url'] = config('Static.Url').'/img/module/instantWin/animeLucky_lose2.gif';
            $view_data['can_show_second_chance'] = $this->syn_instant_win_service->findEmptySecondChallengeLog($this->user_id,$this->cp->getSynCp()->id) ? true : false;
        }else{
            $view_data['image_url'] = $instant_win_prize->image_url;
        }
        $view_data['text'] = $instant_win_prize->text;
        $parser = new PHPParser();
        $view_data['text'] = $instant_win_prize->html_content ? $instant_win_prize->html_content : $parser->toHalfContentDeeply($view_data['text']);
        return $view_data;
    }

    public function getNextTime() {
        $instant_win_user = $this->instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($this->cp_action_id, $this->cp_user_id);
        if ($this->cp_instant_win_action->once_flg == InstantWinPrizes::ONCE_FLG_OFF) {
            $waiting_time = $this->cp_instant_win_action_manager->changeValueIntoTime($this->cp_instant_win_action);
            return date('Y/m/d H:i:s', strtotime($waiting_time, strtotime($this->convertLastJoinAt($instant_win_user->last_join_at))));
        }
    }

    public function hasDrawChance() {
        $instant_win_user = $this->instant_win_user_service->getInstantWinUserByCpActionIdAndCpUserId($this->cp_action_id, $this->cp_user_id);
        if ($this->cp_instant_win_action->once_flg == InstantWinPrizes::ONCE_FLG_OFF) {
            $waiting_time = $this->cp_instant_win_action_manager->changeValueIntoTime($this->cp_instant_win_action);
            return strtotime($waiting_time, strtotime($this->convertLastJoinAt($instant_win_user->last_join_at))) < strtotime($this->cp->end_date);
        } else {
            return false;
        }
    }

    /**
     * 前回参加した日付を計算用にコンバート
     * @param $last_join_at
     * @return bool|string
     */
    public function convertLastJoinAt($last_join_at) {
        if ($this->cp_instant_win_action->time_measurement == CpInstantWinActions::TIME_MEASUREMENT_DAY) {
            $last_join_at = date('Y/m/d 00:00:00', strtotime($last_join_at));
        }

        return $last_join_at;
    }
}
