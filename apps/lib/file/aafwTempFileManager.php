<?php
class aafwTempFileManager {
  protected $_Path      = null;
  protected $_TempPath  = '/tmp';
  public function __construct ( $param = null ) {
    $this->setPath ( $param );
  }

  public function setPath ( $param ) {
    // パスの指定が無い場合はパスだけ作る
    if ( is_null ( $param ) ){
      $path = $this->_TempPath . '/' . uniqid ();
      $this->_Path  = $path;
    }

    // パスの指定がある場合はそのコピーを作業用一時ファイルにする
    elseif ( $data = @file_get_contents ( $param ) ) {
      $path = $this->_TempPath . '/' . uniqid ();
      file_put_contents ( $path, $data );
      $this->_Path = $path;
    }
    // パスの指定があるのにファイルのコピーに失敗した場合
    else {
      throw new aafwException ( $param . 'のコピーに失敗しました' );
    }
  }

  public function getPath () {
    return $this->_Path;
  }

  public function saveTo ( $path, $force = false ) {
    if ( is_file ( $path ) && !$force )
      throw new aafwException ( '既にファイルが存在しています|' . $path );
    copy ( $this->_Path, $path );
    var_dump ( $path );
    chmod ( $path, 0777 );
  }
  public function __destruct () {
    if ( is_dir ( $this->_Path ) ) {
      $dm = new aafwDirectoryManager ();
      $dm->remove ( $this->_Path );
    }
    else {
      @unlink ( $this->_Path );
    }
  }
}
