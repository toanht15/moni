<?php

AAFW::import('jp.aainc.classes.batch.RotateGrowthUser');

class RotateGrowthUserTest extends BaseTest {

    public function setUp() {
        $this->truncateAll('GrowthUserStats');
        $this->deleteEntities('CpUserActionStatuses', ['updated_at:>=' => '2013-01-01 00:00:00', 'updated_at:<' => '2013-03-01']);
        $this->deleteEntities('CpUsers', ['created_at:>=' => '2013-01-01 00:00:00', 'created_at:<' => '2013-03-01']);
        $this->deleteCampaigns(['start_date:=' => '2013-01-01 00:00:00']);
    }

    public function testDoProcess_deleteOneMonthAgoData() {
        $target = new RotateGrowthUser();
        $this->entity('GrowthUserStats', [
            'last_activated_at' => '2013-01-01'
        ]);

        $target->doProcess('2013-01-31');
        $count = $this->countEntities('GrowthUserStats');
        $this->assertEquals(0, $count);
    }

    public function testDoProcess_deleteFutureData() {
        $target = new RotateGrowthUser();
        $this->entity('GrowthUserStats', [
            'last_activated_at' => '2013-01-31'
        ]);

        $target->doProcess('2013-01-31');
        $count = $this->countEntities('GrowthUserStats');
        $this->assertEquals(0, $count);
    }

    public function testDoProcess_notDelete() {
        $target = new RotateGrowthUser();
        $this->entity('GrowthUserStats', [
            'last_activated_at' => '2013-01-01'
        ]);

        $target->doProcess('2013-01-30');
        $count = $this->countEntities('GrowthUserStats');
        $this->assertEquals(1, $count);
    }

    public function testDoProcess_updateStatus() {
        $target = new RotateGrowthUser();
        $this->entity('GrowthUserStats', [
            'user_id' => 1,
            'status' => GrowthUserStat::STATUS_NEW,
            'activated_at' => '2013-01-01',
            'last_activated_at' => '2013-01-30',
        ]);

        $target->doProcess('2013-01-31');
        $record = $this->findOne('GrowthUserStats', ['user_id' => 1]);
        $this->assertEquals(GrowthUserStat::STATUS_ACTIVE, intval($record->status));
    }

    public function testDoProcess_notUpdateStatus() {
        $target = new RotateGrowthUser();
        $this->entity('GrowthUserStats', [
            'user_id' => 1,
            'status' => GrowthUserStat::STATUS_NEW,
            'activated_at' => '2013-01-01',
            'last_activated_at' => '2013-01-01',
        ]);

        $target->doProcess('2013-01-30');
        $record = $this->findOne('GrowthUserStats', ['user_id' => 1]);
        $this->assertEquals(GrowthUserStat::STATUS_NEW, intval($record->status));
    }

    public function testDoProcess_notExistCpActionIds() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // 1月1日に初回参加のデータを作成
        // [0] 1月1日0時参加
        // [1] 1月1日1時参加
        $cps = $this->prepareCpData($user, [
            '2013-02-28 00:00:00'
        ]);

        // prepareCpDataは1/1 00:00:00〜2/28 23:59:59の期間で作られるので、期間外の日付を指定
        $target->doProcess('2013-03-01');
        $this->assertEquals(0, $this->countEntities('GrowthUserStats'));
    }

    public function testDoProcess_createUserWhoStatusIsNew() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // 1月1日に初回参加のデータを作成
        // [0] 1月1日0時参加
        // [1] 1月1日1時参加
        $cps = $this->prepareCpData($user, [
            '2013-01-01 00:00:00',
            '2013-01-01 01:00:00'
        ]);

        $target->doProcess('2013-01-01');
        $entity = $this->findOne('GrowthUserStats', ['user_id' => $user->id]);
        $result = [
            'user_id' => $entity->user_id,
            'status' => $entity->status,
            'activated_by_cp_id' => $entity->activated_by_cp_id,
            'activated_at' => $entity->activated_at,
            'last_activated_at' => $entity->last_activated_at
        ];

        $expected = [
            'user_id' => $user->id,
            'status' => GrowthUserStat::STATUS_NEW,
            'activated_by_cp_id' => $cps[0]->id,
            'activated_at' => '2013-01-01 00:00:00',
            'last_activated_at' => '2013-01-01 01:00:00'
        ];

        $this->assertEquals($expected, $result);
    }

    public function testDoProcess_createUserWhoStatusIsRevive() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // 初回参加から30日以上のデータを作成
        // [0] 1月1日0時参加
        // [1] 1月31日0時参加
        $cps = $this->prepareCpData($user, [
            '2013-01-01 00:00:00',
            '2013-01-31 00:00:00'
        ]);

        $target->doProcess('2013-01-31');
        $entity = $this->findOne('GrowthUserStats', ['user_id' => $user->id]);
        $this->assertEquals(GrowthUserStat::STATUS_REVIVE, $entity->status);
    }

    public function testDoProcess_createUserWhoStatusIsActive() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // 初回参加から30日未満のデータを作成
        // [0] 1月1日0時参加
        // [1] 1月30日0時参加
        $cps = $this->prepareCpData($user, [
            '2013-01-01 00:00:00',
            '2013-01-30 00:00:00'
        ]);

        $target->doProcess('2013-01-30');
        $entity = $this->findOne('GrowthUserStats', ['user_id' => $user->id]);
        $this->assertEquals(GrowthUserStat::STATUS_ACTIVE, $entity->status);
    }

    public function testDoProcess_update() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        $this->entity('GrowthUserStats', [
            'user_id' => $user->id,
            'activated_at' => '2013-01-01 00:00:00',
            'last_activated_at' => '2013-01-01 00:00:00'
        ]);

        // [0] 1月2日0時参加
        $cps = $this->prepareCpData($user, [
            '2013-01-02 00:00:00'
        ]);

        $target->doProcess('2013-01-02');
        $entity = $this->findOne('GrowthUserStats', ['user_id' => $user->id]);
        $result = [
            'user_id' => $entity->user_id,
            'activated_at' => $entity->activated_at,
            'last_activated_at' => $entity->last_activated_at
        ];

        $expected = [
            'user_id' => $user->id,
            'activated_at' => '2013-01-01 00:00:00',
            'last_activated_at' => '2013-01-02 00:00:00'
        ];

        $this->assertEquals($expected, $result);
    }

    // こっからほぼ結合試験
    // 2013/01/01に新規参加、新規ユーザーとなることを確認する
    public function testDoProcess_createNewRecord() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // アクティブ状態ではないのでレコード無し

        // 1月1日に新規参加
        $cps = $this->prepareCpData($user, [
            '2013-01-01 00:00:00'
        ]);

        $target->doProcess('2013-01-01');
        $entity = $this->findOne('GrowthUserStats', ['user_id' => $user->id]);
        $this->assertEquals(GrowthUserStat::STATUS_NEW, $entity->status);
    }

    // 2013/01/10に2回目の参加
    public function testDoProcess_updateLastActivatedAt() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // 1月1日に新規参加
        $this->entity('GrowthUserStats',
            [
                'user_id' => $user->id,
                'status' => GrowthUserStat::STATUS_NEW,
                'activated_at' => '2013-01-01 00:00:00',
                'last_activated_at' => '2013-01-01 00:00:00'
            ]
        );
        // 1月1日に新規参加、1月10日に2回目の参加
        $cps = $this->prepareCpData($user, [
            '2013-01-01 00:00:00',
            '2013-01-10 00:00:00'
        ]);

        $target->doProcess('2013-01-10');
        $entity = $this->findOne('GrowthUserStats', ['user_id' => $user->id]);
        $this->assertEquals('2013-01-10 00:00:00', $entity->last_activated_at);
    }

    // 2013/01/31に新規ユーザーでなくなる
    public function testDoProcess_changeToActiveUser() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // 1月1日に新規参加、1月10日に2回目の参加
        $this->entity('GrowthUserStats',
            [
                'user_id' => $user->id,
                'status' => GrowthUserStat::STATUS_NEW,
                'activated_at' => '2013-01-01 00:00:00',
                'last_activated_at' => '2013-01-10 00:00:00'
            ]
        );
        // 1月1日に新規参加、1月10日に2回目の参加
        $cps = $this->prepareCpData($user, [
            '2013-01-01 00:00:00',
            '2013-01-10 00:00:00'
        ]);

        $target->doProcess('2013-01-31');

        $entity = $this->findOne('GrowthUserStats', ['user_id' => $user->id]);
        $this->assertEquals(GrowthUserStat::STATUS_ACTIVE, $entity->status);
    }

    // 2013/02/09にレコードが削除される
    public function testDoProcess_deleteRecord() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // 1月1日に新規参加、1月10日に2回目の参加
        $this->entity('GrowthUserStats',
            [
                'user_id' => $user->id,
                'status' => GrowthUserStat::STATUS_NEW,
                'activated_at' => '2013-01-01 00:00:00',
                'last_activated_at' => '2013-01-10 00:00:00'
            ]
        );
        // 1月1日に新規参加、1月10日に2回目の参加
        $cps = $this->prepareCpData($user, [
            '2013-01-01 00:00:00',
            '2013-01-10 00:00:00'
        ]);

        $target->doProcess('2013-02-09');

        $count = $this->countEntities('GrowthUserStats', ['user_id' => $user->id]);
        $this->assertEquals(0, $count);
    }

    // 2013/02/11に3回目の参加
    public function testDoProcess_createReviveRecord() {
        $target = new RotateGrowthUser();
        $user = $this->newUser();

        // アクティブ状態ではないのでGrowthUserStatsレコード無し

        // 1月1日に新規参加、1月10日に2回目の参加、2月10日に3回目の参加
        $cps = $this->prepareCpData($user, [
            '2013-01-01 00:00:00',
            '2013-01-10 00:00:00',
            '2013-02-10 00:00:00'
        ]);

        $target->doProcess('2013-02-10');

        $entity = $this->findOne('GrowthUserStats', ['user_id' => $user->id]);
        $this->assertEquals(GrowthUserStat::STATUS_REVIVE, $entity->status);
    }

    // Cp,CpUsers,CpUserActionStatusesを生成する
    private function prepareCpData($user, $joined_at_array) {

        $cps = [];
        foreach($joined_at_array as $joined_at) {

            // キャンペーン作成
            list($brand, $cp, $cp_action_groups, $cp_actions, $cp_concrete_actions) = $this->newCampaign([[CpAction::TYPE_ENTRY]]);
            $cp_user1 = $this->entity('CpUsers', ['cp_id' => $cp->id, 'user_id' => $user->id, 'created_at' => $joined_at]);
            $this->entity('CpUserActionStatuses', ['cp_user_id' => $cp_user1->id, 'cp_action_id' => $cp_actions[0]->id, 'status' => CpUserActionStatus::JOIN, 'updated_at' => $joined_at]);
            $cp->start_date = '2013-01-01 00:00:00';
            $cp->end_date = '2013-02-28 23:59:59';
            $this->save('Cps', $cp);
            $cps[] = $cp;
        }
        return $cps;
    }
}