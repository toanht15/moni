<?php
/**
 * 超絶手抜きphpDocs
 * ブラケットの数とか数えて一番外側の時だけclassとするとか
 * 一段階下のだけfunctionとするとかしてない。
 * 引用符とか気にしないといけなくなるしそこまでする必要アル？
 *   駄目っぽい場合
 *     引用符の中で関数定義している
 *     引用符の中でクラス定義している
 * このパターンが多発するような形式が多くなるようなら、ちゃんとやる
 **/
class aafwDoc {
  public static function showHelp () {?>
超絶手抜きphpDocs

arg: AAFWパスで対象のクラスを指定してください

<?php  }
  /**
   * データ構造だけ抽出する
   * @param 解析対象文字列またはファイルパス
   * @return 解析結果の配列
   **/
  public static function getShortName () {
    return 'doc';
  }

  /**
   * docコマンド
   * docコメントを解析HTML出力
   **/
  public static function doService( $argv ){
    if( !$argv[0] ) {
      say( 'doc argument miss!! please type "doc [namespace.class]"' );
      exit();
    }
    foreach( AAFW::getTargets( $argv[0] ) as $fn  ){
      foreach( self::analyze( $fn ) as $data ){
        include( dirname(__FILE__ ) . '/doc_template.php' );
      }
    }
  }

  public static function analyze( $string ){
    if( is_file($string) ) $string = file_get_contents( $string );
    $in_comment    = false;
    $current       = array();
    $current_class = array();
    $ret = array();
    foreach( preg_split( '#(\r\n|\r|\n)#', $string ) as $row ){
      if    ( preg_match( '#/\*\*+#', $row ) )  $in_comment = true;   // DOCコメント開始
      elseif( preg_match( '#\*\*+/#', $row ) )  $in_comment = false;  // DOCコメント終了
      elseif( $in_comment ){
        if( preg_match( '#@(\S+) (.+)$#', $row, $tmp ) ) $current[trim($tmp[1])][] = trim($tmp[2]);
        else                                             $current['memo'][] = trim(preg_replace( '#^\s*\*+\s*#', '', $row ) );
      }
      else {
        if( preg_match( '#^\s*class\s+(\S+)(?:\s*extends (\S+?)[ $\{])?#', $row, $tmp ) ){
          if( $current_class ) $ret[] = $current_class;
          $current_class            = $current;
          $current_class['name']    = $tmp[1];
          $current_class['parent']  = $tmp[2];
          $current_class['memo']    = join( "\n", $current_class['memo'] );
          $current_class['methods'] = array();
          $current = array();
        }
        elseif( preg_match( '#^\s*(?:(prvate|public)\s+)?(?:(static)\s+)?function\s+([^\( ]+)\s*\((.*)\)#', $row, $tmp)){
          $method = $current;
          $method['scope'] = $tmp[1] ? $tmp[1] : 'public';
          $method['static']= $tmp[2];
          $method['name']  = $tmp[3];
          $method['args']  = array();
          $i = 0; foreach( preg_split( '# *, *#', trim( $tmp[4] ) ) as $arg ) $method['args'][] = array( 'name' => $arg, 'memo' => $current['param'][$i++] );
          $method['memo']    = join( "\n", $method['memo'] );
          $current_class['methods'][] = $method;
          $current = array();
        }
      }
    }
    $ret[] = $current_class;
    return $ret;
  }
}
