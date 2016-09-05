<?php
class aafwServiceFactory {
  public function create ( $name, $params = null ) {
    AAFW::import ( 'jp.aainc.classes.services.' . $name );
    if ( !$params ) return new $name;
    $tmp = array();
    $obj = null;
    for ( $i = 0; $i < count ( $params ); $i++ )  $tmp[] = '$params[' . $i. ']';
    eval ( '$obj = new ' . $name . '(' . join ( ',', $tmp ) .  ');' );
    return $obj;
  }
}

