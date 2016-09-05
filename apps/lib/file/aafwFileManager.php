<?php
AAFW::import ( 'jp.aainc.aafw.file.aafwTempFileManager' );
AAFW::import ( 'jp.aainc.aafw.file.IRemovable' );
class aafwFileManager implements IRemovable {
  public function isFile ( $path ) {
    return is_file ( $path );
  }

  public function readALL ( $path ) {
    if ( !$this->isFile ( $path ) )
      throw new Exception ( $path . 'はファイルではありません' );
    return file_get_contents ( $path );
  }

  public function writeALL ( $path, $content ) {
    return  file_put_contents ( $path, $content );
  }

  public function createTempFile ( $path = null ) {
    return new aafwTempFileManager ( $path );
  }

  public function remove ( $path ) {
    if ( !$this->isFile ( $path ) )
      throw new Exception ( $path . 'はファイルではありません' );
    unlink ( $path );
  }

  public function unZip ( $path ) {
    if ( !class_exists ( 'ZipArchive' ) ) throw new aafwException ( 'zipを扱えません' );
    if ( !$this->isFile ( $path ) )       throw new aafwException ( $path . 'はファイルではありません' );
    $temp = $this->createTempFile();
    if ( true ) {
      $zip = new ZipArchive();
      if ( $zip->open ( $path ) !== true ) throw new aafwException ( $path . 'のzipをオープンできませんでした' );
      $zip->extractTo ( $temp->getPath () );
      $zip->close();
    }
    else {
      shell_exec ( 'unzip ' . $path . ' -d '. $temp->getPath() ) ;
    }
    return $temp;
  }
}
