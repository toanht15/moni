<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

/**
 * 昨日と比べてセグメント今日の条件のユーザー数は30％増減する場合はログする
 * Class CheckSegmentProvisionUsersCount
 */
class CheckSegmentProvisionUsersCount extends BrandcoBatchBase {

    const CHANGE_COUNT_PERCENTAGE_LIMIT = 30;

    public function executeProcess() {
        /** @var SegmentService $segment_service */
        $segment_service = $this->service_factory->create('SegmentService');
        $today_users_counts = $segment_service->getAllSegmentProvisionUsersCountByDate();

        $today = strtotime('today');
        $yesterday = strtotime('-1 day', $today);

        foreach ($today_users_counts as $today_users_count) {
            $yesterday_users_count = $segment_service->getSegmentProvisionUsersCountByDate($today_users_count->segment_provision_id, $yesterday);

            if (!$yesterday_users_count) {
                continue;
            }

            $current_segment_provision = $segment_service->getSegmentProvisionById($today_users_count->segment_provision_id);

            $change_count = abs($today_users_count->total - $yesterday_users_count->total);

            if (($change_count / $yesterday_users_count->total) * 100 > self::CHANGE_COUNT_PERCENTAGE_LIMIT) {
                $this->logger->warn('segment_id='.$current_segment_provision->segment_id.' - segment_provision_id=' . $current_segment_provision->id . ': セグメント条件のユーザー数が30％以上変更されました！');
                $this->hipchat_logger->warn('segment_id='.$current_segment_provision->segment_id.' - segment_provision_id=' . $current_segment_provision->id . ': セグメント条件のユーザー数が30％以上変更されました！');
            }
        }
    }
}