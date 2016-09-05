<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
require_once dirname(__FILE__) . '/../../config/define.php';

class RotateGrowthUser {

    /** @var aafwDataBuilder $dataBuilder */
    private $dataBuilder = null;

    /** @var aafwEntityStoreBase $store */
    private $store = null;

    public function doProcess($date) {

        $this->dataBuilder = aafwDataBuilder::newBuilder();
        $this->store = aafwEntityStoreFactory::create('GrowthUserStats');

        $date = date('Y-m-d', strtotime($date));
        // 1ヶ月前日付(常に<で比較するため29日前を取得)
        $oneMonthAgo = date('Y-m-d', strtotime($date . ' -29 day'));

        try {
            $this->store->begin();

            // 30日間アクティブが無いユーザーと既に記録してしまった未来のユーザーを削除（冪等性を担保）
            $query = "DELETE FROM growth_user_stats WHERE last_activated_at < '$oneMonthAgo' OR last_activated_at >= '$date'";
            $this->dataBuilder->executeUpdate($query);

            // アクティベートしてから30日間たった新規/休眠ユーザーのステータスを変更
            $status = GrowthUserStat::STATUS_ACTIVE;
            $query = "UPDATE growth_user_stats SET status = $status WHERE activated_at < '$oneMonthAgo' AND status <> $status";
            $this->dataBuilder->executeUpdate($query);

            $this->store->commit();
        } catch (Exception $ex) {
            $this->store->rollback();
            throw $ex;
        }

        $nextDate = date('Y-m-d', strtotime($date . ' + 1 day'));

        $condition = [
            'target_date' => $date,
            'target_next_date' => $nextDate
        ];

        $result = $this->dataBuilder->getCpActionIdsOfTheSpecifiedDate($condition);
        $ids = array_column($result, 'id');

        if (count($ids) === 0) {
            // 対象期間のCPが無ければ処理終了
            return;
        }

        $condition = [
            'date_from' => $date,
            'date_to' => $nextDate,
            'cp_action_ids' => $ids,
            '__NOFETCH__' => true
        ];

        $rs = $this->dataBuilder->getDailyActiveUniqueUsers($condition);

        $txContext = new RotateGrowthUser_TxContext();
        $callback = function ($rows) {
            $query = "INSERT INTO growth_user_stats(user_id, status, activated_by_cp_id, activated_at, last_activated_at, created_at, updated_at) VALUES ";
            foreach ($rows as $row) {
                $query .= "(" . $row['user_id'] . "," . $row['status'] . "," . $row['activated_by_cp_id'] . ", '" . $row['activated_at'] . "', '" . $row['last_activated_at'] . "', NOW(), NOW() ), ";
            }
            $query = substr($query, 0, strlen($query) - 2);
            $query .= " ON DUPLICATE KEY UPDATE
                            last_activated_at = VALUES(last_activated_at),
                            updated_at = VALUES(updated_at)";
            return $this->dataBuilder->executeUpdate($query);
        };

        try {
            $this->store->begin();

            while ($row = $this->dataBuilder->fetch($rs)) {
                $previousActivatedAt = $row['previous_activated_at'];

                $alreadyExist = $row['status'] !== null;
                if (!$alreadyExist) {
                    if ($previousActivatedAt === null) {
                        // 新規ユーザー
                        $row['status'] = GrowthUserStat::STATUS_NEW;
                    } elseif ($previousActivatedAt < $oneMonthAgo) {
                        // 休眠ユーザー
                        $row['status'] = GrowthUserStat::STATUS_REVIVE;
                    } else {
                        // アクティブユーザー（初期データ移行と歯抜け日に新規/休眠がいた場合に通過）
                        $row['status'] = GrowthUserStat::STATUS_ACTIVE;
                    }
                }

                $txContext->goNext($row);
                $this->executeState($txContext, $callback);
            }

            $this->executeState($txContext, $callback, true);

            $this->store->commit();
        } catch (Exception $ex) {
            $this->store->rollback();
            throw $ex;
        }
    }

    /**
     * @param RotateGrowthUser_TxContext $txContext
     * @param callable $callback
     * @param boolean $force
     */
    private function executeState($txContext, $callback, $force = false) {

        if ($force || $txContext->canProcess()) {
            if ($txContext->count > 0) {
                $callback($txContext->rows);
                $txContext->clearState();
            }
        }
    }
}

class RotateGrowthUser_TxContext {

    const LIMIT = 100;
    public $count = 0;
    public $total = 0;
    public $rows = array();

    public function goNext($row) {
        $this->rows[] = $row;
        $this->total++;
        $this->count++;
    }

    public function canProcess() {
        return $this->count == self::LIMIT;
    }

    public function clearState() {
        $this->count = 0;
        $this->rows = array();
    }
}