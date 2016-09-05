<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwValidatorBase' );
abstract class aafwActionValidatorBase extends aafwValidatorBase {
  protected $POST    = array();
  protected $GET     = array();
  protected $SESSION = array();
  protected $COOKIE  = array();
  protected $FILES   = array();
  protected $ENV     = array();
  protected $SERVER  = array();
  protected $REQUEST = array();
  protected $Settings = array();
  protected $Data    = array();

  public function __construct(
    $p = array(),
    $g = array(),
    $s = array(),
    $c = array(),
    $f = array(),
    $e = array(),
    $sv = array(),
    $r  = array(),
    $settings = array()
    ){
    $this->setParams( $p, $g, $s, $c, $f, $e, $sv, $r, $settings );
  }

  public function setParams( $p, $g, $s, $c, $f, $e, $sv, $r, $settings ){
    list(
      $this->POST,
      $this->GET,
      $this->SESSION,
      $this->COOKIE,
      $this->FILES,
      $this->ENV,
      $this->SERVER,
      $this->REQUEST,
      $this->Settings
      ) = array( $p, $g, $s, $c, $f, $e, $sv, $r, $settings );
  }

  public function setRequest( $r ){
		$this->REQUEST =  $r;
	}

  public function getRequest( ){
		return $this->REQUEST;
	}
  public function __set( $key, $val ){
    $this->REQUEST[ $key ] = $val;
  }
  public function __get( $key ){
    return $this->REQUEST[$key];
  }
  /**************************************************************
   * このDataプロパティにエラーメッセージとか格納してあげて下さい
   **************************************************************/
  public function getData(){
    return $this->Data;
  }

  /**************************************************************
   * 真偽値を返す。続行可能ならばtrue
   **************************************************************/
  abstract function validate();
}
