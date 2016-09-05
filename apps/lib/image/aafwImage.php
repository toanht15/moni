<?php
AAFW::import ( 'jp.aainc.aafw.file.aafwTempFileManager' );
class aafwImage {
  private $_File = null;
  private $_Self = null;
  private $_Type = null;

  public function __construct ( $file = null ) {
    if ( !$file )  return ;
    if ( !( $file instanceof aafwTempFileManager ) )
      throw new aafwException ( 'ファイルの種別が不正です' );

    $this->_File = $file;
  }

  public function getTempFile () {
    return $this->_File;
  }

  public function getObject () {
    if ( !$this->_Self ) {
      if ( !$this->_File ) throw new aafwException ( 'ファイルの指定がありません' );
      $this->_Self = new Imagick ( $this->_File->getPath() );
    }
    return $this->_Self;
  }

  public function setObject ( $param ) {
    $this->_Self = $param;
  }

  public function clearObject () {
    $this->_Self = null;
  }

  public function getImageWidth () {
    return $this->getObject()->getImageWidth();
  }

  public function getImageHeight () {
    return $this->getObject()->getImageHeight();
  }

  public function out ( $out = null ) {
    if ( !$out )  $out = '/tmp/' . uniqid();
    $this->getObject()->writeImages( $out, true );
    return new aafwTempFileManager ( $out );
  }

  /**
   * 透過範囲に合わせてサイズを自動調整する
   * @param 広さ
   * @param 高さ
   * @param マージン左
   * @param マージン右
   * @param マージン上
   * @param マージン下
   * @return リサイズしたベースイメージ
   */
  public function justify ( $w, $h , $ml = 0, $mr = 0, $mt = 0, $mb = 0 ) {
    $imgBase  = $this->getObject();
    $m_width  = $ml + $mr;
    $m_height = $mt + $mb;
    $flg      = false;

    if ( $imgBase->getImageWidth() > $imgBase->getImageHeight() ) {
      $imgBase->resizeImage ( $w - $m_width, 0 , imagick::FILTER_BLACKMAN, true );
      if ( $h - $m_height > $imgBase->getImageHeight() ) {
        $size = ( $h - $m_height - $imgBase->getImageHeight() ) / 2;
        $imgBase->spliceImage ( 0, $size , 0, 0 );
        $imgBase->spliceImage ( 0, $size , $imgBase->getImageWidth(), $imgBase->getImageHeight() );
      }
      // 横長だけどアスペクト比的にフレームの枠よりも縦長
      else {
        $imgBase->resizeImage ( 0, $h - $m_height , imagick::FILTER_BLACKMAN, true );
        $size = ( $w - $m_width - $imgBase->getImageWidth() ) / 2;
        $imgBase->spliceImage ( $size, 0 , 0, 0 );
        $imgBase->spliceImage ( $size, 0 , $imgBase->getImageWidth(), $imgBase->getImageHeight() );
      }
    }
    else{
      $imgBase->resizeImage ( 0, $h - $m_height , imagick::FILTER_BLACKMAN, true );
      if ( $w - $m_width > $imgBase->getImageWidth() ) {
        $size = ( $w - $m_width - $imgBase->getImageWidth() ) / 2;
        $imgBase->spliceImage ( $size, 0 , 0, 0 );
        $imgBase->spliceImage ( $size, 0 , $imgBase->getImageWidth(), $imgBase->getImageHeight() );
      }
      // 縦長だけどアスペクト比的にフレームの枠よりも横長
      else {
        $imgBase->resizeImage ( $w - $m_width, 0 , imagick::FILTER_BLACKMAN, true );
        $size = ( $h - $m_height - $imgBase->getImageHeight() ) / 2;
        $imgBase->spliceImage ( 0, $size , 0, 0 );
        $imgBase->spliceImage ( 0, $size , $imgBase->getImageWidth(), $imgBase->getImageHeight() );
      }
    }

    if ( $ml || $mt ) $imgBase->spliceImage ( $ml, $mt, 0, 0 );
    if ( $mr || $mb ) $imgBase->spliceImage ( $mr, $mb, $imgBase->getImageWidth(), $imgBase->getImageHeight() );
    $this->__Self = $imgBase;
    return $this;
  }

  public function resize ( $size ) {
    $imgBase  = $this->getObject();
    if ( $imgBase->getImageWidth() > $imgBase->getImageHeight() )
      $imgBase->resizeImage ( $size, 0, imagick::FILTER_BLACKMAN, true );
    else
      $imgBase->resizeImage ( 0, $size, imagick::FILTER_BLACKMAN, true );
    $this->__Self = $imgBase;
    return $this;
  }

  /**
   * ファイルタイプの判別を行う(JPG|PNG|GIF)、
   * それ以外ならとりあえずfalseと言う手抜きっぷり
   * @return ファイルタイプ
   **/
  public function getType (){
    if( $this->_Type ) return $this->_Type;
    if( !$path ) $path = $this->_File->getPath();
    $f = fopen( $path, 'r' );
    $data = fread( $f, 8 );
    fclose( $f );
    if    ( preg_match( '#^\x89PNG#' , $data ) ) return 'png';
    elseif( preg_match( '#^GIF#'     , $data ) ) return 'gif';
    elseif( preg_match( '#^\xFF\xD8#', $data ) ) return 'JPG';
    else                                         return false;
  }
}


