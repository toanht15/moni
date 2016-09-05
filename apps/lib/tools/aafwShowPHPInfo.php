<?php
class aafwShowPHPInfo {
  public static function showHelp () { ?>
PHPInfoを実行するだけです
<?php
  }
  public static function getShortName () {
    return 'phpinfo';
  }  
  public static function doService( $argv ){
    phpinfo();
  }
}
