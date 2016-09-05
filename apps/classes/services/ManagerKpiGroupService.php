<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class ManagerKpiGroupService extends aafwServiceBase {

    protected $manager_kpi_group_columns;
    protected $manager_kpi_groups;

    //管理者登録
    const ADD_FINISH = 1; // 正常終了
    const ADD_ERROR = 2; // エラー終了

    //パスワード変更
    const CHANGE_FINISH = 1; // 正常終了
    const CHANGE_ERROR = 2; // エラー終了
    const CHANGE_REQUIRED = 3; // 期限切れで変更必要



    public function __construct() {
        $this->manager_kpi_group_columns = $this->getModel('ManagerKpiGroupColumns');
        $this->manager_kpi_groups = $this->getModel('ManagerKpiGroups');
        $this->manager_kpi_column = $this->getModel('ManagerKpiColumns');
    }

    public function getKpiGroups($page = 1, $limit = 20, $params = array(), $order = null) {
        $filter = array(
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
        );

        return $this->manager_kpi_groups->find($filter);
    }

    public function countKpiGroups(){

        return $this->manager_kpi_groups->count();
    }


    public function createKpiGroups($params) {

        $kpiGroup = $this->createEmptyKpiGroups();
        $kpiGroup->name = $params['name'];
        return $this->saveKpiGroup($kpiGroup);
    }

    public function saveKpiGroup($kpiGroup) {
        return $this->manager_kpi_groups->save($kpiGroup);
    }

    public function saveGroupColumn($kpiGroupColumn) {
        $this->manager_kpi_group_columns->save($kpiGroupColumn);
    }

    public function updateGroupColumnInfo($groupId, $kpiColumnIds) {

        $kpi_group_columns = $this->getKpiGroupColumnsByKpiGroupId($groupId);

        $oldKpiColumnIds = array();
        foreach($kpi_group_columns as $kpi_group_column) {
            $oldKpiColumnIds[$kpi_group_column->getManagerKpiColumns()->current()->id] = true;
        }

        foreach($kpiColumnIds as $kpiColumnId) {
            if(!$oldKpiColumnIds[$kpiColumnId]) {
                $kpi_group_column = $this->createEmptyKpiGroupColumns();
                $kpi_group_column->manager_kpi_column_id  =$kpiColumnId;
                $kpi_group_column->manager_kpi_group_id = $groupId;

                $this->manager_kpi_group_columns->save($kpi_group_column);
            }
            $oldKpiColumnIds[$kpiColumnId] = false;
        }

        foreach($oldKpiColumnIds as $key => $value) {
            if($value) {
                $kpi_group_column = $this->getKpiGroupColumnByKpiGroupIdAndKpiColumnId($groupId, $key);
                $this->manager_kpi_group_columns->deletePhysical($kpi_group_column);
            }
        }
    }

    public function createEmptyKpiGroups() {
        return $this->manager_kpi_groups->createEmptyObject();
    }

    public function createEmptyKpiGroupColumns() {
        return $this->manager_kpi_group_columns->createEmptyObject();
    }

    public function updateKpiGroup($managerKpiGroups) {
        return $this->manager_kpi_groups->save($managerKpiGroups);
    }

    public function getKpiGroupById($id) {
        $filter = array(
            'id' => $id,
        );
        return $this->manager_kpi_groups->findOne($filter);
    }

    public function getKpiGroupColumnCountByKpiGroupId($manager_kpi_group_id) {
        $filter = array(
            'manager_kpi_group_id' => $manager_kpi_group_id,
        );
        return $this->manager_kpi_group_columns->count($filter);
    }

    public function getKpiGroupColumnsByKpiGroupId($manager_kpi_group_id) {
        $filter = array(
            'manager_kpi_group_id' => $manager_kpi_group_id,
        );
        return $this->manager_kpi_group_columns->find($filter);
    }

    public function getKpiGroupColumnByKpiGroupIdAndKpiColumnId($manager_kpi_group_id, $manager_kpi_column_id) {
        $filter = array(
            'manager_kpi_group_id' => $manager_kpi_group_id,
            'manager_kpi_column_id' => $manager_kpi_column_id
        );
        return $this->manager_kpi_group_columns->findOne($filter);
    }
}