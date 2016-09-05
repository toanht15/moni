<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwParserBase' );

/*************************************
 * JSONのパーサー
 *************************************/
class JSONParser extends aafwParserBase {
  public function getContentType(){
	//return "text/javascript; charset=utf-8";
    return 'application/json; charset=UTF-8';
  }
  /*************************************
   * JSON文字列を解析してハッシュや配列にして返す
   * @param 文字列
   * @return 配列
   *************************************/
  public  function in($data) {
    $buf = null;
    $buf = json_decode ( $data );
    $buf = $this->deep ( $buf, '$src','if( is_object( $src ) ) return (array)$src; else return $src;' );
    return $buf;
  }

  /*************************************
   * データをJSON文字列にする
   * @param データ
   * @return JSON文字列
   *************************************/
  public  function out($data) {

	$data = $data["json_data"];

	//json形式で出力
    if ( json_encode ){
      $data = $this->deep ( $data, '$x', '
        if     ( !is_object( $x ) )                               return $x;
        elseif ( is_subclass_of( $x, "aafwObject" ) )             return $x->getValues();
        elseif ( is_subclass_of( $x, "aafwPhysicalEntityBase" ) ) return $x->getValues();
        else                                                      return null;
      ');
		$result =  json_encode( $data );
    } else {
		$result =  self::encodeJSON( $data );
    }
    return $result;
  }

  //
  // ArrayからJSON文字列へ
  //
  public  function encodeJSON( $data ){
    if( is_array( $data ) ) {
      list( $buf, $keys )  = array( array(), array_keys( $data ) );
      $isHash = count( preg_grep( '/[^0-9]/', $keys ) );
      foreach( $keys as $key ) $buf[] = ( $isHash ? "'$key' : " : '' ) . self::encodeJSON($data[$key]) ;
      return $isHash ? ( '{' . join( ',', $buf ) . '}' ) : ( '[' . join( ',', $buf ) . ']' );
    } elseif ( is_scalar( $data ) ) {
      if( preg_match( '/^(?:[1-9]\d+|\d)$/',$data ) ) return $data;
      return "'" . str_replace( "'", "\'", preg_replace( "/(\r\n|\r|\n)/",'\\n',  html_entity_decode( $data ) ) ) . "'";
    } elseif( is_object( $data ) ) {
      return '{}';
    }
  }
}
