<?php
AAFW::import ( 'jp.aainc.aafw.file.IRemovable' );
AAFW::import ( 'jp.aainc.aafw.file.aafwFileManager' );

class aafwDirectoryManager implements IRemovable {
  private $_FileManager = null;

  public function __construct ( $fm = null ) {
    if ( is_null ( $fm ) ) $this->_FileManager = new aafwFileManager ();
    else                   $this->setFileManager ( $fm );
  }

  public function setFileManager ( $fm ) {
    if ( !( $fm instanceof aafwFileManager ) )
      throw new aafwException ( 'aafwFileManagerではありません' );
    $this->_FileManager = $fm;
  }

  public function getFileManager () {
    return $this->_FileManager;
  }

  public function isDirectory ( $path ) {
    return is_dir ( $path );
  }

  public function make ( $path ) {
    return mkdir ( $path );
  }

  public function getList ( $path ) {
    if ( !$this->isDirectory ( $path  ) ) throw new aafwException ( $path . 'はディレクトリではありません' );
    $result = array ();
    $path = preg_replace ( '#/$#', '', $path );
    $dir = opendir ( $path );
    while ( $f = readdir ( $dir ) ) {
      if ( preg_match ( '#^\.+$#', $f ) ) continue;
      $result[] = $path . '/' . $f;
    }
    closedir ( $dir );
    return $result;
  }

  public function getRecursiveFileList ( $path ) {
    if ( !$this->isDirectory ( $path  ) ) throw new aafwException ( $path . 'はディレクトリではありません' );
    $result = array ();
    $path = preg_replace ( '#/$#', '', $path );
    $dir = opendir ( $path );
    while ( $f = readdir ( $dir ) ) {
      if ( preg_match ( '#^\.+$#', $f ) ) continue;
      $name = $path . '/' . $f;
      if ( $this->isDirectory ( $name ) ) $result = array_merge ( $result, $this->getRecursiveFileList ( $name ) );
      else                                $result[] = $name;
    }
    closedir ( $dir );
    return $result;
  }

  public function remove ( $path ) {
    foreach ( $this->getList ( $path ) as $name  ) {
      if   ( $this->isDirectory ( $name ) ) $this->remove ( $name );
      else                                  $this->_FileManager->remove ( $name );
    }
    rmdir ( $path );
  }
}
