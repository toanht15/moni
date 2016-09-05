<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwParserBase' );
class JSParser extends aafwParserBase {
  public function getContentType(){
    return 'application/x-javascript; charset=utf-8';
  }
	public function in ( $data ) { return $data; }
	public function out ( $data ) {
        if( !is_array( $data ) ) throw new Exception('JSParser.out : 引数は配列で');
        $view = $data['__view__'];
        $params = $data['__REQ__'];
        unset( $data['__view__'] );
        unset( $data['__REQ__'] );
        $this->values = $data;
        $this->params = $params;
        ob_start();
        include $view;
        return ob_get_clean();
    }
}
