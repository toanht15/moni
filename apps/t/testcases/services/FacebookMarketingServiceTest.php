<?php

class FacebookMarketingServiceTest extends BaseTest {
    /** @var  FacebookMarketingService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("FacebookMarketingService");
    }

    public function testCreateOrUpdateUser_null() {
        $user = $this->target->createOrUpdateUser(array());
        $this->assertEquals($user, null);
    }

    public function testCreateOrUpdateAccount_null() {
        $account = $this->target->createOrUpdateAccount(array());
        $this->assertEquals($account, null);
    }

    public function testFacebookMarketingService_db() {
        try {
            $brand = $this->entity('Brands');
            $user = $this->newUser();
            $brand_user = $this->entity('BrandsUsersRelations',
                array('user_id' => $user->id, 'brand_id' => $brand->id));
            $user_info["id"] = "test_user_id";
            $user_info["brand_user_relation_id"] = $brand_user->id;
            $user_info["name"] = "Test User";
            $user_info["access_token"] = "Test User Token";
            $marketing_user = $this->target->createOrUpdateUser($user_info);
            $this->assertEquals($marketing_user->name, "Test User");

            $account_info['marketing_user_id'] = $marketing_user->id;
            $account_info['account_id'] = 'Test account ID';
            $account = $this->target->createOrUpdateAccount($account_info);
            $this->assertEquals($account->marketing_user_id, $marketing_user->id);
            $account1 = $this->target->getMarketingAccountsByBrandUserRelationId($brand_user->id);
            $this->assertEquals(count($account1), 1);

            $account2 = $this->target->getMarketingAccountsByBrandUserRelationId(null);
            $this->assertEquals($account2, array());

            //audience test
            $audience = $this->target->createOrUpdateAudience(null);
            $this->assertEquals($audience, null);
            $data['id'] = 'test audience id';
            $data['account_id'] = $account->id;
            $audience = $this->target->createOrUpdateAudience($data);
            $this->assertEquals($audience->account_id, $account->id);

            $history = $this->target->createOrUpdateSearchHistory(null, null);
            $this->assertEquals($history, null);

            $history = $this->target->createOrUpdateSearchHistory($audience->id, json_encode(array('hoge' => 'hoge')));
            $this->assertEquals($history->audience_id, $audience->id);

            $condition = $this->target->getSearchConditionByAudienceId(null);
            $this->assertEquals($condition, array());

            $condition = $this->target->getSearchConditionByAudienceId($audience->id);
            $this->assertEquals($condition['hoge'], 'hoge');

            $history = $this->target->getSearchHistoryByAudienceId(null);
            $this->assertEquals($history, null);

            $history = $this->target->getSearchHistoryByAudienceId($audience->id);
            $this->assertEquals($history->audience_id, $audience->id);

            //check null
            $copy_history = $this->target->copySearchHistory(null, null);
            $this->assertEquals($copy_history, null);

            //存在していないid
            $copy_history = $this->target->copySearchHistory('hoge id', 'hoge id');
            $this->assertEquals($copy_history, null);

            $audience2 = $this->target->createOrUpdateAudience($data);
            $copy_history = $this->target->copySearchHistory($audience->id, $audience2->id);
            $this->assertEquals($copy_history->audience_id, $audience2->id);
        } finally {
            if ($history->id) {
                $this->purge('FacebookMarketingSearchFanHistories', $history->id);
            }

            if ($copy_history->id) {
                $this->purge('FacebookMarketingSearchFanHistories', $copy_history->id);
            }

            if ($audience->id) {
                $this->purge('FacebookMarketingAudiences', $audience->id);
            }

            if ($audience2->id) {
                $this->purge('FacebookMarketingAudiences', $audience2->id);
            }

            if ($account->id) {
                $this->purge('FacebookMarketingAccounts', $account->id);
            }

            if ($marketing_user->id) {
                $this->purge('FacebookMarketingUsers', $marketing_user->id);
            }

            //他のテストと一緒に実施する影響なので削除できない。FOREIGN_KEY_CHECKSを０にする
            if ($brand_user->id) {
                $db = aafwDataBuilder::newBuilder();
                $db->executeUpdate('SET FOREIGN_KEY_CHECKS = 0');
                $this->purge('BrandsUsersRelations', $brand_user->id);
                $db->executeUpdate('SET FOREIGN_KEY_CHECKS = 1');
            }

            if ($brand->id) {
                $db = aafwDataBuilder::newBuilder();
                $db->executeUpdate('SET FOREIGN_KEY_CHECKS = 0');
                $this->purge('Brands', $brand->id);
                $db->executeUpdate('SET FOREIGN_KEY_CHECKS = 1');
            }

            if ($user->id) {
                $db = aafwDataBuilder::newBuilder();
                $db->executeUpdate('SET FOREIGN_KEY_CHECKS = 0');
                $this->purge('Users', $user->id);
                $db->executeUpdate('SET FOREIGN_KEY_CHECKS = 1');
            }
        }
    }

    public function testGetAccountByAccountIdNum() {
        $result = $this->target->getAccountByAccountIdNum(null);
        $this->assertEquals($result, null);

        $result = $this->target->getAccountByAccountIdNum('1234');
        $this->assertEquals($result, null);
    }

    public function testGetAccountById() {
        $result = $this->target->getAccountById(null);
        $this->assertEquals($result, null);

        $result = $this->target->getAccountById('1234');
        $this->assertEquals($result, null);
    }

    public function testGetMarketingUserById() {
        $result = $this->target->getMarketingUserById(null);
        $this->assertEquals($result, null);

        $result = $this->target->getMarketingUserById('1234');
        $this->assertEquals($result, null);
    }

    public function testGetMarketingUsersByBrandUserRelationId() {
        $result = $this->target->getMarketingUsersByBrandUserRelationId(null);
        $this->assertEquals($result, null);

        $result = $this->target->getMarketingUsersByBrandUserRelationId(array('123','123'));
        $this->assertEquals($result, array());
    }

    public function testGetMarketingAccountsByMarketingUserId() {
        $result = $this->target->getMarketingAccountsByMarketingUserId(null);
        $this->assertEquals($result, null);

        $result = $this->target->getMarketingAccountsByMarketingUserId('1234');
        $this->assertEquals($result, array());
    }

    public function testGetAudiencesByAccountId() {
        $result = $this->target->getAudiencesByAccountId(null);
        $this->assertEquals($result, null);

        $result = $this->target->getAudiencesByAccountId('1234');
        $this->assertEquals($result, array());
    }

    public function testGetUserByMediaIdAndBrandUserRelationId() {
        $result = $this->target->getUserByMediaIdAndBrandUserRelationId(null, null);
        $this->assertEquals($result, null);

        $result = $this->target->getUserByMediaIdAndBrandUserRelationId('1234', '123');
        $this->assertEquals($result, null);
    }

    public function testGetAccountByMarketingUserIdAndAccountId() {
        $result = $this->target->getAccountByMarketingUserIdAndAccountId(null, null);
        $this->assertEquals($result, null);

        $result = $this->target->getAccountByMarketingUserIdAndAccountId('1234', '123');
        $this->assertEquals($result, null);
    }

    public function testCreateOrUpdateAudience() {
        try {
            $result = $this->target->createOrUpdateAudience(array('account_id' => 1234, 'id' => 'hogehoge'));
            $this->assertEquals(true, false);
        } catch(Exception $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testGetAudiencesCountByAccountId() {
        $result = $this->target->getAudiencesCountByAccountId(null);
        $this->assertEquals($result, 0);

        $result = $this->target->getAudiencesCountByAccountId('1234');
        $this->assertEquals($result, 0);
    }

    public function testGetAudienceById() {
        $result = $this->target->getAudienceById(null);
        $this->assertEquals($result, null);

        $result = $this->target->getAudienceById('1234');
        $this->assertEquals($result, null);
    }

    public function testCountTargetByAudienceId() {
        $result = $this->target->countTargetByAudienceId(null);
        $this->assertEquals($result, 0);

        $result = $this->target->countTargetByAudienceId('1234');
        $this->assertEquals($result, 0);
    }
}
