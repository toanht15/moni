<?php
require_once 'parsers/YAMLParser.php';
/**
 * エラーメッセージを大体作る
 */
class aafwErrorMessages {
  private static $Instance = null;
  private $ErrorMessages = array(
    'NOT_REQUIRED' => '必ず入力して下さい',
    'NOT_MATCHES'  => '正しい形式',
    'NOT_EQUAL'    => '一致した値',
    'INVALID_TYPE' => array(
      'DATE'    => '日付',
      'NUM' => '数値',
    ),
    'INVALID_URL' => 'URLの形式',
    'INVALID_MAILADDRESS' => '有効なメールアドレスを入力して下さい',
    'INVALID_KTAIMAILADDRESS' => '携帯メールアドレス' ,
    'INVALID_ZIP' => '郵便番号の形式(xxx-xxxx)',
    'INVALID_ZIPCODE' => '郵便番号の形式(xxx-xxxx)',
    'INVALID_TEL' => '電話番号の形式(xx-xxxx-xxxx)',
    'INVALID_ZENHIRA' => '全角ひらがな',
    'INVALID_ZENKANA' => '全角カタカナ',
    'OUT_OF_RANGE' => array(
      '<'  => 'より小さく',
      '>'  => 'より大きく',
      '<=' => '以下',
      '>=' => '以上',
    ),
    'TOO_LONG' => '{%1}で入力して下さい',
    'INVALID_ALNUMSYMBOL' => '半角英数記号で入力して下さい',
    'TOO_LARGE' => 'ファイルサイズを指定サイズ以内',
    'INVALID_BRAND_NAME_FORMAT' => '全角文字の後に半角スペースが含まれるパターンを避けて入力して下さい'
  );
  private $CustomMessage = array();

  /**
   * シングルトン
   * @return インスタンス
   */
  public static function getInstance(){
    if( !self::$Instance ){
      $class = __CLASS__;
      self::$Instance = new $class;
    }
    return self::$Instance;
  }

  /**
   * コンストラクタ
   */
  private function __construct(){
    if ( is_file( $fn = AAFW_DIR . '/config/err.yml' ) ) {
      $yml = new YAMLParser();
      $err_master = $yml->in( $fn );
      foreach ( $err_master as $key => $value ) {
        if ( $this->ErrorMessages[$key] ){
          $this->ErrorMessages[$key] = $value;
          unset ( $err_master[$key] );
        }
      }
      $this->CustomMessage = $err_master;
    }
  }

  /**
   * エラーメッセージが存在するか否かについて判定する
   * @param <string>  エラータイプ
   * @return 存在すればtrue: 存在しなければfalse
   */
  public function isExistsMessage ( $error ) {
    return $this->__getErrorMessage( $error ) ? true : false;
  }

  public function getErrorTitle( $error_type ){
    $title = $this->__getErrorMessage( 'ERROR_TITLE_' . $error_type );
    if( $title )  return $title;
    else      return 'エラーが発生しました';
  }

  /**
   * エラーメッセージを大体作成する
   * @param <string>  エラータイプ
   * @return 大体のエラーメッセージ
   */
  public function getErrorMessage( $error ) {
    if ( $this->CustomMessage[trim($error)] ) return $this->parseColumnNames ( $this->CustomMessage[trim($error)] );
    $msg = $this->parseColumnNames ( $this->__getErrorMessage( $error ) );
    if ( !$msg ) return 'システムが混雑しています。再度お試しください。';
    if ( !preg_match ( '#入力して下さい$#', $msg ) ) return $msg . 'で入力して下さい';
    return  $this->parseColumnNames ( $msg );
  }

  /**
   * エラーメッセージを大体作成する
   * @param <string>  エラータイプ
   * @return 大体のエラーメッセージ
   */
  private function __getErrorMessage( $error ){
    if ( $this->CustomMessage[$error] ) return $this->CustomMessage[$error];
    $elms = preg_split( '#@#', $error );
    $msg = '';
    $master = $this->ErrorMessages;
    while ( $idx = array_shift( $elms ) ){
      if     ( is_array( $master[$idx] ) )                  $master = $master[$idx];
      elseif ( is_numeric( $idx ) )                         $msg    = str_replace( '{NUM}',$idx, $msg );
      elseif ( $master[$idx] )                              $msg   .= $master[$idx];
      elseif ( preg_match( '#([<>]=?)(.+)#', $idx, $tmp ) ) $msg   .= str_replace( $tmp[0], $tmp[2].$master[$tmp[1]], $idx );
      elseif ( preg_match_all( '#\{%(\d+)\}#', $msg, $matches) ){
        sort($matches[1]);
        $msg   = str_replace( '{%' . $matches[1][0] . '}', $idx, $msg );
      }
      else                                                  break;
    }
    return $msg;
  }

  public function parseColumnNames ( $msg ) {
    if ( preg_match_all ( '#\x0b\{(.+?)\}\x0b#', $msg, $tmp ) ){
      foreach ( $tmp[1] as $col ) {
        if ( $this->CustomMessage['@COLUMNS'][$col] ) {
          $msg = preg_replace ( '#\x0b\{' . $col. '\}\x0b#', $this->CustomMessage['@COLUMNS'][$col], $msg );
        }
      }
    }
    return $msg;
  }
}
