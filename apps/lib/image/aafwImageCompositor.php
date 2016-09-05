<?php
AAFW::import ( 'jp.aainc.aafw.image.aafwImage' );
class aafwImageCompositor {
  private $_Base           = null;
  private $_Parts          = null;
  private $_OutTmpFilePath = null;
  private $_isOutTmpFile   = false;

  /**
   * コンストラクタ
   */
  public function __construct ( $base = null, $parts = null ) {
    $this->_Base  = $base;
    $this->_Parts = $parts;
  }

  public function setBase ( $base ) {
    $this->_Base  = $base;
  }

  public function setParts ( $parts )  {
    $this->_Parts = $parts;
  }

  public function execute () {
    if ( !$this->_Base  ) throw new aafwException ( 'Baseがありません' );
    if ( !$this->_Parts ) throw new aafwException ( 'Partsがありません' );

    $raw_base = $this->_Base->getObject();
    foreach ( $this->_Parts as $part ) {
      if ( $part['Object'] ) {
        $part_object = $part['Object']->getObject();
        $raw_base->compositeImage ( $part_object, $part_object->getImageCompose (), $part['x'], $part['y'] );
      }
      elseif ( $part['Text'] ) {
        $raw_base->annotateImage ( $part['Draw']->getObject(), $part['x'], $part['y'], 0, $part['Text'] );
      }
    }
    return $raw_base;
  }

  /**
   * 結合を実行する
   */
  public function executeOut ( $out = null ) {
    $raw_base = $this->execute ();
    $obj = new aafwImage ();
    $obj->setObject ( $raw_base );
    return $obj->out ( $out );
  }
}
