<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityStoreBase' );
class LIB_EntityStoreBaseTest extends PHPUnit_Framework_TestCase {
    private $target = null;
    public function setup () {
        $this->target = new aafwEntityStoreBase();
        $this->target->setTableName ( 'test' );
    }
    public function testEscapeForSQL () {
        $this->assertEquals ( "hoge''fuga" ,$this->target->escapeForSQL ( "hoge'fuga" ) );
        $this->assertEquals ( "hoge" ,$this->target->escapeForSQL ( "hoge" ) );
    }

    public function testCreateFileters () {
        $this->target->setCatalog ( array (
            'del_flg'  => array (
                'type' => 'string',
            )
        ));
        $this->assertEquals ( "WHERE test.del_flg = '0' AND test.fld1 = 'hoge' AND test.fld2 = 'fuga'", $this->target->createFilters ( array ( 'fld1' => 'hoge', 'fld2' => 'fuga' ) ) );

        $this->assertEquals (
            "WHERE test.del_flg = '0' AND test.fld1 IN ('1','2','3','4''5') ",
            $this->target->createFilters ( array (
                'fld1' => array ( '1','2','3',"4'5" ) ,
            )));

        $this->assertEquals (
            "WHERE ( fld2 IN ('1','2','3','4''5') )",
            $this->target->createFilters ( array (
                'where' => 'fld2 IN (%hoge%)',
                'where_params' => array ( 'hoge' => array ( '1','2','3',"4'5" ) ),
            )));

        $this->assertEquals (
            "WHERE test.del_flg = '0' AND test.fld1 = 'hoge' AND test.fld2 = 'fuga' AND ( fld3 = 'xyzzy' AND fld4 = '''buzz' )",
            $this->target->createFilters ( array (
                'conditions' => array ( 'fld1' => 'hoge', 'fld2' => 'fuga' ),
                'where'      => "fld3 = '%piyo%' AND fld4 = '%fizz%'",
                'where_params' => array ( 'piyo' => 'xyzzy', 'fizz' => "'buzz" ),
            )));
    }

    public function testOrder () {
        $this->assertEquals ( "ORDER BY hogefuga asc ", $this->target->createOrderBySentece ( 'hogefuga asc' ) );
        $this->assertEquals ( "ORDER BY test.hogefuga asc ", $this->target->createOrderBySentece ( array ( 'name' => 'hogefuga', 'direction' => 'asc' ) ) );
        $this->assertEquals ( "ORDER BY test.hogefuga asc ", $this->target->createOrderBySentece ( array ( 'name' => 'hogefuga' ) ) );
        $this->assertEquals ( "", $this->target->createOrderBySentece  ( array ( 'direction' => 'xyzzy' ) ) );
    }

    public function testPager () {
        $this->assertEquals ( "LIMIT 1,2", $this->target->getPager ( 'LIMIT 1,2' ) );
        $this->assertEquals ( "LIMIT 2,2", $this->target->getPager ( array ( 'count' => 2, 'page' => 2  ) ) );
        $this->assertEquals ( "LIMIT 0,2", $this->target->getPager ( array ( 'count' => 2   ) ) );
        $this->assertEquals ( "", $this->target->getPager ( array ( 'page' => 2   ) ) );
    }

    public function testCreateSelectSQL () {
        $this->target->setCatalog ( array (
            'id' =>  array (
                'type'  => 'string',
            ),
            'name' =>  array (
                'type'  => 'string',
            ),
            'del_flg'  => array (
                'type' => 'string',
            )
        ));
        $flds = 'test.id as test_id,test.name as test_name,test.del_flg as test_del_flg';
        $this->assertEquals ( "SELECT $flds FROM test WHERE test.del_flg = '0' AND test.fld1 = '1'", $this->target->createSelectSQL ( array ( 'fld1' => 1 ) ) );
        $this->assertEquals ( "SELECT $flds FROM test WHERE test.del_flg = '0' AND test.fld1 = '1'",
            $this->target->createSelectSQL ( array (
                'conditions' => array ( 'fld1' => 1 )
            )));

        $this->assertEquals ( "SELECT $flds FROM test WHERE test.del_flg = '0' AND test.fld1 = '1' ORDER BY fld2 asc ",
            $this->target->createSelectSQL ( array (
                'conditions' => array ( 'fld1' => 1 ),
                'order' => 'fld2 asc',
            )));

        $this->assertEquals ( "SELECT $flds FROM test WHERE test.del_flg = '0' AND test.fld1 = '1' ORDER BY fld2 asc  LIMIT 2,2",
            $this->target->createSelectSQL ( array (
                'conditions' => array ( 'fld1' => 1 ),
                'order' => 'fld2 asc',
                'pager' => array ( 'count' => 2, 'page' => 2 ),
            )));

        $this->assertEquals ( "SELECT $flds FROM test WHERE test.del_flg = '0' AND test.fld1 = '1' ORDER BY fld2 asc  LIMIT 2,2 FOR UPDATE",
            $this->target->createSelectSQL ( array (
                'conditions' => array ( 'fld1' => 1 ),
                'order' => 'fld2 asc',
                'pager' => array ( 'count' => 2, 'page' => 2 ),
                'for_update' => true,
            )));
    }

    public function testCreateSQLWithJoin () {
        $this->target->setCatalog ( array (
            'flds1'  => array (
                'type' => 'string',
            ),
            'del_flg'  => array (
                'type' => 'string',
            )
        ));
        $this->target->setTableName ( 'pages' );
        $this->assertEquals (
            "SELECT pages.flds1 as pages_flds1,pages.del_flg as pages_del_flg,page_reports.* FROM pages INNER JOIN page_reports page_reports ON pages.id = page_reports.page_id WHERE pages.del_flg = '0' AND pages.fld1 = '1'",
            $this->target->createSelectSQL ( array (
                'conditions' => array (
                    'fld1' => 1
                ),
                'join' => 'page_reports'
            )));
    }

    public function testCreateInsert () {
        $this->target->setCatalog ( array (
            'id' => array (
                'type' => 'string',
                'key' => true,
            ),
            'fld1' => array (
                'type' => 'string',
            ),
            'fld2' => array (
                'type' => 'string',
            ),
        ));
        $this->assertEquals (
            "INSERT INTO test ( id,fld1,fld2 ) VALUES( '123','456','789'); ",
            $this->target->createInsertSQL ( array (
                'id' => '123',
                'fld1' => '456',
                'fld2' => '789',
            )));

        $this->assertEquals (
            "INSERT INTO test ( id,fld1 ) VALUES( '123','456'); ",
            $this->target->createInsertSQL ( array (
                'id' => '123',
                'fld1' => '456',
                'fld2' => null,
            )));

        $this->assertEquals (
            "INSERT INTO test ( id,fld1,fld2 ) VALUES( '123','456',''); ",
            $this->target->createInsertSQL ( array (
                'id' => '123',
                'fld1' => '456',
                'fld2' => '',
            )));
    }

    public function testCreateUpdate () {
        $this->target->setCatalog ( array (
            'id' => array (
                'type' => 'string',
                'key' => true,
            ),
            'fld1' => array (
                'type' => 'string',
            ),
            'fld2' => array (
                'type' => 'string',
            ),
            'del_flg' => array (
                'type' => 'string',
            ),
        ));
        $this->assertEquals (
            "UPDATE test SET id = '123',fld1 = '456',fld2 = '789'  WHERE test.del_flg = '0' AND test.id = '123'",
            $this->target->createUpdateSQL ( array (
                'id' => '123',
                'fld1' => '456',
                'fld2' => '789',
            )));

        $this->assertEquals (
            "UPDATE test SET id = '123',fld1 = '456'  WHERE test.del_flg = '0' AND test.id = '123'",
            $this->target->createUpdateSQL ( array (
                'id' => '123',
                'fld1' => '456',
                'fld2' => null,
            )));
    }
}

