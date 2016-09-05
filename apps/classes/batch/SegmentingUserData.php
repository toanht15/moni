<?php
require_once dirname(__FILE__) . '/../../config/define.php';

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class SegmentingUserData extends BrandcoBatchBase {

    private $create_sql_service;
    private $segment_service;
    private $entity_store;

    private $cur_date;

    public function __construct($argv = null) {
        parent::__construct($argv);

        $this->cur_date = strtotime("today");

        $this->entity_store = aafwEntityStoreFactory::create('Segments');
        $this->segment_service = $this->service_factory->create('SegmentService');
        $this->create_sql_service = $this->service_factory->create("SegmentCreateSqlService");
    }

    public function executeProcess() {
        ini_set('memory_limit', '256M');

        // 性能要改善
        $active_segments = $this->segment_service->getCurrentActiveSegments();
        foreach ($active_segments as $active_segment) {
            $this->segmentingUserData($active_segment);
        }
    }

    /**
     * @param $active_segment
     */
    public function segmentingUserData($active_segment) {
        /** @var SegmentingUserDataService $segmenting_data_service */
        $segmenting_data_service = $this->service_factory->create('SegmentingUserDataService');

        // Init raw data
        $page_info = array('brand_id' => $active_segment->brand_id);

        $default_segment_provisions = $this->segment_service->getSegmentProvisionsBySegmentIdAndType($active_segment->id, SegmentProvision::DEFAULT_SEGMENT_PROVISION);

        // 集計したかどうかチェック
        if ($this->segment_service->isSegmentedProvision($default_segment_provisions->current())) {
            return;
        }

        $unconditional_segment_provisions = $this->segment_service->getSegmentProvisionsBySegmentIdAndType($active_segment->id, SegmentProvision::UNCONDITIONAL_SEGMENT_PROVISION);
        $unclassified_segment_provisions = $this->segment_service->getSegmentProvisionsBySegmentIdAndType($active_segment->id, SegmentProvision::UNCLASSIFIED_SEGMENT_PROVISION);

        $segmenting_data_service->createTmpSegmentingUsers();

        try {
            $this->entity_store->begin();

            if ($unclassified_segment_provisions) {
                $unclassified_segment_provision = $unclassified_segment_provisions->current();
                $cur_provision = json_decode($unclassified_segment_provision->provision, true);

                $segmenting_users = $segmenting_data_service->getSegmentingUsers($page_info, $cur_provision);
                $segmenting_users_count = count($segmenting_users);

                $this->segment_service->updateSegmentProvisionsUsersRelations($segmenting_users, $unclassified_segment_provision->id, $this->cur_date);
                $this->segment_service->updateSegmentProvisionUsersCount($segmenting_users_count, $unclassified_segment_provision->id, $this->cur_date);

                $segmenting_data_service->insertTmpSegmentingUsersByQuery($segmenting_users);
            }

            foreach ($default_segment_provisions as $cur_segment_provision) {
                $cur_provision = json_decode($cur_segment_provision->provision, true);

                $segmenting_users = $segmenting_data_service->getSegmentingUsers($page_info, $cur_provision);
                $segmenting_users_count = count($segmenting_users);

                $this->segment_service->updateSegmentProvisionsUsersRelations($segmenting_users, $cur_segment_provision->id, $this->cur_date);
                $this->segment_service->updateSegmentProvisionUsersCount($segmenting_users_count, $cur_segment_provision->id, $this->cur_date);

                $segmenting_data_service->insertTmpSegmentingUsersByQuery($segmenting_users);
            }

            if ($unconditional_segment_provisions) {
                $unconditional_segment_provision = $unconditional_segment_provisions->current();
                $segmenting_users = $segmenting_data_service->getSegmentingUsers($page_info, array(), true);
                $segmenting_users_count = count($segmenting_users);

                $this->segment_service->updateSegmentProvisionsUsersRelations($segmenting_users, $unconditional_segment_provision->id, $this->cur_date);
                $this->segment_service->updateSegmentProvisionUsersCount($segmenting_users_count, $unconditional_segment_provision->id, $this->cur_date);
            }

            $this->entity_store->commit();
        } catch (Exception $e) {
            $this->entity_store->rollback();
            $this->hipchat_logger->error('segmentingUserData Error: brand_id:' . $active_segment->brand_id . ' - segment_id:' . $active_segment->id);
            $this->logger->error('segmentingUserData Error: brand_id:' . $active_segment->brand_id . ' - segment_id:' . $active_segment->id);
            $this->logger->error($e);
        }

        $segmenting_data_service->dropTmpSegmentingUsers();
    }
}