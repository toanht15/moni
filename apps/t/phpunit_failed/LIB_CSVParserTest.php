<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import ('jp.aainc.aafw.parsers.CSVParser');

class CSVParaserTest extends PHPUnit_Framework_TestCase {
  public function testNormalParse () {
    $csv   = new CSVParser ();
    $rows = $csv->in ( array ( 'csv' => $this->getTestData1() ) );
    $this->assertEquals ( '1',   $rows[0][0] );
    $this->assertEquals ( '2',   $rows[0][1] );
    $this->assertEquals ( '3',   $rows[0][2] );
    $this->assertEquals ( '4',   $rows[0][3] );
    $this->assertEquals ( '1 ' , $rows[1][0] );
    $this->assertEquals ( '2"3', $rows[1][1] );
    $this->assertEquals ( '4"5', $rows[1][2] );
    $this->assertEquals ( '6'  , $rows[1][3] );
    $this->assertEquals ( ''   , $rows[1][4] );
    $this->assertEquals ( ''   , $rows[1][5] );
    $this->assertEquals ( ''   , $rows[1][6] );
    $this->assertEquals ( '7'  , $rows[1][7] );
  }
  public function testFieldParse () {
    $csv   = new CSVParser ();
    $rows = $csv->in ( array (
        'csv' => $this->getTestData1(),
        'flds' => array ( 'fld1', 'fld2', 'fld3', 'fld4' )
    ));
    $this->assertEquals ( '1',   $rows[0]['fld1'] );
    $this->assertEquals ( '2',   $rows[0]['fld2'] );
    $this->assertEquals ( '3',   $rows[0]['fld3'] );
    $this->assertEquals ( '4',   $rows[0]['fld4'] );
    $this->assertEquals ( '1 ' , $rows[1]['fld1'] );
    $this->assertEquals ( '2"3', $rows[1]['fld2'] );
    $this->assertEquals ( '4"5', $rows[1]['fld3'] );
    $this->assertEquals ( '6'  , $rows[1]['fld4'] );
    $this->assertEquals ( ''   , $rows[1][4] );
    $this->assertEquals ( ''   , $rows[1][5] );
    $this->assertEquals ( ''   , $rows[1][6] );
    $this->assertEquals ( '7'  , $rows[1][7] );
  }

  public function getTestData1 ( ) { ob_start () ?>
1   , 2    , 3     , 4,
"1 ", '2"3', "4\"5", 6,"",, ,7
<?php return ob_get_clean();
  }
}

