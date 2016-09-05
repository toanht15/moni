<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class PrefectureService extends aafwServiceBase {
    protected $prefecture;
    private static $_instance = null;

    public function __construct() {
        $this->prefecture = $this->getModel("Prefectures");
    }

    public function getAllPrefectures() {
        $filter = array(
            'order' => array(
                'name' => 'id',
                'direction' => 'asc',
            ),
        );

        $prefecture_list = $this->prefecture->find($filter);

        return $prefecture_list;
    }

    public function getPrefectureByPrefId($prefecture_id) {

        $filter = array(
            'conditions' => array(
                'id' => $prefecture_id,
            ),
        );
    
        $prefecture = $this->prefecture->findOne($filter)->name;

        return $prefecture;
    }
    
    public function getPrefecturesKeyValue(){
        $ret = array();
        foreach($this->prefecture->findAll() as $item){
            $ret[$item->id] = $item->name;
        }
        return $ret;
    }
    

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * @return array
     */
    public function getPrefecturesByRegion() {
        $prefectures = array();

        $region_model = $this->getModel('Regions');
        $regions = $region_model->findAll();

        foreach ($regions as $region) {
            $filter = array(
                'conditions' => array(
                    'region_id' => $region->id
                ),
                'order' => array(
                    'name' => 'id',
                    'direction' => 'asc'
                )
            );

            $cur_prefectures = $this->prefecture->find($filter);

            foreach ($cur_prefectures as $cur_prefecture) {
                $prefectures[$region->name][$cur_prefecture->id] = $cur_prefecture->name;
            }
        }

        return $prefectures;
    }
}