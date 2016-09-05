<?php
AAFW::import ( 'jp.aainc.aafw.file.aafwTempFileManager' );
AAFW::import ( 'jp.aainc.aafw.image.aafwImage' );
class aafwImageFactory {
  public static function create ( $path ) {
    return new aafwImage ( new aafwTempFileManager ( $path ) );
  }
}
