<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwObject' );
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityStoreBase' );


/**
 * 単体のクラスのベース
 */
class aafwEntityBase extends aafwObject {
  protected $_Relations      = array ();
  protected $_RelatedObjects = array ();

  /**
   * 集約クラスの定義を返す
   * @return true / false
   */
  public function getRelations () {
    return $this->_Relations;
  }

  /**
   * 関連オブジェクトを設定する
   * @param 対象のオブジェクト
   */
  public function setRelatedObject ( $obj ) {
    $is_set  = false;
    foreach ( $this->_Relations as $key => $value ) {
      if ( $obj instanceof $key )  {
        $this->_RelatedObjects[$key] = $obj;
        $is_set = true;
        break;
      }
    }
    if ( !$is_set )
      throw new aafwException ( '無関係なオブジェクトが設定されそうになりました' );
  }

  /**
   * 関連オブジェクトを取得する
   * @param 対象のオブジェクト
   */
  protected function getRelatedObject ( $name ) {
    if ( !$this->_RelatedObjects[$name]  )
      throw new aafwException ( '関連オブジェクトが存在しません' );
    return $this->_RelatedObjects[$name];
  }

  /**
   * マジックコール
   * @param メソッド名
   * @param 引数
   * @return 状況による
   */
  public function __call ( $name, $args ) {
    if ( !preg_match ( '#^(has|isExists|getCount|get|saveTo|createEmpty)(.+)$#', $name, $tmp ) )
      throw new aafwException ( 'マジックコールの指定が不正です|' . $name );

    $method = $tmp[1];
    $class  = $tmp[2];
    $target_one = false;
    $result = null;
    if ( !$this->_RelatedObjects[$class] ) {
      if ( $method == 'get' || $method == 'createEmpty' || $method == 'has' ) {
        $class      = $this->convertOneToMany ( $class );
        $target_one = true;
      }
      if ( !$this->_RelatedObjects[$class] ) {
        throw new aafwException ( '無関係な関連オブジェクトのマジックコールが指定されました|' . $class );
      }
    }
    if ( $method == 'saveTo' ) {
      if ( !$args || count ( $args ) > 1 ||  !( $args[0] instanceof aafwEntityBase ) )
        throw new aafwException ( 'マジックコールの引数の指定が不正です' );
      $result = $this->saveToRelatedObject ( $class, $args[0] );
    }
    elseif ( $method == 'createEmpty' ) {
      $result = $this->createRelatedEmptyObject ( $class );
    }
    else {
      if ( $args && ( count ( $args ) > 1 || !is_array ( $args[0] )  ) )
        throw new aafwException ( 'マジックコールの引数の指定が不正です|' . var_export ( $args, true ) );
      if ( $method == 'get' ) {
        if ( $target_one ) $result = $this->findOneByRelatedObject ( $class, $args[0] );
        else               $result = $this->findByRelatedObject ( $class, $args[0] );
      }
      elseif ( $method == 'isExists' ) {
        $result = $this->isExistsByRelatedObject ( $class, $args[0] );
      }
      elseif ( $method == 'getCount' ) {
        $result = $this->getCountByRelatedObject ( $class, $args[0] );
      }
      elseif ( $method == 'has' ) {
        $result = $this->getCountByRelatedObject ( $class, $args[0] );
      }
    }
    return $result;
  }

  /**
   * 関連オブジェクトからfindする
   * @param 関連オブジェクト名
   * @param 引数
   * @return aafwEntityContainer
   */
  public function findByRelatedObject ( $name, $params ) {
    $params = $this->getRelatedObjectParams ( $name, $params );
    return $this->_RelatedObjects[$name]->find ( $params );
  }

  public function findOneByRelatedObject ( $name, $params )  {
    $params = $this->getRelatedObjectParams ( $name, $params );
    return $this->_RelatedObjects[$name]->findOne ( $params );
  }

  /**
   * 関連オブジェクトからfindする
   * @param 関連オブジェクト名
   * @param 引数
   * @return aafwEntityContainer
   */
  public function isExistsByRelatedObject  ( $name, $params ) {
    return $this->getCountByRelatedObject ( $name, $params );
  }

  /**
   * 関連オブジェクトからfindする
   * @param 関連オブジェクト名
   * @param 引数
   * @return aafwEntityContainer
   */
  public function getCountByRelatedObject ( $name, $params ) {
      $params = $this->getRelatedObjectParams ( $name, $params );
    return $this->_RelatedObjects[$name]->count ( $params );
  }

  public function getRelatedObjectParams ( $name, $params ) {
    if ( !$params ) $params = array ();
    foreach ( $this->_Relations[$name] as $key => $value ) {
            if ( !$this->$key ) {
                throw new aafwException ( '不正です@' . get_class ( $this ) . '@' . $key );
            }
      if ( $params['conditions'] )  $params['conditions'][$value] = $this->$key;
      else                          $params[$value] = $this->$key;
    }
    return $params;
  }

  /**
   * 関連オブジェクトにsaveする
   * @param 関連オブジェクト名
   * @param 引数
   */
  public function saveToRelatedObject ( $name, $param ) {
      foreach ( $this->_Relations[$name] as $key => $value ) {
          $param->$value = $this->$key;
      }
    return $this->_RelatedObjects[$name]->save ( $param );
  }

  /**
   * 関連オブジェクトの空オブジェクトをcreateする
   * @param 関連オブジェクト名
   * @return 空オブジェクト
   */
  public function createRelatedEmptyObject ( $name ) {
    return $this->_RelatedObjects[$name]->createEmptyObject();
  }

  public function isMine ( $obj ) {
      $name = strtolower ( get_class ( $this ) ) . "_id";


    return $obj->$name == $this->id;
  }

  public function setValues ( $params ) {
    foreach ($params as $column => $value) {
      $this->$column = $value;
    }
  }
}


