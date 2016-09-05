<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class user_search extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId = Manager::MENU_USER_SEARCH;
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'user_search',
    );

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var UserSearchService $user_search_service */
        $user_search_service = $this->getService('UserSearchService');
        /** @var UserService $user_service */
        $user_service = $this->getService('UserService');
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->getService('BrandsUsersRelationService');
        /** @var BrandService $brand_service */
        $brand_service = $this->getService('BrandService');
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->getService('CpUserService');
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        /** @var SocialAccountService $social_account_service */
        $social_account_service = $this->getService('SocialAccountService');
        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = $this->getService('CpUserActionStatusService');

        $this->Data['sns_type'] = array(
            UserSearchService::USER_SEARCH_DEFAULT => UserSearchService::SEARCH_DEFAULT,
            SocialAccountService::SOCIAL_MEDIA_FACEBOOK => UserSearchService::SNS_TYPE_FACEBOOK,
            SocialAccountService::SOCIAL_MEDIA_TWITTER => UserSearchService::SNS_TYPE_TWITTER,
            SocialAccountService::SOCIAL_MEDIA_INSTAGRAM => UserSearchService::SNS_TYPE_INSTAGRAM,
            SocialAccountService::SOCIAL_MEDIA_GOOGLE => UserSearchService::SNS_TYPE_GOOGLE,
            SocialAccountService::SOCIAL_MEDIA_YAHOO => UserSearchService::SNS_TYPE_YAHOO,
        );
        $this->Data['search'] = $this->search_type;

        $allied_user = null;
        $brandco_user = null;

        // 検索タイプごとにターゲットを特定する(AlliedIDかBRANDCoのアカウント情報のいずれかを取得)
        switch ($this->search_type) {
            case UserSearchService::USER_SEARCH_PLATFORM_ID:
                if (!$this->platform_id) break;
                $allied_user = $user_search_service->getPlatformUserInfo($this->platform_id);
                break;
            case UserSearchService::USER_SEARCH_BRANDCO_ID:
                if (!$this->brandco_uid) break;
                $brandco_user = $user_service->getUserByBrandcoUserId($this->brandco_uid);
                break;
            case UserSearchService::USER_SEARCH_SNS:
                if ($this->sns == UserSearchService::USER_SEARCH_DEFAULT) break;
                $social_account = $social_account_service->getSocialAccountBySocialMediaIdAndSocialMediaAccountId($this->sns, $this->sns_id);
                $brandco_user = $user_service->getUserByBrandcoUserId($social_account->user_id);
                break;
            case UserSearchService::USER_SEARCH_AA_MAIL:
                if (!$this->allied_mail_address) break;
                $allied_user = $user_search_service->getUsersByMailAddress($this->allied_mail_address);
                break;
            case UserSearchService::USER_SEARCH_BRAND_MAIL:
                if (!$this->brandco_mail_address) break;
                $brandco_user = $user_service->getUserByEmail($this->brandco_mail_address);
                break;
            case UserSearchService::USER_SEARCH_BRAND:
                if (!$this->member_no && !$this->brand_id) {
                    break;
                } elseif (!$this->member_no || !$this->brand_id) {
                    $this->Data['message'] = 'ブランドIDと会員番号の両方を入力して下さい。';
                    break;
                }
                $target_brands_users_relation = $brands_users_relation_service->getBrandsUsersRelationByBrandIdAndNo($this->brand_id, $this->member_no);
                $brandco_user = $user_service->getUserByBrandcoUserId($target_brands_users_relation->user_id);
                break;
        }

        // 特定したアカウント情報から各アカウント情報を取得
        if ($allied_user) {
            $brandco_user = $user_service->getUserByMoniplaUserId($allied_user->id);
        } elseif ($brandco_user) {
            $allied_user = $user_search_service->getPlatformUserInfo($brandco_user->monipla_user_id);
        }

        // 各アカウント情報をセットする
        $this->Data['platform_info'] = $allied_user;
        $this->Data['brandco_user_info'] = $brandco_user;

        // BRANDCoのアカウント情報があれば、紐づく情報を取得する
        if ($brandco_user) {
            // ファン登録情報をセット
            $brands_users_relations = $brands_users_relation_service->getBrandsUsersRelationsByUserId($brandco_user->id);

            $this->Data['in_use_brandco'] = false;
            $this->Data['brands_user_relations_info'] = array();
            foreach ($brands_users_relations as $brands_users_relation) {
                // １つでも利用中のブランドがある時は利用中とみなしBRANDCo退会ボタンを表示
                if (!$this->Data['in_use_brandco'] && $brands_users_relation->withdraw_flg == 0) {
                    $this->Data['in_use_brandco'] = true;
                }

                $fans_joined_info = $brands_users_relation->toArray();
                $fans_joined_info['brand_name'] = $brand_service->getBrandById($brands_users_relation->brand_id)->name;
                $withdraw_date = $brands_users_relation_service->getLastWithdrawDatebyBrandUserRelationId($brands_users_relation->id);
                $fans_joined_info['withdraw_date'] = $withdraw_date ?: "-";
                // 代理ログインで使用するtokenを生成
                $token_generator = new TokenWithoutSimilarCharGenerator();
                $salt = $token_generator->generateToken(512);
                $this->setSession('backdoor_login_salt', $salt);
                $token = Util::generateBackdoorLoginToken($fans_joined_info['created_at'], $salt);
                $fans_joined_info['token'] = $token;

                $this->Data['brands_user_relations_info'][] = $fans_joined_info;
            }

            // キャンペーン参加情報をセット
            $cp_users = $cp_user_service->getCpUsersByUserId($brandco_user->id);
            foreach ($cp_users as $cp_user) {
                $cp = $cp_flow_service->getCpById($cp_user->cp_id);
                if ($cp->type == Cp::TYPE_MESSAGE || $cp->status != CP::STATUS_FIX) {
                    continue;
                }
                if (!$cp_user_service->isJoinedCp($cp_user->cp_id, $cp_user->user_id, $cp_user, $cp)) {
                    continue;
                }

                $joined_cp_info = $cp->toArray();

                $joined_cp_info['title'] = $cp->getTitle();
                $no = $brands_users_relation_service->getBrandsUsersRelationsByBrandIdAndUserId($joined_cp_info['brand_id'], $cp_user->user_id)->no;
                $joined_cp_info['participated_no'] = $no ?: "-";
                $joined_cp_info['participated_date'] = $cp_user->created_at;
                $joined_cp_info['cp_user_id'] = $cp_user->id;

                $joined_cp_info['brand'] = $brand_service->getBrandById($joined_cp_info['brand_id']);
                $cp_group_id = $cp_flow_service->getCpActionGroupByCpIdAndOrderNo($cp_user->cp_id, 1)->id;
                $cp_actions = $cp_flow_service->getCpActionsByCpActionGroupId($cp_group_id);
                $total_step_count = $cp_actions->total();
                $prev_action = null;
                foreach ($cp_actions as $cp_action) {
                    $user_action_status = $cp_user_action_status_service->getCpUserActionStatusByCpUserIdAndCpActionId($cp_user->id, $cp_action->id);
                    if ($user_action_status->status == CpUserActionStatus::NOT_JOIN) {
                        break;
                    }
                    $prev_action = $user_action_status;
                }
                if ($prev_action) {
                    $last_join_action = $cp_flow_service->getCpActionById($prev_action->cp_action_id);
                    $joined_cp_info['joined_steps'] = $last_join_action->getStepNo();
                    $joined_cp_info['total_steps'] = $total_step_count;
                    $joined_cp_info['participated_condition'] = $last_join_action->getCpActionDetail()['title'];
                    $joined_cp_info['first_cp_action_id'] = $cp_flow_service->getFirstActionInGroupByAction($last_join_action)->id;
                }

                $this->Data['joined_cps'][] = $joined_cp_info;
            }
        }
        return 'manager/dashboard/user_search.php';
    }
}




