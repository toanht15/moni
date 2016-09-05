<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.brandco.segment.trait.SegmentTrait');
AAFW::import('jp.aainc.classes.brandco.segment.trait.SegmentProvisionTrait');
AAFW::import('jp.aainc.classes.brandco.segment.trait.BrandSegmentLimitTrait');
AAFW::import('jp.aainc.classes.brandco.segment.trait.SegmentProvisionUsersCountTrait');
AAFW::import('jp.aainc.classes.brandco.segment.trait.SegmentProvisionsUsersRelationTrait');

class SegmentService extends aafwServiceBase {
    const SQL_EXECUTE_LIMIT = 100;

    const SEGMENT_CONDITION_STATUS_DEFAULT  = 1;
    const SEGMENT_CONDITION_STATUS_FIX      = 2;

    const SEGMENT_CONDITION_SESSION_KEY = 'segmentCondition'; // メッセージアクション用
    const SEGMENT_ADS_CONDITION_SESSION_KEY = 'segmentAdsCondition'; // adsアクション用

    use SegmentTrait;
    use SegmentProvisionTrait;
    use BrandSegmentLimitTrait;
    use SegmentProvisionUsersCountTrait;
    use SegmentProvisionsUsersRelationTrait;

    private $segments;
    private $segment_provisions;
    private $brand_segment_limits;
    private $segment_provision_users_counts;
    private $segment_provisions_users_relations;

    private $logger;
    private $data_builder;
    private $hipchat_logger;
    private $service_factory;

    public function __construct() {
        $this->segments = $this->getModel('Segments');
        $this->segment_provisions = $this->getModel('SegmentProvisions');
        $this->brand_segment_limits = $this->getModel('BrandSegmentLimits');
        $this->segment_provision_users_counts = $this->getModel('SegmentProvisionUsersCounts');
        $this->segment_provisions_users_relations = $this->getModel('SegmentProvisionsUsersRelations');

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->data_builder = aafwDataBuilder::newBuilder();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        $this->service_factory = new aafwServiceFactory();
    }

    /**
     * @param $brand_id
     * @return array
     */
    public function getSegmentCountList($brand_id) {
        $total = 0;
        $total_limit = 0;
        $segment_count_list = array();

        foreach (Segment::$segment_type_list as $segment_type) {
            $count = $this->countSegmentByType($segment_type, $brand_id);
            $limit = $this->getSegmentLimit($segment_type, $brand_id);

            $segment_count_list[$segment_type]['count'] = $count;
            $segment_count_list[$segment_type]['limit'] = $limit;

            $total += $count;
            $total_limit += $limit;
        }

        $segment_count_list['total']['count'] = $total;
        $segment_count_list['total']['limit'] = $total_limit;

        return $segment_count_list;
    }

    /**
     * @param $segment_provision_condition
     * @return array
     */
    public function getSegmentProvisionArray($segment_provision_condition) {
        $cur_segment_provision = array();
        $segment_provision_index = 1;

        if (!$this->isValidCondition($segment_provision_condition)) {
            return $cur_segment_provision;
        }

        foreach ($segment_provision_condition as $conditions) {
            $cur_segment_provision['segmenting_condition_' . $segment_provision_index] = array();

            foreach ($conditions as $condition) {
                if (Util::isNullOrEmpty($condition)) {
                    continue;
                }

                $condition_array = json_decode($condition, true);
                $cur_segment_provision['segmenting_condition_' . $segment_provision_index] += $condition_array;
            }

            $segment_provision_index++;
        }

        return $cur_segment_provision;
    }

    /**
     * @param $segment_provision_condition
     * @return string
     */
    public function getSegmentProvision($segment_provision_condition) {
        $cur_segment_provision = $this->getSegmentProvisionArray($segment_provision_condition);

        return empty($cur_segment_provision) ? "" : json_encode($cur_segment_provision);
    }

    /**
     * @param $segment_provision_condition
     * @return bool
     */
    private function isValidCondition($segment_provision_condition) {
        if (count($segment_provision_condition) > 1) {
            return true;
        }

        foreach ($segment_provision_condition as $condition_list) {
            foreach ($condition_list as $condition) {
                if (!Util::isNullOrEmpty($condition)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function escapeForSQL($query) {
        return $this->segments->escapeForSQL($query);
    }

    /**
     * @param $condition_key
     * @return bool
     */
    public static function isLegalProvisionCondition($condition_key) {
        return strpos($condition_key, 'segmenting_condition_') !== false;
    }

    /**
     * @param $segment_condition_session
     * @return array
     */
    public function getProvisionIdsFromSession($segment_condition_session) {
        $provision_id_array = array();

        foreach($segment_condition_session as $provisions) {

            foreach($provisions as $provision_id) {
                $provision_id_array[] = $provision_id;
            }

        }

        return $provision_id_array;
    }
}