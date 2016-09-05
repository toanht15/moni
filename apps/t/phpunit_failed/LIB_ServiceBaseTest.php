<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import ('jp.aainc.aafw.base.aafwServiceBase');
AAFW::import ('jp.aainc.aafw.base.aafwEntityStoreBase');

class LIB_ServiceBaseTest extends PHPUnit_Framework_TestCase {
    private $_target = null;

    public function setup () {
        $this->_target = $this->getMock ( 'aafwServiceBase', array ( 'getCommandResult', 'runBackgroundProcess' ) );
    }

    public function testGetClassPath () {
        $this->assertEquals ( 'jp.aainc.classes.services.' . get_class ( $this->_target ), $this->_target->getClassPath () );
    }

    public function testGetMaxPage () {
        $this->assertEquals ( 1, $this->_target->getMaxPage ( 499 ) );
        $this->assertEquals ( 1, $this->_target->getMaxPage ( 500 ) );
        $this->assertEquals ( 2, $this->_target->getMaxPage ( 501 ) );
    }

    public function testGetCurrentProcessCount () {
        $this->_target->expects ( $this->once()  )->method ( 'getCommandResult' )
            ->with ( 'ps ax | grep ' . $this->_target->getClassPath() .  ' | grep -v grep | wc -l' )
            ->will ( $this->returnValue ( '999' ) );
        $this->assertEquals ( 999, $this->_target->getCurrentProcessCount () );
    }

    public function testCanBreakWait () {
        $target = $this->getMock ( 'aafwServiceBase', array ( 'getCurrentProcessCount' ) );
        $target->expects ( $this->exactly(3) )->method ( 'getCurrentProcessCount' )
            ->will ( $this->returnValue ( 10 ) );

        $target->setMaxProcess ( 9 );
        $this->assertEquals ( false,  $target->canBreakWait() );

        $target->setMaxProcess ( 10 );
        $this->assertEquals ( false,  $target->canBreakWait() );

        $target->setMaxProcess ( 11 );
        $this->assertEquals ( true,  $target->canBreakWait() );
    }

    public function testWaitProcess () {
        $target = $this->getMock ( 'aafwServiceBase', array ( 'canBreakWait' ) );
        $target->expects ( $this->exactly(3) )->method ( 'canBreakWait' )
            ->will ( $this->onConsecutiveCalls ( false, false, true ) );
        $target->waitProcess ();
    }

    public function testCreateChildProcess () {
        $this->_target->expects ( $this->once () )->method ( 'runBackgroundProcess' )
            ->with ( $this->equalTo ( 'php' ), $this->equalTo ( array (
                AAFW::$AAFW_ROOT . '/lib/AAFW.php',
                'batch',
                $this->_target->getClassPath(),
                'page=999'
            )));
        $this->_target->createChildProcess ( 999 );
    }

    public function testRunMultiProcess () {
        $target = $this->getMock ( 'aafwServiceBase', array ( 'getModel', 'waitProcess', 'createChildProcess' ) );

        $model = $this->getMockBuilder ( 'aafwEntityStoreBase' )->disableOriginalConstructor()
            ->setMethods ( array ( 'getMax' ) )
            ->getMock();

        $model->expects ( $this->once() )->method ( 'getMax' )
            ->with ( $this->equalTo ( 'id' ), $this->equalTo ( array () ) )
            ->will ( $this->returnValue ( 501 ) );

        $target->setTraverseModelName ( 'hoge' );
        $target->setCanMultiProcess ( true );
        $target->expects ( $this->once() )->method ( 'getModel' )
            ->with ( $this->equalTo ( 'hoge' ) )
            ->will ( $this->returnValue ( $model ) );

        $target->expects ( $this->exactly ( 2 ) )->method('waitProcess');
        $target->expects ( $this->exactly ( 2 ) )->method('createChildProcess');
        $target->runMultiProcess();
    }

}

