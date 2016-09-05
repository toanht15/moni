<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class InstantWinPrizeService extends aafwServiceBase {

    /** @var InstantWinPrizes $instant_win_prizes */
    protected $instant_win_prizes;

    public function __construct() {
        $this->instant_win_prizes = $this->getModel('InstantWinPrizes');
    }

    /**
     * デフォルトで２つ作成
     * @param $cp_instant_win_action_id
     */
    public function createInitInstantWinPrizes($cp_instant_win_action_id) {
        $instant_win_prize_stay = $this->instant_win_prizes->createEmptyObject();
        $instant_win_prize_stay->cp_instant_win_action_id = $cp_instant_win_action_id;
        $instant_win_prize_stay->winning_rate             = 0;
        $instant_win_prize_stay->image_url                = config('Static.Url').'/img/module/instantWin/animeLucky_lose1.gif';
        $instant_win_prize_stay->image_type               = InstantWinPrizes::IMAGE_DEFAULT;
        $instant_win_prize_stay->text                     = '';
        $instant_win_prize_stay->prize_status             = InstantWinPrizes::PRIZE_STATUS_STAY;
        $this->instant_win_prizes->save($instant_win_prize_stay);

        $instant_win_prize_pass = $this->instant_win_prizes->createEmptyObject();
        $instant_win_prize_pass->cp_instant_win_action_id = $cp_instant_win_action_id;
        $instant_win_prize_pass->max_winner_count         = 1;
        $instant_win_prize_pass->winning_rate             = 0.001;
        $instant_win_prize_pass->image_url                = config('Static.Url').'/img/module/instantWin/animeLucky_win1.gif';
        $instant_win_prize_pass->image_type               = InstantWinPrizes::IMAGE_DEFAULT;
        $instant_win_prize_pass->text                     = '';
        $instant_win_prize_pass->prize_status             = InstantWinPrizes::PRIZE_STATUS_PASS;
        $this->instant_win_prizes->save($instant_win_prize_pass);
    }

    /**
     * @param $cp_instant_win_action_id
     * @param $data
     */
    public function updateInstantWinPrizes($cp_instant_win_action_id, $data) {
        $params = $data['instant_win_prizes'];
        $instant_win_prizes = $this->getInstantWinPrizesByCpInstantWinActionId($cp_instant_win_action_id);
        foreach ($params as $param) {
            foreach($instant_win_prizes as $instant_win_prize) {
                if($instant_win_prize->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS) {
                    $instant_win_prize->winning_rate = $param['winning_rate'];
                    $instant_win_prize->max_winner_count = $param['max_winner_count'];
                }
                $instant_win_prize->image_url  = $param['image_url'];
                $instant_win_prize->image_type = $param['image_type'];
                $instant_win_prize->text       = $param['text'];
                $this->instant_win_prizes->save($instant_win_prize);
            }
        }
    }

    /**
     * @param $instant_win_prize
     */
    public function resetInstantWinTime($instant_win_prize) {
        $instant_win_prize->win_time = '0000-00-00 00:00:00';
        $this->instant_win_prizes->save($instant_win_prize);
    }

    /**
     * @param $instant_win_prize
     */
    public function updateInstantWinPrize($instant_win_prize) {
        $this->instant_win_prizes->save($instant_win_prize);
    }

    /**
     * @param $new_cp_instant_win_action_id
     * @param $old_cp_instant_win_action_id
     */
    public function copyInstantWinPrizes($new_cp_instant_win_action_id, $old_cp_instant_win_action_id) {
        $old_instant_win_prizes = $this->getInstantWinPrizesByCpInstantWinActionId($old_cp_instant_win_action_id);
        foreach ($old_instant_win_prizes as $old_instant_win_prize) {
            $new_instant_win_prize = $this->instant_win_prizes->createEmptyObject();
            $new_instant_win_prize->cp_instant_win_action_id = $new_cp_instant_win_action_id;
            $new_instant_win_prize->max_winner_count = $old_instant_win_prize->max_winner_count;
            $new_instant_win_prize->winner_count     = 0;
            if($old_instant_win_prize->prize_status == InstantWinPrizes::PRIZE_STATUS_PASS) {
                $new_instant_win_prize->winning_rate = $old_instant_win_prize->winning_rate;
            }
            $new_instant_win_prize->image_url        = $old_instant_win_prize->image_url;
            $new_instant_win_prize->image_type       = $old_instant_win_prize->image_type;
            $new_instant_win_prize->text             = $old_instant_win_prize->text;
            $new_instant_win_prize->prize_status     = $old_instant_win_prize->prize_status;
            $this->instant_win_prizes->save($new_instant_win_prize);
        }
    }

    /**
     * @param $filter
     * @return entity
     */
    public function getInstantWinPrize($filter) {
        return $this->instant_win_prizes->findOne($filter);
    }

    /**
     * @param $id
     * @return entity
     */
    public function getInstantWinPrizeById($id) {
        return $this->instant_win_prizes->findOne($id);
    }

    /**
     * @param $cp_instant_win_action_id
     * @param $prize_status
     * @return mixed
     */
    public function getInstantWinPrizesByPrizeStatus($cp_instant_win_action_id, $prize_status) {
        $filter = array(
            'cp_instant_win_action_id' => $cp_instant_win_action_id,
            'prize_status' => $prize_status
        );
        return $this->instant_win_prizes->find($filter);
    }

    /**
     * @param $cp_instant_win_action_id
     * @param $prize_status
     * @return mixed
     */
    public function getInstantWinPrizeByPrizeStatus($cp_instant_win_action_id, $prize_status) {
        $filter = array(
            'cp_instant_win_action_id' => $cp_instant_win_action_id,
            'prize_status' => $prize_status
        );
        return $this->instant_win_prizes->findOne($filter);
    }


    /**
     * @param $cp_instant_win_action_id
     * @return aafwEntityContainer|array
     */
    public function getInstantWinPrizesByCpInstantWinActionId($cp_instant_win_action_id) {
        return $this->instant_win_prizes->find(array('cp_instant_win_action_id' => $cp_instant_win_action_id));
    }

}
