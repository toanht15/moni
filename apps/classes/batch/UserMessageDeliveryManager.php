<?php

AAFW::import('jp.aainc.aafw.web.aafwController');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');

require_once dirname(__FILE__) . '/../../config/define.php';

/**
 * 管理画面で送信予約されたメッセージを実際にユーザーに送信します。
 *
 * <p>
 *  クライアントの管理画面から複数ユーザーにメッセージを送信するときの実行基盤です。
 *  このバッチでは以下の処理を担います。
 * </p>
 * <ul>
 *  <li>BRANDCo上のメッセージの送信</li>
 *  <li>e-mailの送信予約</li>
 *  <li>クーポンの配布</li>
 * </ul>
 *
 * Class UserMessageDeliveryManager
 */
class UserMessageDeliveryManager {

    const LOG_LIMIT_COUNT = 10000;

    private $logger;

    /** @var $service_factory aafwServiceFactory */
    private $service_factory;

    /** @var  CpUserService $cp_user_service */
    private $cp_user_service;

    /** @var  CpMessageDeliveryService $delivery_service */
    private $delivery_service;

    /** @var  BrandsUsersRelationService $relation_service */
    private $relation_service;

    /** @var CpTransactionService $transaction_service */
    private $transaction_service;

    private $core;

    private $enableHipchat;

    /** @var BrandGlobalSettingService $brand_global_setting_service */
    private  $brand_global_setting_service;

    public function __construct() {
        ini_set('memory_limit', '1024M');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->cp_user_service = $this->service_factory->create('CpUserService');
        $this->delivery_service = $this->service_factory->create('CpMessageDeliveryService');
        $this->relation_service = $this->service_factory->create('BrandsUsersRelationService');
        $this->transaction_service = $this->service_factory->create("CpTransactionService");
        $this->brand_global_setting_service = $this->service_factory->create('BrandGlobalSettingService');
        $this->enableHipchat = true;
    }

    public function doProcess() {
        $core = $this->core;
        if ($core === null) {
            $core = \Monipla\Core\MoniplaCore::getInstance();
        }
        $reservations = $this->delivery_service->getTargetCpMessageDeliveryReservation();
        foreach ($reservations as $reservation) {
            // caching
            $rsv_id = $reservation->id;
            $is_send_mail = $reservation->isSendMail();

            $this->logger->info('start message delivery. reservation_id = ' . $rsv_id);

            $reservation->status = CpMessageDeliveryReservation::STATUS_DELIVERING;
            $this->delivery_service->updateCpMessageDeliveryReservation($reservation);

            try {
                /**
                 * @var CpAction $cp_action
                 * @var CpAction $concrete_action
                 * @var $cp Cp
                 */
                list($cp_action, $concrete_action, $cp) = $this->cp_user_service->getCpAndActionsByCpActionId($reservation->cp_action_id);
                $cpCacheParams = $this->createCpCacheParams($cp);

                /** @var Brand $brand */
                $brand = $cp->getBrand();

                if($this->isSendMessageTextMail($brand, $cp_action, $concrete_action)) {
                    $cpActionCacheParams = $this->createCpActionCacheParams($cp_action, $concrete_action);
                } else {
                    $cpActionCacheParams = $this->createCpActionCacheParams($cp_action);
                }


                $subject = $concrete_action->title;

                if (!$cp->canSendMessage()) {
                    $reservation->status = CpMessageDeliveryReservation::STATUS_DELIVERY_FAIL;
                    $this->delivery_service->updateCpMessageDeliveryReservation($reservation);

                    $this->logger->info('reservation can not send message. reservation_id = ' . $rsv_id);

                    continue;
                }

                // caching
                $brandCacheParams = $this->createBrandCacheParams($brand);

                $context = new UserMessageDeliveryManager_DeliveryContext(
                    $this->service_factory,
                    $this->cp_user_service,
                    $brand
                );
                $targets = $context->selectTargets($rsv_id, $brandCacheParams['id']);
                $targets_count = $context->countTargets($rsv_id, $brandCacheParams['id']);

                if($targets && $targets_count > self::LOG_LIMIT_COUNT) {
                    $time = time();
                    if ($this->enableHipchat) {
                        aafwLog4phpLogger::getHipChatLogger()->info('UserMessageDeliveryManager reservation_id = ' . $rsv_id . ' Start, Count : ' . $targets_count);
                    }
                }

                $txCxt = new UserMessageDeliveryManager_TxContext($targets_count);
                while ($target = $context->fetch($targets)) {
                    $user = $core->getUserByQuery(array(
                        'class' => 'Thrift_UserQuery',
                        'fields' => array(
                            'socialMediaType' => 'Platform',
                            'socialMediaAccountID' => $target['monipla_user_id'],
                    )));

                    if ($user->result->status === Thrift_APIStatus::SUCCESS) {
                        $target['mail_address'] = $user->mailAddress;
                        $txCxt->goNext($target);
                    } else {
                        $this->logger->warn("The user has not been a monipla user!: user_id=" . $target['user_id']);
                        $txCxt->decrementTargetsCount();
                    }

                    if (!$txCxt->canProcess()) {
                        continue;
                    }

                    try {
                        if (count($txCxt->target_range) > 0) {
                            $context->begin();
                            $context->insertCpUsers($txCxt->target_range, $cpCacheParams['id'], $rsv_id);

                            $cp_user_ids = $context->selectCpUserIds($txCxt->target_range,$cpCacheParams['id']);

                            // 以下の3つはcp_user_idを利用。
                            $targetCpUsers = "";
                            foreach ($cp_user_ids as $tgt) {
                                $targetCpUsers .= $tgt['id'] . ",";
                            }
                            $targetCpUsers = substr($targetCpUsers, 0, strlen($targetCpUsers) - 1);

                            $context->insertMessages($targetCpUsers, $cpActionCacheParams['id'] , $subject);
                            $context->insertCoupons($cp_user_ids, $cp_action, $concrete_action, $cpActionCacheParams['cp_action_type'] ,$brandCacheParams['name'], $cpCacheParams['id']);
                            $context->insertStatuses($targetCpUsers, $cpActionCacheParams['id']);

                            if ($is_send_mail) {
                                $context->insertMailQueues($txCxt->target_range, $cpCacheParams,$brandCacheParams,$cpActionCacheParams,$subject, $rsv_id);
                            }

                            $context->updateDelivTargets($txCxt->target_range);
                            $context->updateRedisCache($txCxt->target_range, $cpActionCacheParams['id'], $brandCacheParams['id']);

                            $context->commit();
                        }
                    } catch(Exception $ex) {
                        // ロールバックに失敗する状況でエラー情報を記録できるとは思えないのでそのまま。
                        $context->rollback();
                        $this->logger->error('message delivery failed. ' . $ex . " targets = " . $targets);
                        $context->recordFailures($txCxt->target_range);
                    }
                    $txCxt->clearState();
                }

                if($targets && $targets_count > self::LOG_LIMIT_COUNT) {
                    if ($this->enableHipchat) {
                        aafwLog4phpLogger::getHipChatLogger()->info('UserMessageDeliveryManager reservation_id = ' . $rsv_id . ' End, Time : ' . (time() - $time));
                    }
                }

                /** @var CpFlowService $cp_flow_service */
                $cp_flow_service = $this->service_factory->create('CpFlowService');

                //メッセージの場合で公開前の場合は公開済みにする
                if($cp->type == Cp::TYPE_MESSAGE && $cp->status == Cp::STATUS_SCHEDULE) {
                    $cp->status = Cp::STATUS_FIX;
                    $cp_flow_service->updateCp($cp);
                }

                // 配信終了
                $reservation->status = CpMessageDeliveryReservation::STATUS_DELIVERED;
                $reservation->delivered_at = date("Y-m-d H:i:s");
                $this->delivery_service->updateCpMessageDeliveryReservation($reservation);

                //送信履歴を書き直す

                $cp_flow_service->setDeliveryHistoryCacheByCpActionId($cp_action->id);

            } catch (Exception $e) {
                // 配信失敗
                $reservation->status = CpMessageDeliveryReservation::STATUS_DELIVERY_FAIL;
                $this->delivery_service->updateCpMessageDeliveryReservation($reservation);
                $this->logger->error('UserMessageDeliveryManager Error.' . $e);
            }

            $this->logger->info('end message delivery. reservation_id = ' . $rsv_id);
        }
    }

    public function sendNow($tgt, $cp, $brand, $cp_action, $subject){
        $context = new UserMessageDeliveryManager_DeliveryContext(
            $this->service_factory,
            $this->cp_user_service,
            $brand
        );

        $context->sendNow($tgt,$this->createCpCacheParams($cp), $this->createBrandCacheParams($brand), $this->createCpActionCacheParams($cp_action), $subject);
    }

    /**
     * @param Cp $cp
     * @return array
     */
    public function createCpCacheParams($cp){
        $cpCacheParams = array();
        $cpCacheParams['id'] = $cp->id;
        $cpCacheParams['cp_icon'] = $cp->getIcon();
        $cpCacheParams['type'] = $cp->type;
        list($cpCacheParams['title'], $cpCacheParams['lp_image_url']) = $cp->getTitleAndLpImageUrl();

        return $cpCacheParams;
    }

    /**
     * @param Brand $brand
     * @return array
     */
    public function createBrandCacheParams($brand){
        $brandCacheParams = array();
        $brandCacheParams['id'] = $brand->id;
        $brandCacheParams['name'] = $brand->name;
        $brandCacheParams['mail_name'] = $brand->mail_name;
        $brandCacheParams['base_url'] = $this->getBaseUrl($brand, true);
        $brandCacheParams['brand_img'] = $brand->getProfileImage();
        $brandCacheParams['is_plan'] = $brand->isPlan(BrandContract::PLAN_PROMOTION_MONIPLA);
        $brandCacheParams['from_address'] = $this->getFromAddressByBrand($brand);
        return $brandCacheParams;
    }


    /**
     * @param $cp_action
     * @return array
     */
    public function createCpActionCacheParams($cp_action, $concrete_action = null){

        $cp_actions = $this->cp_user_service->getCpActionsByCpActionGroupId($cp_action->cp_action_group_id);

        $cpActionCacheParams = array();
        $cpActionCacheParams['id'] = $cp_action->id;
        $cpActionCacheParams['cp_action_type'] = $cp_action->type;
        $cpActionCacheParams['is_last_action_in_group'] = $cp_actions->total() === 1;
        $cpActionCacheParams['is_not_announce_action']  = $cp_action->type  !== CpAction::TYPE_ANNOUNCE;

        if($concrete_action) {
            $cpActionCacheParams['cp_action_text'] = $concrete_action->html_content;
        }

        return $cpActionCacheParams;
    }

    private function getBaseUrl($brand, $secure = false) {
        return Util::createBaseUrl($brand, $secure);
    }

    private function getFromAddressByBrand($brand) {
        $can_set_mail_from_address = $this->brand_global_setting_service->getBrandGlobalSetting($brand->id, BrandGlobalSettingService::CAN_SET_MAIL_FROM_ADDRESS);

        if(Util::isNullOrEmpty($can_set_mail_from_address) || $can_set_mail_from_address->content == '') {
            return null;
        }

        return $can_set_mail_from_address->content;
    }

    private function isSendMessageTextMail($brand, $cp_action, $concrete_action) {
        $can_set_crm_text_mail = $this->brand_global_setting_service->getBrandGlobalSetting($brand->id, BrandGlobalSettingService::CAN_SET_CRM_TEXT_MAIL);

        if(Util::isNullOrEmpty($can_set_crm_text_mail)) {
            return false;
        }

        if($cp_action->type = CpAction::TYPE_MESSAGE && $concrete_action->send_text_mail_flg) {
            return true;
        }

        return false;
    }

    public function setCore($core) {
        $this->core = $core;
    }

    public function setEnableHipChat($enableHipChat) {
        $this->enableHipchat = $enableHipChat;
    }
}

// JavaでいうところのInner Class的な扱い。
// トランザクション処理と、DBアクセスからサービスを守ります。
class UserMessageDeliveryManager_DeliveryContext {

    const MAIL_TEMPLATE_PREPEND = '<p><img src="';
    const MAIL_TEMPLATE_FACEBOOK = '/img/sns/iconSnsFB4.png" width="15" height="15" alt="Facebook" style="vertical-align:middle; padding-right:5px;"/>';
    const MAIL_TEMPLATE_TWITTER = '/img/sns/iconSnsTW4.png" width="15" height="15" alt="Twitter" style="vertical-align:middle; padding-right:5px;"/>';
    const MAIL_TEMPLATE_YAHOO = '/img/sns/iconSnsYH4.png" width="15" height="15" alt="Yahoo!" style="vertical-align:middle; padding-right:5px;"/>';
    const MAIL_TEMPLATE_GOOGLE = '/img/sns/iconSnsGP4.png" width="15" height="15" alt="Google+" style="vertical-align:middle; padding-right:5px;"/>';
    const MAIL_TEMPLATE_GDO = '/img/thirdParty/iconGdo2.png" width="15" height="15" alt="GDO" style="vertical-align:middle; padding-right:5px;"/>';
    const MAIL_TEMPLATE_INSTAGRAM = '/img/sns/iconSnsIG4.png" width="15" height="15" alt="Instagram" style="vertical-align:middle; padding-right:5px;"/>';
    const MAIL_TEMPLATE_LINE = '/img/sns/iconSnsLN2.png" width="15" height="15" alt="LINE" style="vertical-align:middle; padding-right:5px;"/>';
    const MAIL_TEMPLATE_SUBSEQUENT = '</p>';

    const TITLE_MESSAGE_BODY_TEMPLATE = 'message_notification_mail';
    const CONTENT_MESSAGE_BODY_TEMPLATE = 'message_notification_mail_show_content';

    const MAIL_TEMPLATE_SOCIAL_ACCOUNT_PREPEND =
        '<tr>
            <td style="line-height:15px; text-align: left;">';

    const MAIL_TEMPLATE_SOCIAL_ACCOUNT_SUBSEQUENT = '</td></tr>';

    /** @var $service_factory aafwServiceFactory */
    private $service_factory;

    /** @var  CpUserService $cp_user_service */
    private $cp_user_service;

    /** @var  SocialAccountService $social_account_service */
    private $social_account_service;

    private $db;
    private $stores;
    private $cache_manager;
    private $mail_manager;

    private $empty_arg = array();

    private $static_url;

    /** @var CpTransactionService $transaction_service */
    private $coupon_action_manager;

    private $moniplaCore;

    private $cp_user_action_messages;

    public function __construct($service_factory, $cp_user_service, $brand) {
        $this->service_factory = $service_factory;
        $this->cp_user_service = $cp_user_service;

        $this->db = aafwDataBuilder::newBuilder(); // 必ずマスタにつなぐこと!

        $this->cache_manager = new CacheManager();

        $this->transaction_service = $this->service_factory->create('CpTransactionService');
        $this->social_account_service = $this->service_factory->create("SocialAccountService");

        $this->stores = aafwEntityStoreFactory::create('CpMessageDeliveryReservations');

        $this->mail_manager = new MailManager(array('BccSend' => false), true);

        $this->static_url = "https:" . config('Static.Url');

        $this->coupon_action_manager = new CpCouponActionManager();

        $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();

        $this->cp_user_action_messages = aafwEntityStoreFactory::create('CpUserActionMessages');
    }

    public function selectTargets($rsv_id, $brand_id) {
        $selectTargets = "/* UserMessageDeliveryManager_DeliveryContext->selectTargets */
                        SELECT t.id, t.cp_message_delivery_reservation_id, t.user_id, t.cp_action_id, t.status, u.name, u.profile_image_url, r.optin_flg, u.monipla_user_id
                        FROM cp_message_delivery_targets t
                            INNER JOIN users u ON t.user_id = u.id INNER JOIN brands_users_relations r ON u.id = r.user_id
                            WHERE
                                t.cp_message_delivery_reservation_id = " . $rsv_id . " AND t.del_flg = 0 AND t.status = 0 AND
                                u.del_flg = 0 AND
                                r.brand_id = " . $brand_id . " AND r.withdraw_flg = 0 AND r.del_flg = 0";

        return $this->db->getBySQL($selectTargets, array('__NOFETCH__'));
    }

    public function countTargets($rsv_id, $brand_id) {
        $countTargets = "/* UserMessageDeliveryManager_DeliveryContext->countTargets */
                        SELECT COUNT(*)
                        FROM cp_message_delivery_targets t
                            INNER JOIN users u ON t.user_id = u.id INNER JOIN brands_users_relations r ON u.id = r.user_id
                            WHERE
                                t.cp_message_delivery_reservation_id = " . $rsv_id . " AND t.del_flg = 0 AND t.status = 0 AND
                                u.del_flg = 0 AND
                                r.brand_id = " . $brand_id . " AND r.withdraw_flg = 0 AND r.del_flg = 0";

        $value = $this->db->getBySQL($countTargets, $this->empty_arg);
        $actualCount = (int) $value[0]['COUNT(*)'];

        return $actualCount;
    }

    public function insertCpUsers($target_range, $cp_id, $resev_id) {
        $inserIntoCpUsers =
            "/* UserMessageDeliveryManager_DeliveryContext->insertCpUsers */ INSERT INTO cp_users(cp_id, user_id, demography_flg, updated_at, created_at)
                SELECT " . $cp_id . ", user_id, 0, NOW(), NOW() FROM cp_message_delivery_targets T
                WHERE T.cp_message_delivery_reservation_id = " . $resev_id .
            " AND T.del_flg = 0 AND NOT EXISTS(SELECT 1 FROM cp_users WHERE T.user_id = user_id AND cp_id = " . $cp_id . " AND del_flg = 0) AND T.user_id IN(";
        foreach ($target_range as $tgt) {
            $inserIntoCpUsers .= $tgt['user_id'] . ",";
        }
        $inserIntoCpUsers = substr($inserIntoCpUsers, 0, strlen($inserIntoCpUsers) - 1);
        $inserIntoCpUsers .= ")";

        $this->command($inserIntoCpUsers);
    }

    public function selectCpUserIds($target_range, $cp_id) {
        $selectCpUserIds = "/* UserMessageDeliveryManager_DeliveryContext->selectCpUserIds */ SELECT id, user_id FROM cp_users WHERE cp_id=" . $cp_id . " AND user_id IN(";
        foreach ($target_range as $tgt) {
            $selectCpUserIds .= $tgt["user_id"] . ",";
        }
        $selectCpUserIds = substr($selectCpUserIds, 0, strlen($selectCpUserIds) - 1);
        $selectCpUserIds .= ")";

        return $this->db->getBySQL($selectCpUserIds, $this->empty_arg);
    }

    public function insertMessages($targetCpUsers, $cp_action_id, $concrete_action_title) {
        $insertIntoUsrActMsgs =
            "/* UserMessageDeliveryManager_DeliveryContext->insertMessages */ INSERT INTO cp_user_action_messages(cp_user_id, cp_action_id, title, created_at, updated_at)
                SELECT CU.id, " . $cp_action_id . ", '" . $this->escapeForSQL($concrete_action_title) . "',NOW(), NOW() FROM cp_users CU
                WHERE CU.id IN (" . $targetCpUsers . ") AND CU.del_flg = 0
                AND NOT EXISTS ( SELECT 1 FROM cp_user_action_messages M
                                WHERE M.cp_user_id = CU.id AND M.cp_action_id = " . $cp_action_id ." AND M.del_flg = 0)";
        $this->command($insertIntoUsrActMsgs);
    }

    public function insertCoupons($cp_user_ids, $cp_action, $concrete_action, $cp_action_type, $brand_name, $cp_id) {
        // クーポンは使用頻度が高くないので、最適化せずにそのままとする。
        if ($cp_action_type === CpAction::TYPE_COUPON) {
            $msg = "アクション・グループの先頭にクーポンが設定されています!!! : brand_name=" . $brand_name . ", cp_id=" . $cp_id;
//            aafwLog4phpLogger::getHipChatLogger()->warn($msg);
            aafwLog4phpLogger::getDefaultLogger()->warn($msg);
//            foreach ($cp_user_ids as $tgt) {
//                // 二重配布の危険性を回避するため
//                if($this->coupon_action_manager->getCouponCodeUser($tgt['user_id'], $cp_action->id)) {
//                    continue;
//                };
//                $this->cp_user_service->distributeCoupon($tgt['id'], $cp_action, $concrete_action, $this->service_factory);
//            }
        }
    }

    public function insertStatuses($targetCpUsers, $cp_action_id) {
        $insertIntoUsrActStses =
            "/* UserMessageDeliveryManager_DeliveryContext->insertStatuses */ INSERT INTO cp_user_action_statuses(cp_user_id, cp_action_id, created_at, updated_at)
                SELECT CU.id, " . $cp_action_id . ",NOW(), NOW() FROM cp_users CU
                WHERE CU.id IN (" . $targetCpUsers . ") AND CU.del_flg = 0
                AND NOT EXISTS ( SELECT 1 FROM cp_user_action_statuses S
                                WHERE S.cp_user_id = CU.id AND S.cp_action_id = " . $cp_action_id ." AND S.del_flg = 0)";
        $this->command($insertIntoUsrActStses);
    }

    public function insertMailQueues($target_range, $cpCacheParams,$brandCacheParams , $cpActionCacheInfo,$subject, $rsv_id) {
        $insertIntoMailQueues =
            "/* UserMessageDeliveryManager_DeliveryContext->insertMailQueues */ INSERT IGNORE INTO mail_queues(send_schedule, to_address, cc_address,
              bcc_address, subject, body_plain, body_html, from_address, envelope, user_id, cp_message_delivery_reservation_id, created_at, updated_at) VALUES";

        $sendCount = 0;
        $mailSendUserIds = array();
        foreach ($target_range as $tgt) {
            $mailSendUserIds[] = $tgt['user_id'];
            if( $tgt['optin_flg'] === CpMessageDeliveryReservation::SEND_MAIL_OFF && $cpActionCacheInfo['is_not_announce_action'] ) {
                continue;
            }

            if( !$tgt['mail_address'] ) {
                continue;
            }

            $sendCount++;

            $mailParams = $this->createMailManagerParams($tgt,$cpCacheParams,$brandCacheParams,$cpActionCacheInfo,$subject);

            $insertIntoMailQueues .= "(" . "'1970-01-01 00:00:00','" . $mailParams['ToAddress'] . "', '','','" .
                $this->escapeForSQL(
                    $mailParams['Subject']
                ) . "','" . $mailParams['BodyPlain'] . "','" . $mailParams['BodyHTML'] . "','" . $mailParams['FromAddress'] . "','"
                . $this->mail_manager->Envelope . "','" . $tgt['user_id'] . "','" . $rsv_id . "',NOW(),NOW()),";
        }

        if( $sendCount == 0 ) {
            // 送信対象がいないならば、そもそも実行しない。
            return;
        }
        $insertIntoMailQueues = substr($insertIntoMailQueues, 0, strlen($insertIntoMailQueues) - 1);

        $this->command($insertIntoMailQueues);
    }

    /**
     * @param $tgt array('user_id','mail_address','name','profile_image_url')
     * @param $cpCacheParams
     * @param $brandCacheParams
     * @param $subject
     */
    public function sendNow($tgt, $cpCacheParams, $brandCacheParams, $cpActionCacheParams, $subject){
        $mailParams = $this->createMailManagerParams($tgt,$cpCacheParams,$brandCacheParams,$cpActionCacheParams,$subject);
        $mailManager = new MailManager($mailParams, true);
        $mailManager->sendNow();
        aafwLog4phpLogger::getDefaultLogger()->info("sendNowMail : ".json_encode($mailParams,JSON_PRETTY_PRINT));
    }

    /**
     * @param $tgt
     * @param $cpCacheParams
     * @param $brandCacheParams
     * @param $cpActionCacheParams
     * @param $subject
     * @return MailManager
     * @throws aafwException
     */
    public function createMailManagerParams($tgt,$cpCacheParams,$brandCacheParams,$cpActionCacheParams,$subject){

        $isLoadTextMail = $cpActionCacheParams['cp_action_text'] ? true : false;

        $this->loadMailContentTemplate($isLoadTextMail);

        $params = $this->fillBaseParams($tgt,$cpCacheParams,$brandCacheParams,$subject,$cpActionCacheParams['cp_action_text']);
        $mailParams = array();

        $account_html = $this->buildAccountHtml($tgt['user_id']);

        if ($account_html) {
            $params['<#SOCIAL_ACCOUNTS_TITLE_TAG>'] = '<td bgcolor="#EEEEEE" style="background:#EEE; text-align: left;">以下のアカウントと連携しています</td>';
            $params['<#SOCIAL_ACCOUNTS_TAG>'] = self::MAIL_TEMPLATE_SOCIAL_ACCOUNT_PREPEND . $account_html . self::MAIL_TEMPLATE_SOCIAL_ACCOUNT_SUBSEQUENT;
        }

        if (!$cpActionCacheParams['is_last_action_in_group']) {
            $params['<#NOTIFICATION_OTHER_CONTENTS_TAG>'] =
                '<tr>
                     <td style="text-align: center; vertical-align: top;"><img src="' . $params['<#STATIC_URL>'] . '/img/mail/messagePush/text_attention.jpg" width="330" height="24" alt="メッセージの他にコンテンツがあります！ご確認ください" style="border: none;" /></td>
                </tr>';
        }

        if ($brandCacheParams['is_plan']) {
            $mailParams['FromAddress']  = "モニプラ" . "<" . config("Mail.Default.Envelope") . ">";
        } else {
            $mailParams['FromAddress'] = $brandCacheParams['from_address'] ?: $brandCacheParams['mail_name'] . "<" . config("Mail.Default.Envelope") . ">";
        }
        $mailParams['BodyPlain']  = $this->escapeMailBody(Util::applyParameter($this->mail_manager->BodyPlain, $params));
        $mailParams['BodyHTML'] = $this->escapeMailBody(Util::applyParameter($this->mail_manager->BodyHTML, $params));
        $mailParams['ToAddress'] = $tgt['mail_address'];
        $mailParams['Subject'] = $subject;
        $mailParams['BccSend'] = false;

        $isInvalid = !$this->mail_manager->Charset || !$mailParams['ToAddress']  || !$mailParams['FromAddress']  || (!$mailParams['BodyPlain'] && !$mailParams['BodyHTML']);
        if ($isInvalid) {
            // 主に設定ファイルの不備の検知用
            throw new aafwException ("can't send mail");
        }

        return $mailParams;
    }

    public function updateDelivTargets($target_range) {
        $updateDelivTargets = "/* UserMessageDeliveryManager_DeliveryContext->updateDelivTargets */ UPDATE cp_message_delivery_targets SET status=1, updated_at=NOW() WHERE id IN(";
        foreach ($target_range as $tgt) {
            $updateDelivTargets .= $tgt['id'] . ",";
        }
        $updateDelivTargets = substr($updateDelivTargets, 0, strlen($updateDelivTargets) - 1);
        $updateDelivTargets .= ") AND del_flg = 0";

        $this->command($updateDelivTargets);
    }

    public function updateRedisCache($target_range, $cp_action_id, $brand_id) {
        try {
            $this->cache_manager->beginBatch();

            $this->cache_manager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_SEND_MESSAGE, $cp_action_id));
            $this->cache_manager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE, $cp_action_id));
            $this->cache_manager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_NEW_BR_USER, $cp_action_id));
            $this->cache_manager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_PC, $cp_action_id));
            $this->cache_manager->deleteCache("cp_action_member_count",
                array(CpUserService::CACHE_TYPE_READ_PAGE_SP, $cp_action_id));

            foreach ($target_range as $tgt) {
                $this->cache_manager->resetNotificationCount($brand_id, $tgt['user_id']);
            }

            $this->cache_manager->flushBatch();
        } catch(Exception $e) {
            log_error($e);
            $this->cache_manager->resetBatch();
        }
    }

    /**
     * @param $tgt
     * @param $cpCacheParams
     * @param $brandCacheParams
     * @param $subject
     * @return array
     */
    public function fillBaseParams($tgt,$cpCacheParams,$brandCacheParams,$subject,$mail_text_content = null){

        $cp_link_parameter = base64_encode(json_encode(array('cp_action_id' => $tgt['cp_action_id'], 'user_id' => $tgt['user_id'])));
        $optin_status_link_parameter = base64_encode(json_encode(array('brand_id' => $brandCacheParams['id'], 'user_id' => $tgt['user_id'])));
        $params = array(
            '<#STATIC_URL>' => $this->static_url,
            '<#USER_NAME>' => $tgt['name'],
            '<#USER_MAIL>' => $tgt['mail_address'],
            '<#USER_IMAGE>' => $tgt['profile_image_url'] ?: $this->static_url . '/img/mail/messagePush/imgUser1.jpg',
            '<#BRAND_NAME>' => $brandCacheParams['name'],
            '<#BRAND_URL>' => Util::rewriteUrl('', '', '', '', $brandCacheParams['base_url']),
            '<#THREAD_URL>' => Util::rewriteUrl('admin-cp', 'api_click_link', array(), array('params' => $cp_link_parameter), $brandCacheParams['base_url']),
            '<#OPTOUT_URL>' => Util::rewriteUrl('my', 'optin_status', array(), array('params' => $optin_status_link_parameter), $brandCacheParams['base_url']),
            '<#BRAND_LOGO_URL>' => $brandCacheParams['brand_img'],
            '<#BRAND_INQUIRY_URL>' => $brandCacheParams['base_url'] . 'inquiry',
            '<#CAMPAIGN_IMAGE_TAG>' => strlen($cpCacheParams['lp_image_url']) === 0 ? '' :
                '<tr><td width="398"><img src="' . $cpCacheParams['lp_image_url'] .'" alt="' . $cpCacheParams['title'] . '" width="398" /></td></tr>',
            '<#CAMPAIGN_TITLE_TAG>' => $cpCacheParams['type'] == cp::TYPE_MESSAGE ? '' :
                '<tr><td style="vertical-align: middle; padding: 5px 0;text-align: left; font-size:13px;">' . $cpCacheParams['title'] . '</td></tr>',
            '<#MESSAGE_TITLE>' => $subject,
            '<#NOTIFICATION_OTHER_CONTENTS_TAG>' => '',
            '<#SOCIAL_ACCOUNTS_TITLE_TAG>' => '',
            '<#SOCIAL_ACCOUNTS_TAG>' => '',
            '<#PIXEL_TAG>' => '<img src="https://'.config('Domain.brandco_tracker').'/open_email_tracker?params='.$cp_link_parameter.'" width="1" height="1"/>'
        );

        if($mail_text_content) {
            $params['<#MESSAGE_CONTENT>'] = $mail_text_content;
        }

        return $params;
    }

    public function buildAccountHtml($user_id){
        $account_html = '';
        $social_accounts = $this->social_account_service->getSocialAccountsByUserIdOrderBySocialMediaAccountId($user_id);
        foreach ($social_accounts as $social_account) {
            $account_html .= self::MAIL_TEMPLATE_PREPEND . $this->static_url;
            switch ($social_account->social_media_id) {
                case SocialAccountService::SOCIAL_MEDIA_FACEBOOK:
                    $account_html .= self::MAIL_TEMPLATE_FACEBOOK;
                    break;
                case SocialAccountService::SOCIAL_MEDIA_TWITTER:
                    $account_html .= self::MAIL_TEMPLATE_TWITTER;
                    break;
                case SocialAccountService::SOCIAL_MEDIA_YAHOO:
                    $account_html .= self::MAIL_TEMPLATE_YAHOO;
                    break;
                case SocialAccountService::SOCIAL_MEDIA_GOOGLE:
                    $account_html .= self::MAIL_TEMPLATE_GOOGLE;
                    break;
                case SocialAccountService::SOCIAL_MEDIA_GDO:
                    $account_html .= self::MAIL_TEMPLATE_GDO;
                    break;
                case SocialAccountService::SOCIAL_MEDIA_INSTAGRAM:
                    $account_html .= self::MAIL_TEMPLATE_INSTAGRAM;
                    break;
                case SocialAccountService::SOCIAL_MEDIA_LINE:
                    $account_html .= self::MAIL_TEMPLATE_LINE;
                    break;
            }
            $account_html .= $social_account->name . self::MAIL_TEMPLATE_SUBSEQUENT;
        }
        return $account_html;
    }

    public function recordFailures($target_range) {
        // エラーの記録は、正常系のパスではほとんど発生しないことと、安全性重視のため、
        // レコード毎に別トランザクションで実行します。
        foreach ($target_range as $tgt) {
            try {
                $this->begin();
                $updateTargets = "/* UserMessageDeliveryManager_DeliveryContext->recordFailures */ UPDATE cp_message_delivery_targets SET status=2 WHERE id=" . $tgt['id'];
                $this->command($updateTargets);
                $this->commit();
            } catch(Exception $e) {
                $this->rollback();
                throw $e;
            }
        }
    }

    public function begin() {
        $this->stores->begin();
    }

    public function commit() {
        $this->stores->commit();
    }

    public function rollback() {
        $this->stores->rollback();
    }

    public function fetch($rs) {
        return $this->db->fetch($rs);
    }

    public function command($command) {
        $result = $this->db->executeUpdate($command);
        if (!$result) {
            throw new Exception("Command execution failed! : " . $command);
        }
    }

    private function escapeMailBody($sql_value) {
        return str_replace ( "'", "''", $sql_value);
    }

    private function escapeForSQL($sql_value) {
        return $this->cp_user_action_messages->escapeForSQL($sql_value);
    }

    private function loadMailContentTemplate($isLoadTextMail) {

        if($isLoadTextMail) {
            $this->mail_manager->loadMailContent(self::CONTENT_MESSAGE_BODY_TEMPLATE);
        } else {
            $this->mail_manager->loadMailContent(self::TITLE_MESSAGE_BODY_TEMPLATE);
        }

    }

}

// JavaのInner Class的な扱い。
// トランザクションのステート管理の責務を担う。
class UserMessageDeliveryManager_TxContext {

    const LIMIT = 100;
    public $count = 0;
    public $total = 0;
    public $targets_count;
    public $target_range = array();

    public function __construct($targets_count) {
        $this->targets_count = $targets_count;
    }

    public function decrementTargetsCount() {
        $this->targets_count --;
    }

    public function goNext($target) {
        $this->target_range[] = $target;
        $this->total ++;
        $this->count ++;
    }

    public function canProcess() {
        return $this->count == self::LIMIT || $this->total == $this->targets_count;
    }

    public function clearState() {
        $this->count = 0;
        $this->target_range = array();
    }
}
