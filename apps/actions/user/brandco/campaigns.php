<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.CampaignPageValidator');
AAFW::import('jp.aainc.lib.db.aafwRedisManager');
AAFW::import('jp.aainc.classes.CpLPInfoContainer');
AAFW::import('jp.aainc.classes.BrandInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.util.HardCodingConstant');

class campaigns extends BrandcoGETActionBase {

    use BrandcoAuthTrait;

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedRedirect = true;
    public $checkCpClosed = true;
    protected $ContainerName = 'logging';
    const CACHE_TIME_OUT = 10;

    public function doThisFirst() {
        $this->Data['cp_id'] = $this->GET['exts'][0];

        $qa_sess = $this->getBrandSession('qa');
        if ($qa_sess[$this->Data['cp_id']]) {
            unset($qa_sess[$this->Data['cp_id']]);
            $this->setBrandSession('qa', $qa_sess);
        }
    }

    public function validate() {

        /** @var  $cp_flow_service CpFlowService */
        $cp_flow_service = $this->createService('CpFlowService');
        // キャンペーン情報を取得
        $this->Data['cp'] = CpInfoContainer::getInstance()->getCpById($this->Data['cp_id']);
        if (!$this->Data['cp']) {
            return "404";
        }

        // ユーザー情報を取得
        $this->Data['userInfo'] = $this->getBrandsUsersRelation() ? $this->getBrandsUsersRelation()->getUser() : null;
        $validator = new CampaignPageValidator($this->Data['cp_id'], $this->Data['userInfo'], $this->getBrand()->id, $this->Data['cp']);

        if ($this->Data["cp"]->status == Cp::STATUS_DEMO) {
            if ($this->demo_token) {
                $token = $this->demo_token;
                $this->setSession("demo_token_".$this->Data["cp"]->id, $this->demo_token);
            } else {
                $token = $this->getSession("demo_token_".$this->Data["cp"]->id);
            }
            $validator->setDemoToken($token);
        }

        $validator->validate();

        return $validator->isValid() ? true : '404';
    }

    function doAction() {
        $this->setSpecialFanCookie();

        /** @var  $cp_user_service CPUserService */
        $cp_user_service = $this->createService('CpUserService');

        $container = new CpLPInfoContainer();
        $lp_info = $container->getCpLPInfo($this->Data["cp"], BrandInfoContainer::getInstance()->getBrand());

        //サードパーティから受け取った値をSessionに保存する
        $this->preUpdateThirdPartyUserRelation();

        // キャンペーンユーザーを取得
        if ($this->Data['userInfo'] != null) {
            //セッションに入れたサードパーティの値をDBに保存する
            $this->updateThirdPartyUserRelation($this->Data['userInfo']->id);

            $cp_user = $cp_user_service->getCpUserByCpIdAndUserId($this->Data['cp_id'], $this->Data['userInfo']->id);

            list($demography_stt, $demography_err) = $this->getDemographyStatus($this->Data['cp'], $cp_user);
            $this->Data['pageStatus']['isNotMatchDemography'] = $demography_stt;
            $this->Data['pageStatus']['demographyErrors'] = $demography_err;

            if ($cp_user_service->isJoinedCp($this->Data['cp_id'], $this->Data['userInfo']->id, $cp_user, $this->Data['cp'])) {
                // 既に参加済みの場合、スレッド画面にリダイレクト
                return 'redirect: ' . Util::rewriteUrl('messages', 'thread', array("cp_id" => $this->Data['cp_id']),$_GET);
            } else if ($cp_user) {
                //限定キャンペーンならnotificationを既読になるため、read_flgを設定する
                /** @var CpUserActionStatusService $user_message_status_service */
                $user_message_status_service = $this->createService('CpUserActionStatusService');
                $entry_action = $lp_info[CpLPInfoContainer::KEY_ENTRY_ACTION];

                $cp_user_message = $user_message_status_service->getCpUserActionMessagesByCpUserIdAndCpActionId($cp_user->id, $entry_action->id);
                if ($cp_user_message) {
                    if (!$cp_user_message->read_flg) {
                        $cp_user_message->read_flg = true;
                        $user_message_status_service->updateCpUserActionMessage($cp_user_message);
                        //notificationキャッシュ更新
                        $cacheManager = new CacheManager();
                        $cacheManager->resetNotificationCount($this->brand->id, $cp_user->user_id);
                    }
                }
            }
        }else{
            list($demography_stt, $demography_err) = $this->getDemographyStatus($this->Data['cp'],null);
            $this->Data['pageStatus']['isNotMatchDemography'] = $demography_stt;
            $this->Data['pageStatus']['demographyErrors'] = $demography_err;
        }

        $this->Data['pageStatus']['cp'] = $this->Data['cp'];
        // アクション情報
        $cp_action = $lp_info[CpLPInfoContainer::KEY_ENTRY_ACTION];
        $action_info = $lp_info[CpLPInfoContainer::KEY_ACTION_INFO];
        $og_info = $lp_info[CpLPInfoContainer::KEY_OG_INFO];
        $this->Data['pageStatus']['og'] = array(
            'url'         => $og_info['url'],
            'image'       => $og_info['image'],
            'title'       => $og_info['title'],
            'description' => $og_info['description'],
        );

        $this->setSession('cp_id', $this->Data['cp_id']);

        $this->setCpRefererSession($this->Data['cp_id']);

        $this->setCpFromIdSession($this->Data['cp_id']);

        $this->Data['pageStatus']['from_id'] = $this->getSession('cp_fid_'.$this->Data['cp_id']);

        if ($this->Data["cp"]->status == Cp::STATUS_DEMO) {
            $this->Data["pageStatus"]["demo_info"]["is_demo_cp"] = true;
            $this->Data["pageStatus"]["demo_info"]["demo_cp_url"] = $this->Data["cp"]->getDemoUrl(false, $this->brand);
            $this->Data["pageStatus"]["demo_info"]["cp_id"] = $this->Data["cp"]->id;
        }

        $this->Data['brand'] = $this->brand;
        // コンテキストに保存
        $this->Data["action_info"] = $action_info;
        $this->Data['cp_action'] = $cp_action;
        // キャンペーンが終了していればエントランスのボタンの文言を変更
        $cp_status = RequestuserInfoContainer::getInstance()->getStatusByCp($this->Data['cp']);
        if ($this->Data["cp"]->isCampaignTermFinished($cp_status)) {
            $this->Data["action_info"]["concrete_action"]["button_label_text"] = '終了しました';
        }

        $this->Data['page_type'] = 'campaign';
        $this->Data['template_file'] = 'auth/PreLoginForm.php';
        // 一度ログイン o　サインアップエラーしているとき
        if ($this->Data['ActionError']) {
            if ($this->Data['ActionForm']['mode'] === 'signup') {
                $this->Data['template_file'] = 'auth/SignupForm.php';
            } else if ($this->Data['ActionForm']['mode'] === 'login') {
                $this->Data['template_file'] = 'auth/LoginForm.php';
            }
        }

        $this->Data['canLoginByLinkedIn'] = $this->canLoginByLinkedIn();

        if ($this->Data['cp']->isNonIncentiveCp()) {
            if ($this->Data['cp_action']->isLegalOpeningCpAction()) {
                return 'user/brandco/campaigns/non_incentive_campaigns.php';
            }
        }

        if ($this->Data['cp']->join_limit_sns_flg == Cp::JOIN_LIMIT_SNS_ON) {
            return 'user/brandco/campaigns/sns_limited_campaigns.php';
        } else {
            return 'user/brandco/campaigns/sns_unlimited_campaigns.php';
        }
    }
}
