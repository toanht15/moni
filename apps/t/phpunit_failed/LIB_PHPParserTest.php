<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import ( 'jp.aainc.aafw.parsers.PHPParser' );
class LIB_PHPParserTest extends PHPUnit_Framework_TestCase {
  public function testGetActionFormValue () {
    $obj = new PHPParser();
    $obj->ActionForm = array (
      'HOGE' => 'xyzzy',
      'array' => array (
        array (
          'named' => array (
            'array' => array ( 1, 2, 3 )
          )
        ),
        array (
          'named' => array (
            'fizz' => 'buzz',
            'hoge' => 'fuga',
          )
        ),
      ),
      'named' => array(
        'array' => array ( 1000, 2000, 3000 ),
        'named' => array (
          'array' => array( 100,200, 300 ),
        ),
      )
    );
    $this->assertEquals ( 'xyzzy', $obj->getActionFormValue ( 'HOGE' ) );
    $this->assertEquals ( 1, $obj->getActionFormValue ( 'array[0][named][array][0]' ) );
    $this->assertEquals ( 'buzz', $obj->getActionFormValue ( 'array[1][named][fizz]' ) );
    $this->assertEquals ( 'fuga', $obj->getActionFormValue ( 'array[1][named][hoge]' ) );
    $this->assertEquals ( 1000, $obj->getActionFormValue ( 'named[array][0]' ) );
    $this->assertEquals ( 2000, $obj->getActionFormValue ( 'named[array][1]' ) );
    $this->assertEquals ( 3000, $obj->getActionFormValue ( 'named[array][2]' ) );
    $this->assertEquals ( 100, $obj->getActionFormValue ( 'named[named][array][0]' ) );
    $this->assertEquals ( 200, $obj->getActionFormValue ( 'named[named][array][1]' ) );
    $this->assertEquals ( 300, $obj->getActionFormValue ( 'named [named]
      [array]
      [2]' ) );
    $this->assertEquals ( null, $obj->getActionFormValue ( 'そんなものは無い' ) );

    try {
      $obj->getActionFormValue ( 'named[array]a[bc]d' );
    } catch ( Exception $e ) {
      $this->assertEquals ( 'Syntax Error', $e->getMessage () );
    }
  }
}
