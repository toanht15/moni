<?php
AAFW::import( 'jp.aainc.aafw.base.aafwParserBase' );
class GIFParser extends aafwParserBase {
  public function getContentType(){
    return 'image/gif';
  }
  /*
  public function getDisposition(){
    return 'Content-Disposition: attachment; filename="' . date( 'YmdHis' ) . '.csv"';
  }
  */
  public function in( $data ) {
    return true;
  }

  public function out( $data ) {
    if( is_file( $data ) ) return file_get_contents( $data );
    else                   return $data;
  }
}

