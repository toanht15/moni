<?php
/**
 * よく使うディレクトリ構成を適当に初期化する
 * あるディレクトリは作らないです
 **/
class aafwModelGenerator {
  public static function showHelp () { ?>
与えられた引数のリスト分だけモデルクラスを作る
DBからテーブル構造を抜いて自動生成はローカルで
作業するのが微妙なのでやってない
<?php  }
  /**
   * 短い名前です
   **/
  public static function getShortName () {
    return 'm-gen';
  }

    /**
     * @param $argv
     * @param string $method
     */
  public static function doService ( $argv , $method = aafwCommandLineTool::METHOD_CREATE){

    foreach ( $argv as $arg ) {
      $aafwObject = new aafwObject();
      $class = $aafwObject->convertCamel ( $arg );
      $entity_name =  $aafwObject->convertManyToOne ( $class );
      $entity_fname = AAFW::$AAFW_ROOT . '/classes/entities/' . $entity_name . '.php';
      $store_fname = AAFW::$AAFW_ROOT . '/classes/stores/' .  $class . '.php';
      if ($method == aafwCommandLineTool::METHOD_CREATE) {
          if ( !is_file ( $entity_fname ) && !is_file ( $store_fname ))  {
              file_put_contents ( $entity_fname, self::getOneClass ( $entity_name ) );
              file_put_contents ( $store_fname, self::getStoreClass ( $class, $arg, $entity_name ) );
          } else {
              echo $class . '存在しています。';
              exit ();
          }

      } elseif ($method == aafwCommandLineTool::METHOD_REMOVE) {
          if (is_file($entity_fname) && is_file($store_fname)) {
              unlink($entity_fname);
              unlink($store_fname);
          } else {
              echo $class . '存在していません。';
              exit ();
          }
      }
    }
  }

  public static function getOneClass ( $class ) {
      ob_start () ?>
<?php print '<?php' . "\n" ?>
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class <?php echo $class ?> extends aafwEntityBase {
}
<?php return ob_get_clean ();
  }

  public static function getStoreClass ( $class, $table_name, $entity_name ) { ob_start () ?>
<?php print '<?php' . "\n" ?>
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityStoreBase' );
class <?php echo $class ?> extends aafwEntityStoreBase {
      protected $_TableName = "<?php echo $table_name ?>";
      protected $_EntityName = "<?php echo $entity_name ?>";
}
<?php return ob_get_clean ();
  }
}
