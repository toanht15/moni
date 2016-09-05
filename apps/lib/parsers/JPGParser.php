<?php
AAFW::import( 'jp.aainc.aafw.base.aafwParserBase' );
class JPGParser extends aafwParserBase {
  private $Attachement = false;
  public function getContentType(){
    return 'image/jpeg';
  }
  public function getDisposition(){
    if ( $this->Attachement ) return 'Content-Disposition: attachment; filename="' . date( 'YmdHis' ) . '.jpg"';
    else                      return '';
  }
  public function in( $data ) {
    return true;
  }

  public function out( $data ) {
    if( is_array( $data) ){
      $this->Attachement = $data['is_attachment'];
      if( $data['data'] ) $data = $data['data'];
      else                $data = $data['file'];
    }
    if( is_file( $data ) ) return file_get_contents( $data );
    else                   return $data;
  }
}

