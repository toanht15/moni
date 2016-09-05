<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');

class download_provision_data extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'provision_data_download';

    public $NeedManagerLogin = true;

    private $db;

    private $targeted_date;
    private $segment_id;
    private $segment_provision_id;

    public function doThisFirst() {
        $this->targeted_date = $this->GET['targeted_date'] ?: date('Y/m/d', strtotime('yesterday'));
        $this->segment_provision_id = $this->GET['exts'][1] ?: null;
        $this->segment_id = $this->GET['exts'][0] ?: null;
        $this->db = aafwDataBuilder::newBuilder();
    }

    public function validate() {
        return true;
    }

    function doAction() {
        if (!$this->segment_provision_id) {
            return 'redirect: /segment/provision_data_download';
        }

        // Export csv
        $csv = new CSVParser();
        $csv->setCSVFileName(date('Ymd', strtotime($this->targeted_date)) . '_' . $this->segment_provision_id);
        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());

        $segment_service = $this->getService('SegmentService');
        $cur_segment = $segment_service->getSegmentById($this->segment_id);

        $page_info = array('brand_id' => $cur_segment->brand_id);
        $cur_users = $this->getBrandsUsersRelationNo($this->segment_provision_id, strtotime($this->targeted_date));

        $bur_no_array = array();
        foreach ($cur_users as $cur_user) {
            $bur_no_array[] = $cur_user['no'];
        }

        $search_condition = array(
            CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO => array(
                'search_profile_member_no_from' => implode(',', $bur_no_array)
            )
        );

        $create_sql_service = $this->getService("CpCreateSqlService");
        $select_query = $create_sql_service->getUserSql($page_info, $search_condition);
        $user_list = $this->db->getBySql($select_query, array(array('__NOFETCH__')));

        $join_users = array();
        foreach ($user_list as $user) {
            $join_users['user_id'][$user['user_id']] = $user['user_id'];
            $join_users['relation_id'][$user['user_id']] = $user['relation_id'];
        }

        /** @var FanListDownloadService $fan_list_download_service */
        $fan_list_download_service = $this->getService('FanListDownloadService');


        // headerとrowsの取得に必要なデータを初回に用意
        $action_data = $fan_list_download_service->getActionData(
            FanListDownloadService::TYPE_PROFILE,
            $page_info
        );

        // headerを出力
        $header = $fan_list_download_service->getActionHeader(
            FanListDownloadService::TYPE_PROFILE,
            $action_data
        );

        $array_data = $csv->out(array('data' => $header), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

        if (count($join_users) == 0) {
            return 'redirect: /segment/provision_data_download';
        }

        $rows = $fan_list_download_service->getActionRows(
            FanListDownloadService::TYPE_PROFILE,
            $join_users,
            $page_info,
            $action_data
        );

        foreach ($rows as $user) {
            $array_data = $csv->out(array('data' => $user), 1);
            print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        }

        exit;
    }

    /**
     * @param $segment_provision_id
     * @param $targeted_date
     * @return mixed
     */
    public function getBrandsUsersRelationNo($segment_provision_id, $targeted_date) {
        $query = 'SELECT bur.no
                    FROM brands_users_relations bur
                        LEFT JOIN segment_provisions_users_relations cpur
                            ON cpur.brands_users_relation_id = bur.id AND cpur.del_flg = 0
                    WHERE bur.del_flg = 0
                        AND cpur.segment_provision_id = ' . $segment_provision_id . '
                        AND cpur.created_date = "' . $targeted_date . '"';

        return $this->db->getBySQL($query);
    }
}
