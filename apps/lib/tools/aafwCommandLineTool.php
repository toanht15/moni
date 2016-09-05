<?php
AAFW::import( 'jp.aainc.aafw.tools.aafwDoc' );

/**
 * コマンドラインツール
 * @package org.fww.tools
 * @access public
 * @author t_ishida
 * @todo 処理が増えるようならばサブコマンド名と処理を一体系にしたプラグインにする？
 **/
class aafwCommandLineTool {
  private static $Commands = array();
    const METHOD_CREATE = 'create';
    const METHOD_REMOVE = 'remove';
  /**
   * 入り口
   **/
  public static function doService( $argv ){
    $path = dirname ( __FILE__ );
    $d = opendir ( $path );
    while ( $f = readdir ( $d ) ) {
      if ( preg_match ( '#^\.#', $f ) )   continue;
      if ( $f == basename ( __FILE__ ) )  continue;
      if ( $f == 'doc_template.php'  )    continue;
      if ( is_dir ( $path . '/' . $f ) )  continue;
      $class_name = AAFW::import ( 'jp.aainc.aafw.tools.' .  preg_replace ( '#\.php#', '', $f ) );
      $obj = new $class_name[0];
      $command_name = $obj->getShortName();
      self::$Commands[$command_name] = $obj;
    }
    ob_start();
    if ( $argv[1] == 'help' && self::$Commands[$argv[2]]){
        self::$Commands[$argv[2]]->showHelp( $argv );

    } elseif ( self::$Commands[$argv[1]] ){
      array_shift ( $argv );
      self::$Commands[array_shift($argv)]->doService( $argv );
    }
    else {
        print '============ usage ===========' . "\n";
        foreach ( self::$Commands as $key => $obj ) print 'plz type => php AAFW.php help ' . $key  . "\n";
    }
    print mb_convert_encoding(  ob_get_clean(),  preg_match( '#WIN#', PHP_OS ) ? 'sjis' : 'utf8', 'utf8' );
  }
}
