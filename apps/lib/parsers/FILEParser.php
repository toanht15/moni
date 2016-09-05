<?php
AAFW::import( 'jp.aainc.aafw.base.aafwParserBase' );
class FILEParser extends aafwParserBase {
  private $Attachement = false;
  private $FileType    = null;
  private $FileName    = null;
  public function getContentType(){
    return 'application/octet-stream';
  }
  public function getDisposition(){
    if ( $this->Attachement ) {
      $exts = '';
      $name  = '';
      if ( $this->FileName ) {
        $name = $this->FileName;
      }
      else {
        if ( $this->FileType ) $exts = '.' . $this->FileType;
        else                   $exts = '';
        $name = date ( 'YmdHis' ) . $exts;
      }
      return 'Content-Disposition: attachment; filename="' . $name;
    }
    else {
      return '';
    }
  }

  public function in( $data ) {
    return true;
  }

  public function out( $data ) {
    if( is_array( $data) ){
      $this->Attachement = $data['is_attachment'];
      if ( $data['file_type'] ) $this->FileType = $data['file_type'];
      if ( $data['file_name'] ) $this->FileName = $data['file_name'];
      if ( $data['data'] ) $data = $data['data'];
      else                 $data = $data['file'];
    }
    if( is_file( $data ) ) return fopen( $data, 'r' );
    else                   return $data;
  }
}

