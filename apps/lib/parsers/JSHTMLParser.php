<?php
AAFW::import( 'jp.aainc.aafw.base.aafwParserBase' );
AAFW::import( 'jp.aainc.aafw.parsers.PHPParser' );

class JSHTMLParser extends aafwParserBase {
  public function getContentType(){ return 'text/javascript'; }
  public function in( $data ) { return true; }
  public function out( $data ) {
    if( $data['__HTML__'] ) $html = $data['__HTML__'];
    else                    $html = PHPParser::out( $data );
    return "document.write('" . str_replace( "'", "\'", preg_replace( "/(\r\n|\r|\n)/", '', $html ) ) . "');";
  }
}
