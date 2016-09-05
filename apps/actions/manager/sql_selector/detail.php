<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
require_once 'parsers/CSVParser.php';

class detail extends BrandcoManagerGETActionBase {

    private $split_delimiter = '---';

    protected $ErrorPage = 'manager/sql_selector/detail.php';

    public $NeedManagerLogin = true;
    public $delimiter         = ",";

    protected $sqlSelectorsStore = null;

    public function doThisFirst() {
        $this->Data['GET']   = $this->GET;
        $this->Data['sqlId'] = isset( $this->GET['exts'][0] ) ? $this->GET['exts'][0] : '1';
    }

    public function validate() {
        $this->Data['errors'] = array();

        /** @var SqlSelectorService $sql_selector_service */
        $sql_selector_service = $this->createService('SqlSelectorService');
        /** @var ConversionService $conversion_service */
        $conversion_service = $this->createService('ConversionService');
        /** @var BrandService $brand_service */
        $brand_service = $this->createService('BrandService');
        $this->Data['brand_name'] = $brand_service->getBrandById($this->brand_id)->name;
        $conversions = $conversion_service->getConversionsByBrandId($this->brand_id);
        foreach($conversions as $conversion){
            $this->Data['conversion'][$conversion->id] = $conversion->name;
        }
        $this->Data['sqlSelector'] = $sql_selector_service->getSqlSelectorById($this->Data['sqlId']);
        if( !$this->Data['sqlSelector'] ){
            return 'redirect: /sql_selector/';
        }
        preg_match_all( "/\<#[^>]*?>/i", $this->Data['sqlSelector']->sql_string, $match );
        $this->Data['match'] = array_unique(( $match[0] ));
        $this->Data["controllers"] = $sql_selector_service->replaceInputSqlDataByArray($this->Data['match']);

            $no = 0;
            foreach( $this->Data["controllers"] as $item ) {
                if ($item['check'] == SqlSelectorService::CHECK_CONVERSION) {
                    $this->Data['check'] = $item['check'];
                }
                if ($this->Data['GET']['mode']) {
                    if ($item['required'] == '1' && !$this->Data['GET']['search' . $no]) {
                        $this->Data['errors']['search' . $no] = '必須入力です';
                    }

                    if ($item['type'] == 'date' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->Data['GET']['search' . $no])) {
                        $this->Data['errors']['search' . $no] = '日付形式で入力ください';
                    }
                    $no++;
                }

            }

        return !count( $this->Data['errors'] );
    }

    public function doAction() {
        set_time_limit( 3600 );

        if( !$this->Data['GET']['mode'] ) {
            return 'manager/sql_selector/detail.php';
        }
        /** @var SqlSelectorService $sql_selector_service */
        $sql_selector_service = $this->createService('SqlSelectorService');
        $sqls = explode($this->split_delimiter, $this->Data['sqlSelector']->sql_string);

        foreach ($sqls as $sql_num => $sql) {
            $no  = '0';
            foreach ($this->Data['match'] as $item) {
                if ($this->Data['GET']['search' . $no] != '') {
                    $sql = str_replace($item, $this->Data['GET']['search' . $no], $sql);
                } else {
                    $sql = preg_replace('/^[^(\n|\z)]*?(' . str_replace(array("|"), array("\|"), $item) . ')[^(\n|\z)]*?(\n|\z)/im', "", $sql);
                }
                $no++;
            }
            //sql_selectorsテーブルにdb_nameカラムがあるが指定していないレコードが多いのでメンテDBを直接指定
            $db = new aafwDataBuilder('maintedb');
            $rs = $db->getBySQL($sql, array('__NOFETCH__'));

            while ($row = $db->fetch($rs)) {
                $temp = array_keys($row);
                for ($i = 0; $i < count($temp); $i++) {
                    if (strpos($temp[$i], "(serialize)") !== false) {
                        $temp[$i] = str_replace("(serialize)", "", $temp[$i]);
                    }
                }
                $this->Data['columns'][$sql_num] = $temp;
                break;
            }

            $this->Data['db'] = $db;
            $this->Data['rs'][$sql_num] = $db->getBySQL($sql, array('__NOFETCH__'));
            if ($this->Data['GET']['mode'] == 'csv') {
                $array_data = $sql_selector_service->sqlIntoCsv($this->Data['columns'][$sql_num],$this->Data['rs'][$sql_num],$db);
                print mb_convert_encoding($array_data['header'], 'Shift_JIS', "UTF-8");
                $csv = new CSVParser();
                header("Content-type:" . $csv->getContentType());
                header($csv->getDisposition());
                foreach($array_data['data'] as $data){
                    print mb_convert_encoding($data, 'Shift_JIS', "UTF-8");
                }
               exit();
            }
        }

        if( $this->Data['GET']['mode'] != 'csv' ) {
            return 'manager/sql_selector/detail.php';
        } else {
            exit();
        }
    }
}
?>
