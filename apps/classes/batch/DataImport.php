<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
AAFW::import('jp.aainc.lib.parsers.JSONParser');

abstract class DataImport extends BrandcoBatchBase {

    protected $relateModelName = '';
    protected $storagePath = '';
    protected $brandId;
    protected $brandUserAttrDefinitons;
    protected $csvFilePath;
    protected $storeFactory;

    public function __construct($argv = null) {
        parent::__construct($argv);
        $this->storeFactory = new aafwEntityStoreFactory();
    }

    function executeProcess() {
        $start = time();
        $files = $this->downloadDataFile();
        $definitions = $this->getBrandUserAttrDefinitonsFromCSV();
        $definitions = $this->updateBrandUserAttrDefinition($definitions);
        foreach($files as $file) {
            $this->importData($definitions,$file);
            unlink($file);
        }
        echo 'Execute time: '. (time() - $start);
    }

    abstract public function getBrandUserAttrDefinitonsFromCSV();
    abstract public function importData($definitions,$jsonFilePath);
    abstract public function downloadDataFile();

    public function getUserUID() {
        $model = $this->storeFactory->create($this->relateModelName);
        $records = $model->find(array());
        $uids = array();
        foreach($records as $record) {
            $uids[$record->brands_users_relation_id] = $record->uid;
        }
        return $uids;
    }

    public function updateBrandUserAttrDefinition($definitions) {
        $brandUserAttributeDefinitions = $this->storeFactory->create ('BrandUserAttributeDefinitions');
        for($i = 0; $i < count($definitions); $i++) {
            $brandUserAttributeDefinition = $brandUserAttributeDefinitions->findOne(array('attribute_key' => $definitions[$i]['key']));
            if($brandUserAttributeDefinition) {
                $definitions[$i]['id'] = $brandUserAttributeDefinition->id;
                continue;
            }
            $brandUserAttributeDefinition = $brandUserAttributeDefinitions->createEmptyObject();
            $brandUserAttributeDefinition->brand_id = $this->brandId;
            $brandUserAttributeDefinition->attribute_key = $definitions[$i]['key'];
            $brandUserAttributeDefinition->attribute_name = $definitions[$i]['name'];
            $brandUserAttributeDefinition->attribute_type = BrandUserAttributeDefinitions::ATTRIBUTE_TYPE_SET;
            $brandUserAttributeDefinition->value_set = json_encode(array('1' => '◯'));
            $brandUserAttributeDefinitions->save($brandUserAttributeDefinition);
            $definitions[$i]['id'] = $brandUserAttributeDefinition->id;
        }
        return $definitions;
    }
}