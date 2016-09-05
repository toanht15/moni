<?php
require_once __DIR__ . '/../../../../config/define.php';
AAFW::import ( 'jp.aainc.aafw.net.aafwMultiRequest' );
AAFW::import ( 'jp.aainc.classes.C2DMNotification' );

class  aafwMultiRequestTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function testRequest(){
		$mock = $this->getMockBuilder('aafwMultiRequest')
			->disableOriginalConstructor()
			->setMethods(array ( 'initCurlMulti', 'addHandleToCurl','multiExec','getResults','release' ) )
			->getMock();
		$mock->expects( $this->once() )->method( 'initCurlMulti' )
			->will ( $this->returnValue ( null ) );

		$mock->expects( $this->exactly(3) )->method( 'addHandleToCurl' )
			->with ($this->equalTo('curl'))
			->will ( $this->returnValue ( null ) );

		$mock->expects( $this->once() )->method( 'multiExec' )
			->will ( $this->returnValue ( null ) );

		$mock->expects( $this->once() )->method( 'getResults' )
			->will ( $this->returnValue ( 'hoge' ) );

		$mock->expects( $this->once() )->method( 'release' )
			->will ( $this->returnValue ( null ) );

		$child1 = $this->getMockBuilder('C2DMNotification')
			->setMethods( array ( 'prepareMultiExec' ) )
			->getMock();

		$child2 = $this->getMockBuilder('C2DMNotification')
			->setMethods( array ( 'prepareMultiExec' ) )
			->getMock();

		$child3 = $this->getMockBuilder('C2DMNotification')
			->setMethods( array ( 'prepareMultiExec' ) )
			->getMock();

		$child1->expects ( $this->once () )-> method ( 'prepareMultiExec' )
			->will ( $this->returnValue( 'curl' ) );
		$child2->expects ( $this->once () )-> method ( 'prepareMultiExec' )
			->will ( $this->returnValue( 'curl' ) );
		$child3->expects ( $this->once () )-> method ( 'prepareMultiExec' )
			->will ( $this->returnValue( 'curl' ) );

		$mock->add ( $child1 );
		$mock->add ( $child2 );
		$mock->add ( $child3 );
		$mock->request();

	}

    /**
     * @test
     */
    public function testMultiExec(){
		$mock = $this->getMockBuilder('aafwMultiRequest')
			->disableOriginalConstructor()
			->setMethods(array ( 'exec' ) )
			->getMock();

		$mock->setRunning(3);

		$mock->expects( $this->exactly(3) )->method( 'exec' )
			->will ( $this->returnCallBack ( function () use ( $mock ) {
				$mock->setRunning ( $mock->getRunning () - 1 );
			}));
		$mock->multiExec();
	}

}
