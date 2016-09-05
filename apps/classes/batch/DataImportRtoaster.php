<?php
/**
 * Class DataImportRtoaster
 * php DataImportRtoaster.php brand_id=15 segp='/../../batch/tmp/rtoaster_gdo/import_gdo_brandco_segmentmst_201505011024.csv''
 */
AAFW::import('jp.aainc.classes.batch.DataImport');
AAFW::import('jp.aainc.classes.BaseStorageClient');
AAFW::import('jp.aainc.classes.RtoasterStorageClient');

class DataImportRtoaster extends DataImport{

    protected $relateModelName = 'BrandsUsersRtoasters';
    protected $storagePath = 'rtoaster_gdo';
    protected $brandId = 15;
    protected $csvFilePath = '/../../batch/tmp/rtoaster_gdo/segment_list_mst_20150904181558_0009.csv';

    public function getBrandUserAttrDefinitonsFromCSV() {
        $csv = new CSVParser();
        $segments = $csv->in(dirname(__FILE__) . $this->csvFilePath);
        $definitions = array();
        foreach($segments as $segment) {
            $definitions[] = array(
                'key' => $segment[2],
                'name' => $segment[3],
            );
        }
        return $definitions;
    }

    public function importData($definitions,$jsonFilePath) {
        $brandUserAttributes = $this->storeFactory->create ('BrandUserAttributes');
        $handle = gzopen($jsonFilePath, 'r');
        if (!$handle) {
            return false;
        }
        $uids = $this->getUserUID();
        $brandUserRelationService = new BrandsUsersRelationService();
        while (!gzeof($handle)) {
            $line = gzgets($handle);
            $rows = json_decode($line);
            if(!($brandUserRelationId = array_search($rows->uid,$uids))) {
                continue;
            }
            $userId = $brandUserRelationService->getBrandsUsersRelationById($brandUserRelationId)->user_id;
            if(!$userId) {
                continue;
            }
            foreach($definitions as $definition) {
                if(array_search($definition['key'], $rows->attrs)) {
                    $brandUserAttribute = $brandUserAttributes->findOne(array('definition_id' => $definition['id'], 'user_id' => $userId));
                    if(!$brandUserAttribute) {
                        $brandUserAttribute = $brandUserAttributes->createEmptyObject();
                    }
                    $brandUserAttribute->definition_id = $definition['id'];
                    $brandUserAttribute->user_id = $userId;
                    $brandUserAttribute->value = 1;
                    $brandUserAttributes->save($brandUserAttribute);
                }
            }
        }
        gzclose($handle);
    }

    public function downloadDataFile() {
        if($this->argv['date']) {
            $date = date('Y-m-d', strtotime($this->argv['date']));
        } else {
            $date = date('Y-m-d', strtotime(' -1 day'));
        }
        $storageClient = new RtoasterStorageClient();
        $dataFilePrefix = $storageClient->getPrefixDataFile(array('date' => $date));
        $dataFiles = $storageClient->listObjects($dataFilePrefix);
        $files = array();
        //Download file
        foreach($dataFiles as $dataFile) {
            $storageClient->getObject($dataFile['Key'],dirname(__FILE__) . '/../../batch/tmp/'.$this->storagePath. '/'.mbsplit('/',$dataFile['Key'])[1]);
            $files[] = dirname(__FILE__) . '/../../batch/tmp/'.$this->storagePath. '/'.mbsplit('/',$dataFile['Key'])[1];
        }
        return $files;
    }
}