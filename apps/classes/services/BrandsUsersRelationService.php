<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.UserService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class BrandsUsersRelationService extends aafwServiceBase {
    /** @var aafwEntityStoreBase $brands_users_relations */
    public $brands_users_relations;
    private static $_instance = null;

    const FROM_KIND_BRANDCO = 1;
    const FROM_KIND_CAMPAIGN = 2;
    const ADMIN_USER = 1;
    const NOT_ADMIN_USER = 0;

    const STATUS_OPTIN = 1;
    const STATUS_OPTOUT = 2;

    const NON_WITHDRAW = 0;
    const WITHDRAW = 1;

    const MANAGER_USER_WITHDRAW_REASON = "マネージャーからユーザを退会しました";
    const MANAGER_USER_WITHDRAW_NO = 0;

    const BLOCK = -1;
    const NON_RATE = 0;
    const RATE_1 = 1;
    const RATE_2 = 2;
    const RATE_3 = 3;
    const RATE_4 = 4;
    const RATE_5 = 5;

    const NOT_HAVE_ADDRESS = 0;
    const NOT_DUPLICATE_ADDRESS = 1;

    /** @var aafwDataBuilder $data_builder */
    private $data_builder;

    public function __construct() {
        $this->brands_users_relations = $this->getModel("BrandsUsersRelations");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->data_builder = aafwDataBuilder::newBuilder();
    }

    public function getAdminUsers() {
        $filter = array(
            'admin_flg' => 1
        );
        return $this->brands_users_relations->find($filter);
    }

    public function getBrandsUsersRelation($brandId, $userId, $params = array()) {
        if (!$brandId || !$userId) return;
        $filter = array(
            'brand_id' => $brandId,
            'user_id' => $userId,
        );
        $filter = array_merge($filter, $params);
        return $this->brands_users_relations->findOne($filter);
    }

    public function getMaxNoByBrandId($brandId) {
        $filter = array(
            'brand_id' => $brandId,
        );
        return $this->brands_users_relations->getMax('no', $filter);
    }

    public function createBrandsUsersRelation($brandUserRelation) {
        $this->brands_users_relations->save($brandUserRelation);
    }

    public function updatePersonalInfo($brands_users_relation_id, $personal_info_flg) {
        if (Util::isNullOrEmpty($brands_users_relation_id)) {
            return;
        }
        if (Util::isNullOrEmpty($personal_info_flg)) {
            return;
        }
        $builder = $this->data_builder;
        $builder->executeUpdate("
            UPDATE brands_users_relations
              SET personal_info_flg = {$personal_info_flg} WHERE id = {$brands_users_relation_id} AND del_flg = 0
        ");
    }

    public function createEmptyBrandsUsersRelation() {
        return $this->brands_users_relations->createEmptyObject();
    }

    public function getBrandsAdminUsersByBrandId($brandId) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
                'admin_flg' => self::ADMIN_USER,
                'withdraw_flg' => self::NON_WITHDRAW
            ),
            'order' => array(
                'name' => 'id',
                'direction' => 'desc',
            ),
        );
        return $this->brands_users_relations->find($filter);
    }

    public function deleteAdminFlg($brandId, $userId) {
        $adminUser = $this->getBrandsUsersRelation($brandId, $userId);
        $adminUser->admin_flg = self::NOT_ADMIN_USER;

        $this->brands_users_relations->save($adminUser);
    }

    /**
     * 各ブランド毎の管理者に対して管理者フラグを付与
     * @param $brandId
     * @param $userId
     */
    public function setAdminFlg($brandId, $userId) {
        $brands_users = $this->getBrandsUsersRelation($brandId, $userId);
        $brands_users->admin_flg = BrandsUsersRelationService::ADMIN_USER;
        $this->createBrandsUsersRelation($brands_users);
    }

    /**
     * ブランドユーザーのoptinフラグを変更する
     * @param $brandId
     * @param $userId
     * @param $optinFlg
     * @return bool
     */
    public function changeOptinFlg($brandId, $userId, $optinFlg) {
        if (in_array($optinFlg, array(self::STATUS_OPTIN, self::STATUS_OPTOUT)) == false) {
            return false;
        }
        $brands_user = $this->getBrandsUsersRelation($brandId, $userId);
        if (!$brands_user) {
            return false;
        }
        $brands_user->optin_flg = $optinFlg;
        $this->brands_users_relations->save($brands_user);
        return true;
    }

    /**
     * オプトインの変更する
     * @param BrandsUsersRelation $brands_users_relation
     * @param $optin_flg
     * @return bool
     */
    public function setOptinFlg(BrandsUsersRelation $brands_users_relation, $optin_flg) {
        if (!in_array($optin_flg, array(self::STATUS_OPTIN, self::STATUS_OPTOUT))) return;

        $brands_users_relation->optin_flg = $optin_flg;
        $this->brands_users_relations->save($brands_users_relation);
        return true;
    }

    public function changeOptinFlgOnClosedBrand($brands_users_relation) {
        if ($brands_users_relation->admin_flg == BrandsUsersRelationService::ADMIN_USER) {
            $brands_users_relation->admin_flg = BrandsUsersRelationService::NOT_ADMIN_USER;
        }
        $brands_users_relation->optin_flg = self::STATUS_OPTOUT;

        $this->brands_users_relations->save($brands_users_relation);
    }

    public function getBrandsUsersRelationsByBrandId($brandId, $filter = array()) {

        $filter = array_merge($filter, array(
            'brand_id' => $brandId,
            'withdraw_flg' => 0
        ));
        return $this->brands_users_relations->find($filter);
    }

    public function getBrandsUsersRelationsByUserId($userId) {
        if (!$userId) return;
        $filter = array(
            'user_id' => $userId
        );
        return $this->brands_users_relations->find($filter);
    }

    public function getBrandsUsersRelationByBrandIdAndNo($brandId, $no) {
        if (!$brandId || !$no) return;
        $filter = array(
            'brand_id' => $brandId,
            'no' => $no
        );
        return $this->brands_users_relations->findOne($filter);
    }

    public function countBrandsUsersRelationsByBrandId($brandId) {
        if (Util::isNullOrEmpty($brandId)) {
            return null;
        }

        $data_builder = aafwDataBuilder::newBuilder();
        $result = $data_builder->executeUpdate(
            "SELECT COUNT(*) FROM (SELECT withdraw_flg, del_flg FROM brands_users_relations WHERE brand_id = {$brandId}) bul WHERE bul.del_flg = 0 AND bul.withdraw_flg = 0");
        if (!$result) {
            return null;
        }
        $row = $data_builder->fetchResultSet($result);
        return $row['COUNT(*)'];
    }

    public function getBrandsUsersRelationsByConditions($conditions) {
        return $this->brands_users_relations->find($conditions);
    }

    public function getBrandsUsersRelationsByBrandIdAndUserIds($brandId, $userIds) {
        $filter = array(
            'brand_id' => $brandId,
            'user_id' => $userIds
        );
        return $this->brands_users_relations->find($filter);
    }

    public function getBrandsUsersRelationsByBrandIdAndUserId($brandId, $userIds) {
        $filter = array(
            'brand_id' => $brandId,
            'user_id' => $userIds
        );
        return $this->brands_users_relations->findOne($filter);
    }

    public function getBrandsUsersRelationById($brands_users_relations_id) {
        $filter = array(
            'id' => $brands_users_relations_id,
        );
        return $this->brands_users_relations->findOne($filter);
    }

    public function changeFanRate($brands_users_relations_id, $rate) {
        $brands_user = $this->getBrandsUsersRelationById($brands_users_relations_id);
        if (!$brands_user) {
            return false;
        }
        $brands_user->rate = $rate;
        $this->brands_users_relations->save($brands_user);
        return true;
    }

    /**
     * @param $brandsUsersRelations
     */
    public function setLoginInfo(BrandsUsersRelation $brandsUsersRelations) {
        $date = date("Y-m-d H:i:s", time());

        $brandsUsersRelations->last_login_date = $date;
        $brandsUsersRelations->login_count++;

        $this->createBrandsUsersRelation($brandsUsersRelations);
    }

    /**
     * @param $created_at
     * データ作成日より、ログイン期間を以下のいづれかで返す
     * 登録してから1週間以内 => NEW
     * 登録してから2週間以内 => 1W
     * 登録してから2ヶ月以内 => 1M
     * 登録してから1年以上 => 1Y〜
     */
    public function getHistorySummary($created_at) {
        $current_date = date("Y-m-d H:i:s");
        if (date("Y-m-d 00:00:00", strtotime($current_date . " -7 day")) < $created_at) {
            $history_summary = 'NEW';
        } elseif (date("Y-m-d 00:00:00", strtotime($current_date . " -1 month")) < $created_at) {
            for ($w = 1; $w <= 4; $w++) {
                if (date("Y-m-d 00:00:00", strtotime($current_date . " -" . ($w + 1) . " week")) < $created_at) {
                    $history_summary = $w . '週間';
                    break;
                }
            }
        } elseif (date("Y-m-d 00:00:00", strtotime($current_date . " -1 year")) < $created_at) {
            for ($m = 1; $m <= 11; $m++) {
                if (date("Y-m-d 00:00:00", strtotime($current_date . " -" . ($m + 1) . " month")) < $created_at) {
                    $history_summary = $m . 'ヶ月';
                    break;
                }
            }
        } else {
            $history_summary = '1年〜';
        }
        return $history_summary;
    }

    /**
     * @param $last_login_date
     * 最終ログイン時間よりどれだけ時間が経っているのか、以下のいづれかで返す
     * ログインから5分以内（今と表示）、1時間以内、3時間以内、6時間以内、12時間以内、24時間以内、
     * 1日前、2日前、3日前、
     * 1週間前、2週間前、3週間前、
     * 1ヶ月前、2ヶ月前、3ヶ月前、4ヶ月前、5ヶ月前、6ヶ月前、7ヶ月前、8ヶ月前、9ヶ月前、10ヶ月前、11ヶ月前、
     * 1年前
     * 未ログイン
     */
    public function getLastLoginSummary($last_login_date) {

        if ($last_login_date == '0000-00-00 00:00:00') {
            return '-';
        }

        $next_day_start = date("Y-m-d 00:00:00", strtotime("+1 day"));
        $time_gap = (strtotime(date("Y-m-d H:i:s")) - strtotime($last_login_date));

        if ($time_gap / (60 * 60 * 24) < 1) {
            if ($time_gap / 60 < 5) {
                $last_login_summary = '今';
            } elseif ($time_gap / (60 * 60) < 1) {
                $last_login_summary = '1時間以内';
            } elseif ($time_gap / (60 * 60) < 3) {
                $last_login_summary = '3時間以内';
            } elseif ($time_gap / (60 * 60) < 6) {
                $last_login_summary = '6時間以内';
            } elseif ($time_gap / (60 * 60) < 12) {
                $last_login_summary = '12時間以内';
            } else {
                $last_login_summary = '24時間以内';
            }
        } elseif ($last_login_date >= date("Y-m-d H:i:s", strtotime($next_day_start . " -1 week"))) {
            for ($d = 1; $d <= 3; $d++) {
                if ($last_login_date >= date("Y-m-d H:i:s", strtotime($next_day_start . " -" . ($d + 1) . " day"))) {
                    $last_login_summary = $d . '日前';
                    break;
                }
                if ($d == 3) {
                    $last_login_summary = $d . '日前';
                }
            }
        } elseif ($last_login_date > date("Y-m-d H:i:s", strtotime($next_day_start . " -1 month"))) {
            for ($w = 1; $w <= 4; $w++) {
                if ($last_login_date > date("Y-m-d H:i:s", strtotime($next_day_start . " -" . ($w + 1) . " week"))) {
                    $last_login_summary = $w . '週間前';
                    break;
                }
            }
        } elseif ($last_login_date > date("Y-m-d H:i:s", strtotime($next_day_start . " -1 year"))) {
            for ($m = 1; $m <= 11; $m++) {
                if ($last_login_date > date("Y-m-d H:i:s", strtotime($next_day_start . " -" . ($m + 1) . " month"))) {
                    $last_login_summary = $m . 'ヶ月前';
                    break;
                }
            }
        } else {
            $last_login_summary = '1年前';
        }
        return $last_login_summary;
    }

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function withdrawByBrandUserRelation(BrandsUsersRelation $brand_user_relation, $isFromAAID = false) {
        $brand_user_relation->withdraw_flg = 1;
        $this->brands_users_relations->save($brand_user_relation);

        /** @var WithdrawLogs $withdraw_log_store */
        $withdraw_log_store = $this->getModel('WithdrawLogs');

        $new_log = $withdraw_log_store->createEmptyObject();
        $new_log->brand_user_relation_id = $brand_user_relation->id;
        $new_log->withdraw_from_aaid_flg = $isFromAAID ? 1 : 0;
        return $withdraw_log_store->save($new_log);
    }


    public function getLastWithdrawDatebyBrandUserRelationId($brand_user_relationship_id){
        $withdraw_log_store = $this->getModel('WithdrawLogs');
        $filter = array(
            'brand_user_relation_id' => $brand_user_relationship_id,
        );
        return $withdraw_log_store->getMax('created_at',$filter);
    }

    public function createWithdrawReason($withdraw_log_id, $reason, $question_num) {
        if ($this->isEmpty($reason)) {
            return;
        }
        $store = $this->getModel('WithdrawReasonsLogs');
        $new_reason = $store->createEmptyObject();
        $new_reason->withdraw_log_id = $withdraw_log_id;
        $new_reason->reason = $reason;
        $new_reason->question_num = $question_num;

        $store->save($new_reason);
    }

    public function getAllRelationsByUserId($user_id) {
        $condition = array(
            'user_id' => $user_id,
            'withdraw_flg' => 0
        );
        return $this->brands_users_relations->find($condition);
    }
    
    public function getAllRelationsByUserIds($user_ids) {
        $condition = array(
            'user_id' => $user_ids,
            'withdraw_flg' => 0
        );
        return $this->brands_users_relations->find($condition);
    }

    public function getWithdrawFanUserRelationsWithoutBrandId($del_info_flg = 0) {
        $filter = array(
            'conditions' => array(
                'withdraw_flg' => 1,
                'del_info_flg' => $del_info_flg
            )
        );
        return $this->brands_users_relations->find($filter);
    }

    public function setDelInfoFlgByBrandUserRelation(BrandsUsersRelation $brand_user_relation) {
        $brand_user_relation->del_info_flg = 1;
        $this->brands_users_relations->save($brand_user_relation);
    }

    public function canSendMail($brand_id, $user_id) {
        $brand_user_relation = $this->getBrandsUsersRelation($brand_id, $user_id);
        if ($brand_user_relation->optin_flg != self::STATUS_OPTOUT) {
            return true;
        }
        return false;
    }

    public function getBrandsUsersRelationsByBrandIdOrderById($brandId) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
            ),
            'order' => array(
                'name' => 'id',
                'direction' => 'asc',
            ),
        );
        return $this->brands_users_relations->find($filter);
    }

    public function getNewBrandsUsersRelations($brandId) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
                'no' => 0
            ),
            'order' => array(
                'name' => 'id',
                'direction' => 'asc',
            ),
        );
        return $this->brands_users_relations->find($filter);
    }

    public function getSavedBrandsUsersRelationsNo($brandId) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
                'no:>' => 0
            ),
        );
        return $this->brands_users_relations->countOnMaster($filter, 'id');
    }

    public function getProfileQuestionnaireStatus($personal_info_flg) {
        if ($personal_info_flg == BrandsUsersRelation::SIGNUP_WITHOUT_INFO) {
            return '未取得';
        } elseif ($personal_info_flg == BrandsUsersRelation::SIGNUP_WITH_INFO) {
            return '取得済み';
        } else {
            return '要再取得';
        }
    }

    public function getMemberRate($rate) {
        $rate_info = array();
        switch ($rate) {
            case self::BLOCK :
                $rate_info['rate'] = '';
                $rate_info['image_url'] = '/img/raty/iconBlockOn.png';
                break;
            case self::NON_RATE :
                $rate_info['rate'] = '-';
                $rate_info['image_url'] = '/img/raty/iconStar_0.png';
                break;
            case self::RATE_1 :
                $rate_info['rate'] = self::RATE_1;
                $rate_info['image_url'] = '/img/raty/iconStar_1.png';
                break;
            case self::RATE_2 :
                $rate_info['rate'] = self::RATE_2;
                $rate_info['image_url'] = '/img/raty/iconStar_2.png';
                break;
            case self::RATE_3 :
                $rate_info['rate'] = self::RATE_3;
                $rate_info['image_url'] = '/img/raty/iconStar_3.png';
                break;
            case self::RATE_4 :
                $rate_info['rate'] = self::RATE_4;
                $rate_info['image_url'] = '/img/raty/iconStar_4.png';
                break;
            case self::RATE_5 :
                $rate_info['rate'] = self::RATE_5;
                $rate_info['image_url'] = '/img/raty/iconStar_5.png';
                break;
        }
        return $rate_info;
    }

    public function getAgeFromBirthday($birthday) {
        if (!empty($birthday) && $birthday != '0000-00-00') {
            $now = date('Ymd');
            $birthday_ymd = date('Ymd', strtotime($birthday));
            $age = floor(($now - $birthday_ymd) / 10000);
        } else {
            $age = '';
        }
        return $age;
    }

    public function updateBrandsUsersRelation($brandsUsersRelation) {
        return $this->brands_users_relations->save($brandsUsersRelation);
    }
    
    public function updateDuplicateAddressCount($ids, $duplicateCount) {

        if(!$ids) return ;

        $sql = "
            UPDATE brands_users_relations
            SET duplicate_address_count = ".$duplicateCount."
            WHERE id IN (".implode(',',$ids).")
        ";

        $this->data_builder->executeUpdate($sql);
    }
}
