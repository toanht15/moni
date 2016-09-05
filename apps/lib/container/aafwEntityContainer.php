<?php
/**
 *  コンテナー
 */
AAFW::import ( 'jp.aainc.aafw.base.IContainerBase' );
class aafwEntityContainer extends aafwObject implements IContainerBase {
  private $_Position     = 0;
  private $_EntityName   = null;
  private $_StoreMaster  = null;
  private $_StoreRead    = null;
  private $_CurrentStore = null;
  private $_Resource     = null;
  private $_ForUpdate    = null;
  private $_Intrans      = null;
  private $_EntityFactory = null;
  private $_TotalCount   = 0;
  private static $_ObjectCache = array ();

  /**
   *  コンストラクタ
   */
  public function __construct ( $params ) {
    if ( !$params['EntityName'] )
      throw new aafwException ( 'EntityNameがありません' );

    if ( !$params['StoreMaster'] || !$params['StoreRead'] )
      throw new aafwException ( 'Storeの指定が不正が不正です' );

    if ( !$params['EntityFactory'] )
      throw new aafwException ( 'EntityFactoryの指定が不正が不正です' );

    if ( !$params['Resource']  )
      throw new aafwException ( 'Resourceの指定が不正が不正です' );

    AAFW::import ( 'jp.aainc.classes.entities.'. $params['EntityName'] );
    $this->_EntityName    = $params['EntityName'];
    $this->_StoreMaster   = $params['StoreMaster'];
    $this->_StoreRead     = $params['StoreRead'];
    $this->_Resource      = $params['Resource'];
    $this->_ForUpdate     = $params['ForUpdate'];
    $this->_Intrans       = $params['InTrans'];
    $this->_EntityFactory = $params['EntityFactory'];
    $this->_Config        = $params['Config'];
    $this->_CurrentStore  = ( $this->_ForUpdate || $this->_Intrans ) ? $params['StoreMaster'] : $params['StoreRead'];
    $this->_TotalCount    = $this->_CurrentStore->getSelectedRows ( $this->_Resource );
  }

  /**
   * 位置を返す
   * @return 現在のポインタ位置
   */
  public function key () {
    return $this->_Position;
  }

  /**
   * ポインタを次に進める
   * @return 次のポインタ位置
   */
  public function next () {
    return ++$this->_Position;
  }

  /**
   * ポインタ位置が適正かを返す
   * @return 適正な場合 true, 不正な場合は false
   */
  public function valid () {
    return $this->_TotalCount > $this->_Position;
  }

  /**
   * 現在位置のオブジェクトを返す
   * @return mixed|null
   */
    public function current () {
    return $this->createObject ( $this->_Position );
  }

  /**
   * ポインタを初期化する
   */
  public function rewind () {
    $this->_Position = 0;
    if ( $this->_TotalCount ) $this->_CurrentStore->setIndex ( $this->_Resource, 0 );
  }
  /**
   * オブジェクトを返す
   * @return 適宜オブジェクト
   */
  public function createObject () {
    if ( !$this->valid () ) return null;
    $obj = $this->_EntityFactory->create ( $this->_EntityName );
    $values = $this->_CurrentStore->fetch ( $this->_Resource );
    $regex = '#^' . preg_quote ( $this->convertLowerStyle ( $this->convertOneToMany ( $this->_EntityName ) ), '#' ) . '_#';
        $joined = array ();
        foreach ( $values as $key => $value ) {
            if ( preg_match ( $regex, $key ) ) {
                $key = preg_replace ( $regex, '', $key );
                $obj->$key = $value;
            }
            else {

            }
        }
        if ( $joined ) $obj->joinedField = (object)$joined;
    return $obj;
   }

  public function map ( $f ) {
    $result = array ();
    foreach ( $this as $row ) $result[] = $f ( $row );
    return $result;
  }

  public function filter ( $f ) {
    $result = array ();
    foreach ( $this as $row ) {
      if ( $f ($row ) ) {
        $result[] = $row;
      }
    }
    return $result;
  }

  /**
   * オーバーライド
   */
  public function toArray () {
    $result = array();
    foreach ( $this as $row ) {
      $result[] = $row;
    }
    $this->rewind();
    return $result;
  }

  public function total () {
      return $this->_TotalCount;
  }


}
