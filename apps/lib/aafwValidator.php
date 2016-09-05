<?php
require_once 'base/aafwValidatorBase.php';
require_once 'aafwErrorMessages.php';
class aafwValidator extends aafwValidatorBase {
  protected $Errors = array();
  protected $Messages = null;
  protected $Definition = array();

  public function __construct( $def = null ){
    $this->Messages = aafwErrorMessages::getInstance();
    $this->setDefinition ( $def );
  }

  public function clearErrors (){
    $this->Errors = array ();
  }

  public function setDefinition ( $def ) {
    $this->Definition = $def;
  }

  public function getError( $key = null ){
    if ( $key ) return $this->Errors[$key];
    else        return $this->Errors;
  }

  public function getErrors ( ) {
    return $this->Errors;
  }

  public function getErrorCount () {
    return count ( $this->getErrors() );
  }

  public function setError ( $key, $val ){
    $this->Errors[$key] = $val;
  }

  public function getMessage( $key ){
    if ( !$this->Errors[$key] ) return '';
    return $this->Messages->getErrorMessage( $this->Errors[$key] );
  }

  public function isValid( $key = null ){
    if ( !$key ) return !count( $this->Errors );
    return !isset( $this->Errors[$key] );
  }

  /**
   * 定義から自動バリデートを行う
   * ※定義の例
   *  protected $Definition = array(
   *    // 値の名前に対して定義を行う
   *    'val1' => array(
   *      'required' => 1 ,
   *      'length'   => 100,
   *      'type'     => 'num',  // num | str | date
   *      'range'    => array(
   *        '<' => 100 ,
   *        '>' => 100 ,
   *        ),
   *      'regex'    => '/hogehogeho/i',
   *      /// isから始まるものを指定できる( mix-in )
   *      'validator' => array( 'MailAddress', 'KtaiMailAddress', 'URL' )
   *      ),
   *    'val2' => array(
   *      'required' => 1 ,
   *      'length'   => 100,
   *      'type'     => 'num',  // num | str | date
   *      'range'    => array(
   *        '<' => 100 ,
   *        '>' => 100 ,
   *        ),
   *      'regex'    => '/hogehogeho/i',
   *      ),
   *   );
   * @param ハッシュまたはfwwObject
   * @return true/false
   **/
  public function validate( $values ){
    $errors = array();
    foreach( $this->Definition as $key => $def ){
      $errors[$key] = array();
      /// 必須チェック
      if ( $this->isEmpty ( $values[$key] )) {
        if( $def['required'] ) $errors[$key] = 'NOT_REQUIRED';
        else                   unset ( $errors[$key] );
        continue;
      }
      $tmp_values = array ();
      $failed = false;
      if   ( !is_array ( $values[$key] ) ) $tmp_values = array ( $values[$key] );
      else                                 $tmp_values = $values[$key];
      foreach ( $tmp_values as $val ) {
        /// 正規表現
        if( $def['regex'] && !preg_match( $def['regex'], $val ) ){
          $errors[$key] = 'NOT_MATCHES';
          $failed = true;
          break;
        }

        /// 型チェック
        if( ( $def['type'] == 'num'  && !$this->isNumeric ( $val ) ) ||
            ( $def['type'] == 'file' && !is_file ( $val ) )         ||
            ( $def['type'] == 'date' && !$this->isDate (  $val ) ) ||
            ( $def['type'] == 'datetime' && !$this->isDateTime (  $val ) )
        ) {
          $errors[$key] = 'INVALID_TYPE@' . strtoupper ( $def['type'] );
          $failed = true;
          break;
        }

        if ( $def['equals'] && is_scalar ( $def['equals'] ) ) {
          $result = true;
          if ( preg_match ( '#^@_(.+)_@$#', $def['equals'], $tmp ) ) $result = $val == $values[$tmp[1]];
          else                                                      $result = $val == $def['equals'];
          if ( !$result )  {
            $errors[$key] = 'NOT_EQUAL';
            $failed = true;
            break;
          }
        }

        /// 範囲チェック
        if ( is_array ( $def['range'] ) && !$this->inRange ( $val, $def['range'], $def['type'], $values ) ){
          $errors[$key] = 'OUT_OF_RANGE';
          $keys =  array_keys ( $def['range'] );
          foreach ( $keys  as $key2 )  $errors[$key] .= '@' . $key2 . $def['range'][$key2];
          $failed = true;
          break;
        }

        // ファイルサイズチェック
        if ( $def['type'] == 'file' && $def['size'] && !$this->inFileSize ( $val, $def['size'] ) ) {
          $errors[$key] = 'TOO_LARGE';
          $failed = true;
          break;
        }

        /// 文字長チェック
        if ( $def['type'] == 'str' && $def['length'] && !$this->inStrLen ( $val, $def['length'], true ) ){
          $errors[$key] = 'TOO_LONG@';
          if     ( is_array ( $def['length'] ) && $def['length']['min'] && $def['length']['max'] ) $errors[$key] .= $def['length']['min'] . '-' . $def['length']['max'] . '文字';
          elseif ( is_array ( $def['length'] ) && $def['length']['min'] )                          $errors[$key] .= $def['length']['min'] . '文字以上';
          else                                                                                     $errors[$key] .= ( is_array( $def['length'] ) ? $def['length']['max'] : $def['length']). '文字以内';
          $failed = true;
          break;
        }

        if ( is_array ( $def['validator'] ) ) {
          foreach ( $def['validator'] as $name ) {
            $method = 'is' . $name;
            if ( !$this->$method( $val ) ) {
              $errors[$key] = 'INVALID_' . strtoupper ( $name );
              break;
            }
          }
          if ( $errors[$key] ) {
            $failed = true;
            break;
          }
        }
      }
      if ( $failed ) continue;
      unset ( $errors[$key] );
    }
    $this->Errors = $errors;
    return !count( $errors );
  }

  /**
   * レンジ内かどうか
   * @param 検査対象の値
   * @param 範囲の定義配列
   * @param データ型 (num || date )
   * @return true/false
   **/
  private function inRange( $value, $range, $type, $values ){
    if ( $type == 'date' ) $value = $this->strtoTime( $value );
    foreach( $range as $key => $val ){
      if ( preg_match ( '#\x0b\{(.+?)\}\x0b$#', $val, $tmp ) ) {
        if ( $values[$tmp[1]] )  {
          $val = $values[$tmp[1]];
          if( $type == 'date' ) {
            $val = strtotime( $val );
          }
        }
        elseif ( $tmp[1] == 'NOW' )  {
          $val = time();
        }
        elseif ( $tmp[1] == 'TODAY' ) {
          $val = strtotime ( date('Y/m/d') );
        }
      }
      elseif( $type == 'date' ) {
        $val = strtotime( $val );
      }

      if( ( $key == '>'  && !( $value >  $val ) ) ||
        ( $key == '>=' && !( $value >= $val ) ) ||
        ( $key == '<'  && !( $value <  $val ) ) ||
        ( $key == '<=' && !( $value <= $val ) ) ) return false;
    }
    return true;
  }
}
