<?php
class aafwLibraryFactory {
  public static function create ( $path, $params = null ) {
    AAFW::import ( $path );
    $tmp  = preg_split ( '#\.#', $path );
    $name = array_pop ( $tmp );
    if ( !$params ) return new $name;
    $tmp = array();
    $obj = null;
    for ( $i = 0; $i < count ( $params ); $i++ )  $tmp[] = '$params[' . $i. ']';
    eval ( '$obj = new ' . $name . '(' . join ( ',', $tmp ) .  ');' );
    return $obj;
  }
}

