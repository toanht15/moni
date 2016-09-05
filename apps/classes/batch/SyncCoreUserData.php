<?php

AAFW::import("jp.aainc.aafw.web.aafwController");
AAFW::import('jp.aainc.classes.core.UserAttributeManager');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import('jp.aainc.classes.services.SocialAccountService');
require_once dirname(__FILE__) . '/../../config/define.php';

/**
 * 数十万件分の処理を実行するオーバーヘッドを考えて、
 * 関数、余計なO/R Mapperなどを廃止し、
 * Stored Procedure的な集合単位の演算のみを実施します。
 */
class SyncCoreUserData {

    public function doProcess($argv) {
        if (count($argv) !== 2) {
            $msg = "Partitioning no must be specified!";
            aafwLog4phpLogger::getHipChatLogger()->error($msg);
            throw new aafwException($msg);
        }
        $partitioning_no = (int) $argv[1];

        $start = time();
        ini_set('memory_limit', '256M');

        $logger = aafwLog4phpLogger::getDefaultLogger();
        $logger->info("start SyncCoreUserData");

        $hipchatLogger = aafwLog4phpLogger::getHipChatLogger();
        $dataBuilder = aafwDataBuilder::newBuilder();

        $factory = new aafwEntityStoreFactory();
        $store = $factory->create("Users");

        $moniplaCore = \Monipla\Core\MoniplaCore::getInstance();

        $value = $dataBuilder->getBySQL("/* SyncCoreUserData SELECT1 */ SELECT MAX(id) FROM users", array()); // フルスキャンを避ける
        $maxId = (int) $value[0]['MAX(id)'];
        if ($maxId === 0) {
            $logger->warn("There are no data in the users table!");
            return;
        }

        $partitioning_factor = (int) config("SyncCoreUserDataPartitioningFactor");
        $start_id = (int) ($maxId * ($partitioning_no - 1) / $partitioning_factor) + 1;
        $end_id = (int) ($maxId * $partitioning_no / $partitioning_factor);
        $logger->info("starting SyncCoreUserData... : start id ={$start_id}, end id={$end_id}");

        /**
         * マスターのIdの仕様が変わったため、内部表現にマッピングします。
         * なお、書き込み及び検索時は読み替えを実施しているので問題はありません。
         */
        $userAttributeMasterIdMap = array('1' => '-1', '2' => '-2');

        /**
         * ユーザーの情報をCoreからBRANDCoに流し込みます。
         */
        $not_found_users = array();
        $error_count = 0;
        $emptyArray = array();
        for ($i = $start_id ; $i < $end_id ; $i += 100) {

            try {
                $maxRange = $i + 99;
                $users = $dataBuilder->getBySQL("/* SyncCoreUserData SELECT2 */ SELECT u.id, u.monipla_user_id FROM users u WHERE EXISTS(SELECT 1 FROM brands_users_relations r WHERE r.withdraw_flg = 0 AND r.user_id = u.id) AND u.del_flg = 0 AND u.id BETWEEN " . $i . " AND " . $maxRange, $emptyArray);

                $userIdToAttrValueMap = array();
                $successExists = false;
                $socialAccountExists = false;
                $userAttributeExists = false;
                $userSearchInfoExists = false;
                $socialAccountsQuery = "/* SyncCoreUserData INSERT1 */ INSERT INTO social_accounts(social_media_id, social_media_account_id, name, mail_address, profile_image_url, profile_page_url, user_id, validated, friend_count, del_flg) VALUES";
                $shippingAddressesQuery = "/* SyncCoreUserData INSERT2 */ INSERT INTO shipping_addresses(user_id, first_name, last_name, first_name_kana, last_name_kana, zip_code1,
                                                            zip_code2, pref_id, address1, address2, address3, tel_no1, tel_no2, tel_no3, del_flg, updated_at, created_at) VALUES";
                $userAttributesQuery = "/* SyncCoreUserData INSERT3 */ INSERT INTO user_attributes(user_id, user_attribute_master_id, value, del_flg) VALUES";
                $userSearchInfoQuery = "/* SyncCoreUserData INSERT4 */ INSERT INTO user_search_info(user_id, sex, birthday, del_flg) VALUES";
                $updateFromSocialAccounts = "/* SyncCoreUserData UPDATE1 */ UPDATE social_accounts SET del_flg = 1 WHERE user_id IN(";

                $user_id_array = array();
                foreach ($users as $user) {
                    $user_id = $user['id'];
                    $user_id_array[] = $user_id;
                    try {
                        /**
                         * Coreとの通信。
                         * RPCの信頼性がいまいちわからないので、いずれかの問い合わせで失敗した場合、
                         * トランザクションはロールバックせずにスキップします。
                         */
                        $userInfo = $moniplaCore->getUserByQuery(array(
                            'class' => 'Thrift_UserQuery',
                            'fields' => array(
                                'socialMediaType' => 'Platform',
                                'socialMediaAccountID' => $user['monipla_user_id'],
                            )));
                        if ($userInfo->result->status != Thrift_APIStatus::SUCCESS) {
                            $error_msg = $userInfo->result->errors[0]->message;
                            if ($error_msg == 'レコードが見付かりませんでした') {
                                $not_found_users[] = $user_id;
                            }
                            continue;
                        }
                        $getShippingAddressResult = $moniplaCore->getShippingAddress(array(
                            'class' => 'Thrift_Address',
                            'fields' => array('userId' => $userInfo->id)
                        ));
                        if ($getShippingAddressResult->result->status != Thrift_APIStatus::SUCCESS) {
                            $hipchatLogger->error("getShippingAddress failed!: user id=" . $user_id . ", " . $getShippingAddressResult->result->errors);
                            continue;
                        }

                        $userAttributesSocialAccount = $userInfo;
                        if (!$userInfo->class || !$userInfo->fields) {
                            $userAttributesSocialAccount = array(
                                'class' => 'Thrift_SocialAccount',
                                'fields' => array(
                                    'socialMediaType' => 'Platform',
                                    'socialMediaAccountID' => $userInfo->id,
                                    'name' => $userInfo->name,
                                ),
                            );
                        }
                        $getUserAttributesResult = $moniplaCore->getUserAttributes(array(
                                'class' => 'Thrift_UserAttributeQuery',
                                'fields' => array(
                                    'socialAccount' => $userAttributesSocialAccount)
                            )
                        );
                        if ($getUserAttributesResult->result->status != Thrift_APIStatus::SUCCESS) {
                            $hipchatLogger->error("getUserAttributes failed!: user id=" . $user_id . ", " . $getUserAttributesResult->result->errors);
                            continue;
                        }
                    } catch(Exception $e) {
                        $logger->warn("The Thrift communication has been failed!: " . $e);
                        continue;
                    }

                    /**
                     * Insert文の組み立て
                     */
                    foreach ($userInfo->socialAccounts as $socialAccount) {
                        if($socialAccount->socialMediaType === 'Facebook') {
                            $social_media_id = SocialAccountService::SOCIAL_MEDIA_FACEBOOK;
                        } elseif($socialAccount->socialMediaType === 'Twitter') {
                            $social_media_id = SocialAccountService::SOCIAL_MEDIA_TWITTER;
                        } elseif($socialAccount->socialMediaType === 'Google') {
                            $social_media_id = SocialAccountService::SOCIAL_MEDIA_GOOGLE;
                        } elseif($socialAccount->socialMediaType === 'Yahoo') {
                            $social_media_id = SocialAccountService::SOCIAL_MEDIA_YAHOO;
                        } elseif($socialAccount->socialMediaType === 'GDO') {
                            $social_media_id = SocialAccountService::SOCIAL_MEDIA_GDO;
                        } elseif($socialAccount->socialMediaType === 'LINE') {
                            $social_media_id = SocialAccountService::SOCIAL_MEDIA_LINE;
                        } elseif($socialAccount->socialMediaType === 'Instagram') {
                            $social_media_id = SocialAccountService::SOCIAL_MEDIA_INSTAGRAM;
                        } elseif($socialAccount->socialMediaType === 'LinkedIn') {
                            $social_media_id = SocialAccountService::SOCIAL_MEDIA_LINKEDIN;
                        } else {
                            continue;
                        }

                        $friend_count = $socialAccount->friendCount === "null" ? "null" : $socialAccount->friendCount;
                        $socialAccountsQuery .= "(" . $social_media_id . ", '" . $socialAccount->socialMediaAccountID . "', '" .$dataBuilder->escape($socialAccount->name) . "',
                                                    '" . $dataBuilder->escape($socialAccount->mailAddress) . "', '" . $socialAccount->profileImageUrl . "', '" . $socialAccount->profilePageUrl . "',
                                                    " . $user_id . ", " . $socialAccount->validated . "," . $friend_count . ", 0), ";
                        $socialAccountExists = true;
                    }

                    $shippingAddress = $getShippingAddressResult->address;
                    $shippingAddressesQuery .= "(" . $user_id . ", '" . $dataBuilder->escape($shippingAddress->firstName) . "', '" . $dataBuilder->escape($shippingAddress->lastName) . "', '" . $dataBuilder->escape($shippingAddress->firstNameKana) . "',
                                                    '" . $dataBuilder->escape($shippingAddress->lastNameKana) . "', '" . $shippingAddress->zipCode1 . "', '" . $shippingAddress->zipCode2 . "', '" . $shippingAddress->prefId . "',
                                                    '" . $dataBuilder->escape($shippingAddress->address1) . "', '" . $dataBuilder->escape($shippingAddress->address2) . "', '" . $dataBuilder->escape($shippingAddress->address3)
                        . "', '" . $shippingAddress->telNo1 . "', '" . $shippingAddress->telNo2 . "', '" . $shippingAddress->telNo3 . "', 0, NOW(), NOW()), ";

                    foreach($getUserAttributesResult->userAttributeList as $attr) {
                        $innerMasterId = $userAttributeMasterIdMap[$attr->masterId];
                        if ($attr->masterId > 2) {
                            // 性別=1, 生年月日=2以外のデータは受け付けない
                            continue;
                        }
                        $userAttributesQuery .= "(" . $user_id . "," . $innerMasterId . ", '" . $dataBuilder->escape($attr->value) . "',0), ";
                        if (!$userIdToAttrValueMap[$user_id]) {
                            $userIdToAttrValueMap[$user_id] = array();
                        }
                        $userIdToAttrValueMap[$user_id][$innerMasterId] = json_decode($attr->value)->value;

                        $userAttributeExists = true;
                    }

                    // どちらか片方の可能性があるので、2パス実行
                    if($userIdToAttrValueMap[$user_id][UserAttributeManager::SEX] !== null &&
                        $userIdToAttrValueMap[$user_id][UserAttributeManager::BIRTH_DAY] !== null) {
                        $sex = $userIdToAttrValueMap[$user_id][UserAttributeManager::SEX] ?: '';
                        $birthday = $userIdToAttrValueMap[$user_id][UserAttributeManager::BIRTH_DAY] ?: '';
                        $birthday = $birthday ? date('Y-m-d', strtotime($birthday)) : '';
                        // 両方に正しいデータが入っていなければ更新しない
                        // 1970-01-01に関しては、以前のAlliedID障害時に発生した不正データなので除外する
                        // 1970-01-01生まれでも登録時にそのように入力していたなら問題ない
                        if ($sex && ($birthday && $birthday != '0000-00-00' && $birthday != '1970-01-01')) {
                            $userSearchInfoQuery .= "(" . $user_id . ", '" . $sex . "', '" . $birthday . "',0), ";
                            $userSearchInfoExists = true;
                        }
                    }

                    $successExists = true;
                }

                /**
                 * Insert文の実行。
                 * Insertに失敗=コネクション喪失、ディスクサイズオーバー etc...の致命的なエラーだと思われるため、
                 * Insertの失敗でロールバックします。
                 */
                if (!$successExists) {
                    continue;
                }

                $store->begin();

                if (count($user_id_array) > 0) {
                    $updateFromSocialAccounts .= join(',', $user_id_array);
                    $updateFromSocialAccounts .= ')';
                    $updateResult = $dataBuilder->executeUpdate($updateFromSocialAccounts);
                    if(!$updateResult) {
                        throw new aafwException("UPDATE social_accounts FAILED!: result=" . $updateResult . ", SQL=". $updateFromSocialAccounts);
                    }
                }

                if ($socialAccountExists) {
                    $socialAccountsQuery = substr($socialAccountsQuery, 0, strlen($socialAccountsQuery) - 2);
                    $socialAccountsQuery .= " ON DUPLICATE KEY UPDATE
                                                    social_media_account_id = VALUES(social_media_account_id),
                                                    name = VALUES(name),
                                                    mail_address = VALUES(mail_address),
                                                    profile_image_url = VALUES(profile_image_url),
                                                    profile_page_url = VALUES(profile_page_url),
                                                    validated = VALUES(validated),
                                                    friend_count = VALUES(friend_count),
                                                    del_flg = 0";
                    $accountsResult = $dataBuilder->executeUpdate($socialAccountsQuery);
                    if (!$accountsResult) {
                        throw new aafwException("INSERT INTO social_accounts FAILED!: " . $accountsResult . ", SQL=" . $socialAccountsQuery);
                    }
                }

                $shippingAddressesQuery = substr($shippingAddressesQuery, 0, strlen($shippingAddressesQuery) - 2);
                $shippingAddressesQuery .= " ON DUPLICATE KEY UPDATE
                                                 first_name = VALUES(first_name),
                                                 last_name = VALUES(last_name),
                                                 first_name_kana = VALUES(first_name_kana),
                                                 last_name_kana = VALUES(last_name_kana),
                                                 zip_code1 = VALUES(zip_code1),
                                                 zip_code2 = VALUES(zip_code2),
                                                 pref_id = VALUES(pref_id),
                                                 address1 = VALUES(address1),
                                                 address2 = VALUES(address2),
                                                 address3 = VALUES(address3),
                                                 tel_no1 = VALUES(tel_no1),
                                                 tel_no2 = VALUES(tel_no2),
                                                 tel_no3 = VALUES(tel_no3),
                                                 del_flg = VALUES(del_flg),
                                                 updated_at = VALUES(updated_at),
                                                 created_at = VALUES(created_at)";

                $addrResult = $dataBuilder->executeUpdate($shippingAddressesQuery);
                if (!$addrResult) {
                    throw new aafwException("INSERT INTO shipping_address FAILED!: " . $addrResult . ", SQL=" . $shippingAddressesQuery);
                }

                if ($userAttributeExists) {
                    $userAttributesQuery = substr($userAttributesQuery, 0, strlen($userAttributesQuery) - 2);
                    $userAttributesQuery .= " ON DUPLICATE KEY UPDATE
                                                    value = VALUES(value),
                                                    del_flg = VALUES(del_flg)";
                    $attrResult = $dataBuilder->executeUpdate($userAttributesQuery);
                    if (!$attrResult) {
                        throw new aafwException("INSERT INTO user_attributes FAILED!: " . $attrResult . ", SQL=" . $userAttributesQuery);
                    }
                }

                if ($userSearchInfoExists) {
                    $userSearchInfoQuery = substr($userSearchInfoQuery, 0, strlen($userSearchInfoQuery) - 2);
                    $userSearchInfoQuery .= " ON DUPLICATE KEY UPDATE
                                                    sex = VALUES(sex),
                                                    birthday = VALUES(birthday),
                                                    del_flg = VALUES(del_flg)";
                    $infoResult = $dataBuilder->executeUpdate($userSearchInfoQuery);
                    if (!$infoResult) {
                        throw new aafwException("INSERT INTO user_search_info FAILED!: " . $infoResult . ", SQL=" . $userSearchInfoQuery);
                    }
                }

                $store->commit();
            } catch(Exception $e) {
                $store->rollback();
                $error_count ++;
                if ($error_count <= 100) {
                    $tx_failed_msg = "transaction failed!: start id=" . $i . ", end id=" . $maxRange . ", exception=" . $e;
                    $logger->error($tx_failed_msg);
                    $tx_failed_msg = "SyncCoreUserData transaction failed!: start id=" . $i . ", end id=" . $maxRange;
                    $hipchatLogger->error($tx_failed_msg);
                }
            }
        }

        $duration = time() - $start;
        $endMsg = "end SyncCoreUserData: elapsed time={$duration}, max id= {$maxId}, start id={$start_id}, end id={$end_id}";
        $logger->info($endMsg);
        $hipchatLogger->info($endMsg);

        // レコードが見つからなかったユーザーの退会処理
        if (count($not_found_users) > 0) {
            $user_ids = join(",", $not_found_users);
            $withdraw_from_all_brands = "UPDATE brands_users_relations SET withdraw_flg = 1, updated_at = NOW() WHERE user_id IN(" . $user_ids . ")";
            $dataBuilder->executeUpdate($withdraw_from_all_brands);
            $row_affected = $dataBuilder->getAffectedRows();
            $logger->info("ユーザーの退会を実施しました: 更新行数=" . $row_affected . ", ユーザーID=" . $user_ids);
        }
    }
}