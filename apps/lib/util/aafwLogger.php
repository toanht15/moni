<?php
class aafwLogger{
  private $lines       = array();
  private $path        = '';
  private $auto_commit = false;
  private $flds        = array( 'type', 'timestamp', 'contents' );
  private static $obj  = array();

  public static function getInstance( $p = null ){
    $class = __CLASS__;
    if( !$p ) $p = '/tmp/' . date('Ym') . '.crawler-log';
    if( self::$obj[$p] ) return self::$obj[$p];
    else                 return ( self::$obj[$p] = new $class( $p ) );
  }
  
  public function __construct( $p = null ) {
    if( $p ){
      $this->path = $p;
    }  elseif( !$p ){
      $p = date('Ym') . '.crawler-log';
      $this->path =  '/tmp/' . $p;
    }
  }

  public function append( $type, $ln , $desc = null){
    $this->lines[] = "[$type]:" . date("Y/m/d H:i:s") . ":$ln" . ($desc ? "\n" . $desc : null);
  }

  public function save(){
    if( !( $fno = fopen( $this->path, 'a' ) ) ) die('ファイルが開けない');
    foreach( $this->lines as $ln )  fwrite( $fno, "$ln\n" );
    $this->lines = array();
    fclose($fno);
  }
  public function toString(){ return join("\n" , $this->lines ); }
  //TODO:やる気が有る時にファイルの読み込みとか、フィルタとか。
  public function __destruct(){
    if( $this->lines ) $this->save();
  }
}
