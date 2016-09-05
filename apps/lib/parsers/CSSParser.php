<?php
AAFW::import( 'jp.aainc.aafw.base.aafwParserBase' );
class CSSParser extends aafwParserBase {
  public function getContentType(){
    return 'text/css; charset=utf-8';
  }
	public function in ( $data ) { return $data; }
	public function out ( $data ) { return $data; }
}
