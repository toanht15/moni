<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class SynInstantWinService extends aafwServiceBase {
    
    const INSTANT_WIN_RESET_TIME = "00:00:00";
    const DAY_SEC = 86400;

    public function saveChallengeLog($userId,$synCpId){
        if( !$userId || !$synCpId) return;

        $synCpSecondChallengeLog = $this->findDrawableSecondChallengeLog($userId,$synCpId);
        if($synCpSecondChallengeLog){
            $this->drawSecondChallenge($synCpSecondChallengeLog);
        }else{
            $this->drawFirstChallenge($userId,$synCpId);
        }
    }

    public function drawFirstChallenge($userId,$synCpId){
        //SynCpChallengeLogをsaveする前に判定して連続チャレンジログ記録しないと絶対2倍チャレンジとみなされるので先に取得しておく
        $challengeMode = $this->isDoubleUpChallenge($userId,$synCpId) ? SynCpSecondChallengeLog::DOUBLE_UP_CHALLENGE : SynCpSecondChallengeLog::NORMAL_CHALLENGE;

        $synCpChallengeLogStore = $this->getModel("SynCpChallengeLogs");
        $synCpChallengeLog = $synCpChallengeLogStore->createEmptyObject();
        $synCpChallengeLog->user_id = $userId;
        $synCpChallengeLog->syn_cp_id = $synCpId;
        $synCpChallengeLog->challenged_at = date("Y-m-d H:i:s", time());
        $synCpChallengeLogStore->save($synCpChallengeLog);

        $synCpSecondChallengeLogStore = $this->getModel("SynCpSecondChallengeLogs");
        $synCpSecondChallengeLog = $synCpSecondChallengeLogStore->createEmptyObject();
        $synCpSecondChallengeLog->syn_cp_challenge_log_id = $synCpChallengeLog->id;
        $synCpSecondChallengeLog->challenge_mode = $challengeMode;
        $synCpSecondChallengeLogStore->save($synCpSecondChallengeLog);
    }

    /**
     * 本日分のSyn.キャンペーンのスピードくじのチャレンジ履歴を取得
     * @param $userId
     * @param $synCpId
     * @return SynCpChallengeLog
     */
    public function findTodayChallengeLog($userId,$synCpId){
        if(!$userId || !$synCpId) return null;

        $synCpChallengeLogStore = $this->getModel("SynCpChallengeLogs");
        list($beginChallengedAt,$endChallengedAt) = $this->getTodayBetweenChallengedAt();
        $filter = array(
            'conditions' => array(
                'user_id'=>$userId,
                'syn_cp_id'=>$synCpId,
                'challenged_at:>='=>$beginChallengedAt,
                'challenged_at:<'=>$endChallengedAt
            ),
            'order' => array(
                'name' => 'id',
                'direction' => 'DESC'
            )
        );
        return $synCpChallengeLogStore->findOne($filter);
    }

    /**
     * 本日分のSyn.のチャレンジログを取得するための日付の範囲を返す
     * @param string $resetTime
     * @return array
     */
    public function getTodayBetweenChallengedAt(){
        if($this->isOverResetTime()){
            $beginChallengedAt = $this->getResetDate();
            $endChallengedAt = date("Y-m-d H:i:s", strtotime($beginChallengedAt." 1 day"));
            return array($beginChallengedAt,$endChallengedAt);
        }else{
            $endChallengedAt = $this->getResetDate();
            $beginChallengedAt = date("Y-m-d H:i:s", strtotime($endChallengedAt." -1 day"));
            return array($beginChallengedAt,$endChallengedAt);
        }
    }
    
    public function isOverResetTime(){
        return date("Y-m-d H:i:s") > $this->getResetDate();
    }

    /**       
     * ほぼテストコードのために用意してる。
     * @param $format
     * @param $strtotime
     * @return string
     */
    public function getResetDate($resetTime = self::INSTANT_WIN_RESET_TIME){
        return date('Y-m-d '.$resetTime);
    }
    /**
     * 本日分の参加履歴に紐づく連続チャレンジログで
     * menuのクリック形跡があり、まだ連続チャレンジしてないものは
     * もう一度挑戦できる
     * @param $userId
     * @param $synCpId
     * @return SynCpSecondChallengeLog
     */
    public function findDrawableSecondChallengeLog($userId,$synCpId){
        if(!$userId || !$synCpId) return null;

        $synCpChallengeLog = $this->findTodayChallengeLog($userId,$synCpId);
        if(!$synCpChallengeLog) return null;

        $synCpSecondChallengeLogStore = $this->getModel("SynCpSecondChallengeLogs");
        //menuのクリック形跡があり、まだ連続チャレンジしてないもの抽出
        $synCpSecondChallengeLog = $synCpSecondChallengeLogStore->findOne(
            array(
                'syn_cp_challenge_log_id'=>$synCpChallengeLog->id,
                'menu_clicked_at:!='=> '0000:00:00 00:00:00',
                'challenged_at' => '0000:00:00 00:00:00'
            )
        );

        return $synCpSecondChallengeLog;
    }

    public function findEmptySecondChallengeLog($userId,$synCpId){
        if(!$userId || !$synCpId) return null;

        $synCpChallengeLog = $this->findTodayChallengeLog($userId,$synCpId);
        if(!$synCpChallengeLog) return null;

        $synCpSecondChallengeLogStore = $this->getModel("SynCpSecondChallengeLogs");
        $synCpSecondChallengeLog = $synCpSecondChallengeLogStore->findOne(
            array(
                'syn_cp_challenge_log_id'=>$synCpChallengeLog->id,
                'menu_clicked_at'=> '0000:00:00 00:00:00',
                'challenged_at' => '0000:00:00 00:00:00'
            )
        );

        return $synCpSecondChallengeLog;
    }
    /**
     * 今日のログに紐づく空の連続チャレンジログあれば
     * クリック時間を記録する
     * @param $userId
     * @param $synCpId
     */
    public function saveClickMenu($userId,$synCpId){
        $synCpChallengeLog = $this->findTodayChallengeLog($userId,$synCpId);
        if(!$synCpChallengeLog) return;

        $synCpSecondChallengeLogStore = $this->getModel("SynCpSecondChallengeLogs");
        $synCpSecondChallengeLog = $synCpSecondChallengeLogStore->findOne(
            array(
                'syn_cp_challenge_log_id'=>$synCpChallengeLog->id,
                'menu_clicked_at'=> '0000-00-00 00:00:00',
                'challenged_at' => '0000-00-00 00:00:00'
            )
        );
        if(!$synCpSecondChallengeLog) return;
        $synCpSecondChallengeLog->menu_clicked_at = date("Y-m-d H:i:s", time());
        $synCpSecondChallengeLogStore->save($synCpSecondChallengeLog);
    }

    /**
     * 連続チャレンジを引いたあとの処理
     * @param $synCpSecondChallengeLog
     */
    public function drawSecondChallenge($synCpSecondChallengeLog){
        $synCpSecondChallengeLogStore = $this->getModel("SynCpSecondChallengeLogs");
        $synCpSecondChallengeLog->challenged_at = date("Y-m-d H:i:s", time());
        $synCpSecondChallengeLogStore->save($synCpSecondChallengeLog);
    }

    public function findLastChallengeLog($userId){

        $synCpChallengeLogStore = $this->getModel("SynCpChallengeLogs");
        $filter = array(
            'conditions' => array(
                'user_id' => $userId
            ),
            'order' => array(
                'name' => 'id',
                'direction' => 'DESC'
            )
        );
        return $synCpChallengeLogStore->findOne($filter);
    }

    /**
     * 当選確率２倍の条件を判定する
     * 連続チャレンジ中は連続チャレンジログのchallenge_modeに従う
     * 「前回のチャレンジの次のスピードくじ開始日時」と「今回のチャレンジの時間」
     * の差分が１日以内なら連続で挑戦しているとする
     * @param $userId
     * @return bool
     */
    public function isDoubleUpChallenge($userId,$synCpId){
        $continuityChallengeLog = $this->findDrawableSecondChallengeLog($userId,$synCpId);
        if($continuityChallengeLog){
            return $continuityChallengeLog->challenge_mode == SynCpSecondChallengeLog::DOUBLE_UP_CHALLENGE;
        }
        $lastChallengeLog = $this->findLastChallengeLog($userId);
        if(!$lastChallengeLog){
            return false;
        }
        $nextChallengeStartAt = strtotime(date("Y-m-d ".self::INSTANT_WIN_RESET_TIME, strtotime($lastChallengeLog->challenged_at."+1 day")));
        $betweenChallengeTimeLastAndCurrent = $this->getCurrentTimestamp() - $nextChallengeStartAt;
        return $betweenChallengeTimeLastAndCurrent < self::DAY_SEC;
    }

    public function getCurrentTimestamp(){
       return strtotime(date("Y-m-d H:i:s", time()));
    }
}