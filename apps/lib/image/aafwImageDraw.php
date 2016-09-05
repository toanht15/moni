<?php
class aafwImageDraw {
  private $_Settings = null;
  public function __construct ( $params ) {
    $this->_Settings = $params;
  }

  public function getSettings () {
    return $this->_Settings;
  }

  public function getObject () {
    $draw = new ImagickDraw ();
    $s = $this->_Settings;
    if ( $s['Font'] )  $draw->setFont     ( $s['Font'] );
    if ( $s['Color'] ) $draw->setFillColor ( $s['Color'] );
    if ( $s['Size'] )  $draw->setFontSize ( $s['Size'] );
    if ( $s['Text'] )  $draw->annotation  ( $s['Text'] );
    return $draw;
  }

}
