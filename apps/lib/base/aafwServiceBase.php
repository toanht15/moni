<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwObject' );
class aafwServiceBase extends aafwObject {
    protected $_traverseModelName     = 0;
    protected $_maxProcess            = 1;
    protected $_canMultiProcess       = false;
    protected $_oneProcessRecordCount = 500;

    public function getTraverseModelName () {
        return $this->_traverseModelName;
    }

    public function setTraverseModelName ( $val ) {
       $this->_traverseModelName = $val;
    }

    public function getMaxProcess() {
        return $this->_maxProcess;
    }

    public function setMaxProcess ( $val ) {
        $this->_maxProcess =   $val;
    }

    public function getCanMultiProcess () {
        return $this->_canMultiProcess;
    }

    public function setCanMultiProcess ( $val ) {
        $this->_canMultiProcess = $val;
    }

    public function getOneProcessRecordCount () {
        return $this->_oneProcessRecordCount;
    }

    public function setOneProcessRecordCount ( $val ) {
        $this->_oneProcessRecordCount = $val;
    }

    public function getClassPath () {
        return 'jp.aainc.classes.services.' . get_class ( $this ) ;
    }

    public function createTraverseFilter () {
        return array();
    }

    public function getMaxPage ( $count ) {
        return floor ( $count / $this->_oneProcessRecordCount ) + ( $count % $this->_oneProcessRecordCount ? 1 : 0 );
    }

    public function getCurrentProcessCount () {
        return $this->getCommandResult ( 'ps ax | grep ' . $this->getClassPath() . ' | grep page= | grep -v grep | wc -l' ) - 0;
    }

    public function runMultiProcess () {
        if ( !$this->_canMultiProcess )
            throw new aafwException ( 'このサービスはマルチプロセスでの動作をサポートしていません' );

        if ( !$this->_traverseModelName )
            throw new aafwException ( 'トラバースするモデル名がありません' );

        $model = $this->getModel ( $this->_traverseModelName );
        $count = $model->getMax ( 'id', $this->createTraverseFilter () );
        say ( "$count 件を処理します" );
        for ( $i = 1; $i <= $this->getMaxPage ( $count ); $i++ ) {
            $this->waitProcess ();
            $this->createChildProcess ( $i );
        }
    }


    public function createChildProcess ( $page ) {
        return $this->runBackgroundProcess ( 'php' , array (
            AAFW::$AAFW_ROOT .'/lib/AAFW.php',
            'batch',
            $this->getClassPath(),
            'page=' . $page
       ));
    }

    public function canBreakWait () {
       return $this->getCurrentProcessCount() < $this->_maxProcess;
    }

    public function waitProcess () {
        while ( 1 ) {
            if ( $this->canBreakWait() ) break;
        }
    }

    public function runChildProcess ( $page ) {
        if ( !in_array ( 'doChildMain', get_class_methods ( $this ) ) )
            throw new aafwException ( 'マルチプロセスバッチを実装するにはdoChildMainを実装してください' );

        if ( !$this->_canMultiProcess )
            throw new aafwException ( 'このサービスはマルチプロセスでの動作をサポートしていません' );

        if ( !$this->_traverseModelName )
            throw new aafwException ( 'トラバースするモデル名がありません' );

        $model   = $this->getModel ( $this->getTraverseModelName () );
        $records = $model->find ( array (
            'conditions' => $this->createTraverseFilter (),
            'order' => array (
                'name'      => 'id',
                'direction' => 'asc',
            ),
            'pager' => array (
                'count' => $this->_oneProcessRecordCount,
                'page'  => $page,
            ),
        ));
        $this->doChildMain ( $records );
    }
}
