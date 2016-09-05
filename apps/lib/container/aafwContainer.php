<?php
AAFW::import ( 'jp.aainc.aafw.base.IContainerBase' );
/**
 *  コンテナー
 */
class aafwContainer extends aafwObject implements IContainerBase {
  private $_Position        = 0;
  private $_canConverObject = false;
  private $_InnerArray      = array();
  private $_TotalCount      = 0;

  /**
   *  コンストラクタ
   */
  public function __construct ( $params, $can_convert_object = false ) {

    if ( !is_array ( $params ) )
      throw new aafwException ( '配列ではありません' );

    $this->_canConverObject = $can_convert_object;
    $this->_Config          = $params['Config'];
    $this->_TotalCount      = count ( $params );
    $this->_InnerArray      = $params;
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
   * @return オブジェクト
   */
  public function current () {
    return $this->createObject ( $this->_Position );
  }

  /**
   * ポインタを初期化する
   */
  public function rewind () {
    $this->_Position = 0;
  }

  /**
   * オブジェクトを返す
   * @return 適宜オブジェクト
   */
  public function createObject ( $index ) {
    if ( !$this->valid () ) return null;
    $elm = $this->_InnerArray[$index];
    if ( $this->_canConverObject ) $elm = (object)$elm;
    return $elm;
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
    return count($this->_InnerArray);
  }
}
