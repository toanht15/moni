<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class SqlSelectorService extends aafwServiceBase {

    const CHECK_CONVERSION = "0";

    public $delimiter         = ",";

    public function __construct() {
        $this->sqlSelectorsStore = $this->getModel('SqlSelectors');
    }

    public function getSqlSelectorById($id) {
        return $this->sqlSelectorsStore->findOne(array('id'=>$id));
    }

    public function replaceInputSqlDataByArray($match){
        $inputControllers = array();
        foreach( $match as $item ){
            $controllers = array();
            $div = explode("|", $item);
            $controllers['title']    = str_replace( "<#", "", $div[0] );
            $controllers['type']     = str_replace( ">",  "", $div[1] );
            $controllers['required'] = str_replace( ">",  "", $div[2] );
            if($div[3]) {
                $controllers['check'] = str_replace( ">",  "", $div[3] );
            }
            $inputControllers[] = $controllers;
        }
        return $inputControllers;
    }

    public function sqlIntoCsv($sql_num, $rs,$db){
        $data_csv = array();
        $data_csv['list'][] = $sql_num;
        $data_csv['delimiter'] = $this->delimiter;
        $csv = new CSVParser();
        $array_data['header'] = $csv->out(array('data' => $sql_num), null, true, true);
        while ($row = $db->fetch($rs)) {
            $data_csv = array();
            $row = $this->unserializeRow($row);
            $data_csv['list'][] = $row;
            $data_csv['delimiter'] = $this->delimiter;
            $array_data['data'][] = $csv->out($data_csv);
        }
        return $array_data;
    }

    /**
     * (serialize)という名前を含む列の内容をkey=valueの形に置換する
     * @param $row
     * @return aafwPhysicalEntityBase $row
     */
    private function unserializeRow( $row ){
        foreach( $row as $key => &$val ){
            if( strpos( $key, "(serialize)" ) !== false ){
                $list = unserialize( $val );
                $val = "";
                if( $list ){
                    foreach( $list as $dataKey => $dataVal ){
                        $val .= $dataKey . "=" . $dataVal . "\n";
                    }
                }
            }
        }
        return $row;
    }
}
