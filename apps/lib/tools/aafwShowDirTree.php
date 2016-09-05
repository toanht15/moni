<?php
class aafwShowDirTree {
  public static function showHelp () { ?>
args: "api" or "actions"

ディレクトリとファイルの一覧を表示します。
ここでパスを確認して doc コマンドを打ったりすると良いと思います


<?php  }
  public static function getShortName () {
    return 'show';
  }

  /**
   * docコマンド
   * docコメントを解析HTML出力
   **/
  public static function doService( $argv ){
    $path = '';
    if    ( $argv[0] == 'api' )     $path = AAFW::$AAFW_ROOT;
    elseif( $argv[0] == 'actions' ) $path = AAFW::$AAFW_ROOT . '/actions';
    else {
      say( 'show argument miss!! please type "show  (?:actions|api)"' );
      exit();
    }
    say(  AAFW::buildTree( AAFW::getFiles( $path ), 0) );
  }
}
