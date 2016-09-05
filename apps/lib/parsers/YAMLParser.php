<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwParserBase' );
AAFW::import ( 'jp.aainc.vendor.spyc.spyc' );
/******************************************
 * YAMLパーサーって言うかspycのラッパー
 ******************************************/
class YAMLParser {
  private static $CachePath = '/tmp';
  public function  getContentType(){
    return 'text/yaml';
  }
  public function in($data) {
    $cache_name = self::$CachePath . '/' . preg_replace( '#/#', '-', $data ) ;
    if ( is_file( $cache_name ) && filemtime( $cache_name ) > filemtime( $data ) ) return unserialize( file_get_contents ( $cache_name ) );
    $obj = Spyc::YAMLLoad( $data );
    file_put_contents ( $cache_name , serialize( $obj ) );
    chmod ( $cache_name, 0777 );
    return  $obj;
  }
  public function out($data) {
    return Spyc::YAMLDump( $data );
  }
}
