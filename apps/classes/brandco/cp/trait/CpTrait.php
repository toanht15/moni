<?php

AAFW::import('jp.aainc.classes.entities.Cp');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.SocialAccountService');
AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.classes.CpInfoContainer');

trait CpTrait {

    protected $cps;

    protected $join_sns_kind_array =  [
        'fb'       => SocialAccountService::SOCIAL_MEDIA_FACEBOOK,
        'tw'       => SocialAccountService::SOCIAL_MEDIA_TWITTER,
        'ggl'      => SocialAccountService::SOCIAL_MEDIA_GOOGLE,
        'yh'       => SocialAccountService::SOCIAL_MEDIA_YAHOO,
        'line'     => SocialAccountService::SOCIAL_MEDIA_LINE,
        'insta'    => SocialAccountService::SOCIAL_MEDIA_INSTAGRAM,
        'platform' => SocialAccountService::SOCIAL_MEDIA_PLATFORM,
        'linkedin'     => SocialAccountService::SOCIAL_MEDIA_LINKEDIN,
    ];

    /**
     * @param $id
     * @return mixed
     */
    public function getCpById($id) {
        if ($id === null) {
            return null;
        }
        return $this->cps->findOne($id);
    }

    /**
     * @param $brand_id
     * @param int $page
     * @param int $count
     * @param int $is_archive
     * @return mixed
     */
    public function getDraftCpsByBrandIdAndArchiveFlg($brand_id, $page = 1, $count = 20, $is_archive = 0) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status' => Cp::STATUS_DRAFT,
                'archive_flg' => $is_archive
            ),
            'pager' => array(
                'page' => $page,
                'count' => $count,
            ),
            'order' => array(
                'name' => 'created_at',
                'direction' => 'desc'
            )
        );
        return $this->cps->find($filter);
    }

    /**
     * @param $brand_id
     * @param int $is_archive
     * @return mixed
     */
    public function getDraftCpsCountByBrandIdAndArchiveFlg($brand_id, $is_archive = 0) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status' => Cp::STATUS_DRAFT,
                'archive_flg' => $is_archive
            )
        );
        return $this->cps->count($filter);
    }

    /**
     * @return mixed
     */
    public function getCpsForBatch() {

        $now = date('Y/m/d H:i:s');

        $filter = array(
            'conditions' => array(
                "type" => Cp::TYPE_CAMPAIGN,
                'status' => Cp::STATUS_SCHEDULE,
                'public_date:<' => $now

            ),
            'order' => array(
                'name' => 'public_date',
                'direction' => 'desc'
            )
        );
        return $this->cps->find($filter);
    }

    /**
     * @param $brand_id
     * @param int $page
     * @param int $count
     * @param int $is_archive
     * @return mixed
     */
    public function getCpsNotDraftByBrandIdAndArchiveFlg($brand_id, $type, $page = 1, $count = 20, $is_archive = 0) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status:!=' => Cp::STATUS_DRAFT,
                'archive_flg' => $is_archive,
                'type' => $type
            ),
            'order' => array(
                'name' => 'created_at',
                'direction' => 'desc'
            ),
            'pager' => array(
                'page' => $page,
                'count' => $count,
            )
        );
        return $this->cps->find($filter);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getCpsNotDraftByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status:!=' => Cp::STATUS_DRAFT
            ),
        );
        return $this->cps->find($filter);
    }

    public function getCpsByFilter($filter) {
        if(!$filter) {
            return null;
        }
        return $this->cps->find($filter);
    }

    public function getPublishedCampaign($brand_id, $page = 1, $count = 20) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status:!=' => Cp::STATUS_DRAFT,
                'type' => Cp::TYPE_CAMPAIGN
            ),
            'order' => array(
                'name' => 'created_at',
                'direction' => 'desc'
            ),
            'pager' => array(
                'page' => $page,
                'count' => $count,
            )
        );
        return $this->cps->find($filter);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getPublishedCampaignAndMessage($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status:!=' => Cp::STATUS_DRAFT
            ),
            'order' => array(
                'name' => 'created_at',
                'direction' => 'desc'
            )
        );

        return $this->cps->find($filter);
    }

    public function getPublishedCampaignCountByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status:!=' => Cp::STATUS_DRAFT,
                'type' => Cp::TYPE_CAMPAIGN
            )
        );
        return $this->cps->count($filter);
    }

    public function getCpsNotDraftCountByBrandId($brand_id, $type, $is_archive = 0) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status:!=' => Cp::STATUS_DRAFT,
                'archive_flg' => $is_archive,
                'type' => $type
            )
        );
        return $this->cps->count($filter);
    }

    /**
     * @return mixed
     */
    //TODO 使っていない関数？
    public function getPublicCps() {
        $filter = array(
            'conditions' => array(
                'status:' => Cp::STATUS_FIX,
            ),
            'order' => array(
                'name' => 'start_date',
                'direction' => 'desc'
            )
        );
        return $this->cps->find($filter);
    }

    /**
     * モニプラ用にキャンペーン情報を返す
     * @param $app_id
     * @return mixed
     */
    public function getPublicCpsForMonipla($app_id) {
        $filter = array(
            'app_id' => $app_id
        );
        $db = aafwDataBuilder::newBuilder();
        $db->setObjectMode('__ON__');
        return $db->getPublicCpsForMonipla($filter);
    }

    /**
     * 公開中キャンペーン SELECT 条件取得
     * user_id 指定すると未参加のキャンペーン一覧取得
     * @return array
     */
    public function buildPublicCpsFilter() {
        $filter = array(
            'status' => Cp::STATUS_FIX,
            'end_date' => date('Y/m/d H:i:s'),
            'show_monipla_com_flg' => true,
            'test_page' => 0,
            'type' => Cp::TYPE_CAMPAIGN,
        );
        return $filter;
    }

    /**
     * 応募可能なキャンペーンの取得
     * @return mixed
     */
    public function getOpenCps() {

        $now = date('Y/m/d H:i:s');
        $filter = array(
            'conditions' => array(
                'status' => Cp::STATUS_FIX,
                'start_date:<' => $now,
                'end_date:>' => $now,
            ),
            'order' => array(
                'name' => 'start_date',
                'direction' => 'desc'
            )
        );
        return $this->cps->find($filter);
    }

    /**
     * @param $brand_id
     * @param $reference_url
     * @return mixed
     */
    public function getCpByBrandIdAndReferenceUrl($brand_id, $reference_url) {
        $filter = array(
            'brand_id' => $brand_id,
            'reference_url' => $reference_url
        );

        return $this->cps->find($filter);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getOpenCpsByBrandId($brand_id) {
        $now = date('Y/m/d H:i:s');

        $filter = array(
            'where' => "del_flg = 0 AND brand_id = " . $brand_id
                . " AND status = " . Cp::STATUS_FIX
                . " AND type = " . Cp::TYPE_CAMPAIGN
                . " AND start_date <= '" . $now
                . "' AND (permanent_flg = " . Cp::PERMANENT_FLG_ON
                . " OR end_date > '" . $now
                . "')",
            'order' => array(
                'name' => 'start_date',
                'direction' => 'desc'
            )
        );

        return $this->cps->find($filter);
    }

    public function getPublicCpsByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'status' => array(Cp::STATUS_SCHEDULE, Cp::STATUS_FIX, Cp::STATUS_DEMO)
            )
        );
        return $this->cps->find($filter);
    }

    public function getParticipatedPublicCpUsers($user_id, $brand_id) {
        $db = new aafwDataBuilder();
        return $db->getBySQL("
            SELECT u.id, u.cp_id FROM cp_users u
              INNER JOIN cps c ON u.cp_id = c.id
              WHERE
                u.del_flg = 0 AND c.del_flg = 0 AND u.user_id = " . $user_id . " AND c.brand_id = " . $brand_id . "  AND c.status IN(2, 3, 4, 5)
        ", array());
    }

    /**
     * @param $brand_id
     * @param $announce_type
     * @return mixed
     */
    public function createCp($brand_id, $announce_type, $join_limit_flg = CP::JOIN_LIMIT_OFF) {
        $cp = $this->cps->createEmptyObject();
        $cp->brand_id = $brand_id;
        $cp->join_limit_flg = $join_limit_flg;
        $cp->selection_method = $announce_type;
        if ($announce_type == CpCreator::ANNOUNCE_DELIVERY) {
            $cp->shipping_method = Cp::SHIPPING_METHOD_PRESENT;
        }
        $cp->show_monipla_com_flg = Cp::FLAG_SHOW_VALUE;
        $cp->show_recruitment_note = Cp::SHOW_RECRUITMENT_NOTE;

        if ($announce_type == CpCreator::ANNOUNCE_NON_INCENTIVE) {
            $cp->permanent_flg = Cp::PERMANENT_FLG_ON;
        }

        if($join_limit_flg == Cp::JOIN_LIMIT_ON) {
            // 限定キャンペーンの時は露出をしない
            $cp->show_monipla_com_flg   = Cp::FLAG_HIDE_VALUE;
            $cp->share_flg              = Cp::FLAG_HIDE_VALUE;
            $cp->show_top_page_flg      = Cp::FLAG_HIDE_VALUE;
        }

        $cp->type = cp::TYPE_CAMPAIGN;
        $cp->recruitment_note = '・関係者の応募はご遠慮ください。
・ご応募は日本国内にお住まいの方に限らせていただきます。
・当選されたお客様には、モニプラにご登録のメールアドレスへご連絡いたします。
・賞品の発送は諸事情により遅れる場合があります。あらかじめご了承ください。
・当選賞品の販売・換金はできません。
・当選に関するお問い合わせにはお答えいたしかねます。
・やむを得ない事情により、賞品は予告なく変更となることがあります。
・当選者の住所が不明確な場合や、転居による住所変更などの理由により、賞品をお届けできない場合、当選資格を無効とさせていただく場合があります。
・同一住所への賞品の発送は1点までとさせていただく場合があります。
・その他、弊社が不正とみなした方は当選対象外とさせていただきます。';
        if ($cp->selection_method === CpCreator::ANNOUNCE_LOTTERY) {
            $cp->recruitment_note .='
・当選人数に達し次第、キャンペーンは終了となります。';
        }
        $cp->extend_tag = "";
        return $this->cps->save($cp);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function createMsg($brand_id) {
        $cp = $this->cps->createEmptyObject();
        $cp->brand_id = $brand_id;
        $cp->type = cp::TYPE_MESSAGE;
        $cp->status = cp::STATUS_SCHEDULE;
        $cp->recruitment_note = "";
        return $this->cps->save($cp);
    }


    /**
     * @param $cp_id
     * @return mixed
     */
    public function copyCp($cp_id) {
        $cp = $this->getCpById($cp_id);
        $new_cp = $this->cps->createEmptyObject();
        $new_cp->brand_id = $cp->brand_id;
        $new_cp->title = $cp->title;
        $new_cp->selection_method = $cp->selection_method;
        $new_cp->shipping_method = $cp->shipping_method;
        $new_cp->winner_count = $cp->winner_count;
        $new_cp->image_url = $cp->image_url;
        $new_cp->back_monipla_flg = $cp->back_monipla_flg;
        $new_cp->show_monipla_com_flg = $cp->show_monipla_com_flg;
        $new_cp->share_flg = $cp->share_flg;
        $new_cp->show_top_page_flg = $cp->show_top_page_flg;
        $new_cp->get_address_type = $cp->get_address_type;
        $new_cp->show_winner_label = $cp->show_winner_label;
        $new_cp->winner_label = $cp->winner_label;
        $new_cp->show_recruitment_note = $cp->show_recruitment_note;
        $new_cp->recruitment_note = $cp->recruitment_note;
        $new_cp->join_limit_flg = $cp->join_limit_flg;
        $new_cp->join_limit_sns_flg = $cp->join_limit_sns_flg;
        $new_cp->permanent_flg = $cp->permanent_flg;
        $new_cp->extend_tag = "";
        return $this->cps->save($new_cp);
    }

    public function copyCpWithNewData($cp, $data = array()){
        $new_cp = $this->cps->createEmptyObject();
        $new_cp->brand_id = $cp->brand_id;
        $new_cp->type = $cp->type;
        $new_cp->public_date = $data['start_date'];
        $new_cp->start_date = $data['start_date'];
        $new_cp->end_date = $data['end_date'];
        $new_cp->announce_date = $data['announce_date'];
        $new_cp->selection_method = $cp->selection_method;
        $new_cp->shipping_method = $cp->shipping_method;
        $new_cp->winner_count = $cp->winner_count;
        $new_cp->image_url = $cp->image_url;
        $new_cp->image_rectangle_url = $cp->image_rectangle_url;
        $new_cp->join_limit_sns_flg = $cp->join_limit_sns_flg;
        $new_cp->join_limit_flg = $cp->join_limit_flg;
        $new_cp->share_flg = $cp->share_flg;
        $new_cp->show_monipla_com_flg = $cp->show_monipla_com_flg;
        $new_cp->back_monipla_flg = $cp->back_monipla_flg;
        $new_cp->show_top_page_flg = $cp->show_top_page_flg;
        $new_cp->send_mail_flg = $cp->send_mail_flg;
        $new_cp->get_address_type = $cp->get_address_type;
        $new_cp->fix_basic_flg = $data['fix_basic_flg'];
        $new_cp->fix_attract_flg =  $data['fix_attract_flg'];
        $new_cp->status = $data['status'];
        $new_cp->show_winner_label = $cp->show_winner_label;
        $new_cp->winner_label = $cp->winner_label;
        $new_cp->show_recruitment_note = $cp->show_recruitment_note;
        $new_cp->recruitment_note = $cp->recruitment_note;
        $new_cp->use_extend_tag = $cp->use_extend_tag;
        $new_cp->extend_tag = $cp->extend_tag;
        $new_cp->restricted_age_flg = $cp->restricted_age_flg;
        $new_cp->restricted_age = $cp->restricted_age;
        $new_cp->restricted_gender_flg = $cp->restricted_gender_flg;
        $new_cp->restricted_gender = $cp->restricted_gender;
        $new_cp->restricted_address_flg = $cp->restricted_address_flg;
        $new_cp->au_flg = $cp->au_flg;
        $new_cp->reference_url = $cp->reference_url;
        $new_cp->permanent_flg = $cp->permanent_flg;

        return $this->cps->save($new_cp);
    }

    /**
     * @param Cp $cp
     */
    public function updateCp(Cp $cp) {
        $this->cps->save($cp);
        $cache_manager = new CacheManager();
        $cache_manager->clearCampaignLPInfo($cp->id);
        $cache_manager->clearCpInfo($cp->id);
    }

    /**
     * @param Cp $cp
     */
    public function deleteCp(Cp $cp) {
        $this->cps->deleteLogical($cp);
    }

    /**
     * @param $cp_id
     * @return bool
     */
    public function isFixedCpInfo($cp_id) {

        $cp = $this->getCpById($cp_id);

        if ($cp->fix_basic_flg === CpAction::STATUS_FIX)
            if ($cp->fix_attract_flg === CpAction::STATUS_FIX) {
                return true;
            }
        return false;
    }

    /**
     * @param $cp_id
     * @return bool
     */
    public function isDraftCp($cp_id) {
        $cp = $this->getCpById($cp_id);
        if ($cp->status === Cp::STATUS_DRAFT) {
            return true;
        }
        return false;
    }

    /**
     * @param $cp_id
     * @return bool
     */
    public function isFixedCp($cp_id) {
        $cp = $this->getCpById($cp_id);
        if ($cp->status === Cp::STATUS_FIX) {
            return true;
        }
        return false;
    }

    /**
     * @param $cp_id
     * @return bool
     */
    public function isScheduledCp($cp_id) {
        $cp = $this->getCpById($cp_id);
        if ($cp->status === Cp::STATUS_SCHEDULE) {
            return true;
        }
        return false;
    }

    /**
     * @param $cp
     * @return bool
     */
    public function isPublicCp($cp) {
        if ($cp->status != Cp::STATUS_FIX) {
            return false;
        }

        if (!$this->isPast($cp->start_date)) {
            return false;
        }

        if ($cp->permanent_flg == Cp::PERMANENT_FLG_ON || !$this->isPast($cp->end_date)) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getCpStore() {
        return $this->cps;
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getCpByCpAction(CpAction $cp_action) {
        return CpInfoContainer::getInstance()->getCpById($cp_action->getCpActionGroup()->cp_id);
    }

    public function getCps($page = 1, $limit = 20, $params = array(), $order = null) {
        $filter = array(
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
        );

        return $this->cps->find($filter);
    }

    public function countCps(){

        return $this->cps->count();
    }

    public function getJoinSnsKindByClientId($client_id){

        return $this->join_sns_kind_array[$client_id]?:'0';

    }

    public function getCpIdsHaveMoreThan2Group($limitMode){
        $db = aafwDataBuilder::newBuilder();
        $condition = array();
        if($limitMode){
            $condition['LIMIT_MODE'] = '__ON__';
        }
        return $db->getCampaignsHaveMoreThan2Group($condition);
    }
}
