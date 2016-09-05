<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwParserBase' );
AAFW::import('jp.aainc.classes.BrandInfoContainer');

/***************************
 * PHPParserっていうか外部PHPをテンプレートエンジンとして使うだけの不便なやつ
 * それと、その場で思いついた適当なHTML生成ヘルパが付いている
 *
 * @todo: ヘルパ部分のメソッド名微妙じゃね(主にDeeplyってのが要らないよね)
 ****************************/
class PHPParser extends aafwParserBase {
  protected $values      = array();
  protected $params      = array();
  protected $action;
  private $Logger        = null;
  private $Methods       = array();
  private $Matches = array();
  const ACTION_FORM = "\x0bActionForm\x0b";

  public function __construct( $settings = array() ){
    $plugin_dir = $settings['plugin_dir'];
    !$plugin_dir && $plugin_dir = dirname( __FILE__ ) . '/helpers';
    if( is_dir( $plugin_dir ) ){
      $d = opendir( $plugin_dir );
      while( $fn = readdir( $d ) ){
        if( !preg_match( '#^([^\.]+)\.php$#', $fn, $tmp ) ) continue;
        $class = $tmp[1];
        require_once $plugin_dir . '/' . $fn;
        $this->Methods[$class] = new $class();
      }
    }
  }

  /***************************
   * セッタ(笑)
   ****************************/
  public function __set( $key, $value ){
    $this->values[$key] = $value;
  }
  /***************************
   * ゲッタ(笑)
   ****************************/
  public function __get( $key ){
    return $this->values[$key];
  }

  public function __call( $name, $args ){
    if( $this->Methods[$name] ) {
      if ( !$args ) $args = array();
      $args[] = $this;
      return $this->Methods[$name]->doMethod( $args );
    }
    if( $this->values[$name] ){
      if( preg_match( '#^\$([0-9]+),\$([0-9]+)$#', $args[0], $tmp ) ) return $this->Matches[$name][$tmp[1]][$tmp[2]];
      if( preg_match( '#^\$([1-9])$#', $args[0], $tmp ) )             return $this->Matches[$name][$tmp[1]];
      if( preg_match( '#^[^a-zA-Z0-9]#', $args[0], $tmp ) ){
        $tmp[1] = str_replace( '#', '\#', $tmp[1] );
        if( preg_match( '#' . $tmp[1] .'([a-z]*)$#', $args[0], $switches ) ){
          if( preg_match( '#m#', $switches[1] ) ){
            $args[0] = preg_replace( '#' . $tmp[1] . $switches[1] . '$#', str_replace( '\\', '', $tmp[1] ) . str_replace( 'm', '', $switches[1] ), $args[0] );
            return preg_match_all( $args[0], $this->values[$name], $this->Matches[$name] );
          }
          if( count( $args ) == 1 ) return preg_match( $args[0], $this->values[$name], $this->Matches[$name] );
          else                      return preg_replace( $args[0], $args[1], $this->values[$name] );
        }
      }
      $buf = $this->values[$name];
      if( is_array( $args[0] ) ){
        foreach( $args as $arg ){
          if( $arg[0] == 'esc'  )             $buf = $this->escapeDeeply( $buf );
          if( $arg[0] == 'fmtD' && $arg[1] )  $buf = $this->formatDate( $buf, $arg[1] );
          if( $arg[0] == 'fmtC' )             $buf = $this->formatCurrency( $buf );
          if( $arg[0] == 'half')              $buf = $this->toHalfContentDeeply( $buf );
          if( $arg[0] == 'cut' && preg_match( '#^\d+$#', $arg[1] ) ) $buf = $this->cutLongText( $buf, $arg[1] );
        }
      } else {
        if( $args[0] == 'esc'  )             $buf = $this->escapeDeeply( $buf );
        if( $args[0] == 'fmtD' && $args[1] ) $buf = $this->formatDate( $buf, $args[1] );
        if( $args[0] == 'fmtC' )             $buf = $this->formatCurrency( $buf );
        if( $args[0] == 'half')              $buf = $this->toHalfContentDeeply( $buf );
        if( $args[0] == 'cut' && preg_match( '#^\d+$#', $args[1] ) ) $buf = $this->cutLongText( $buf, $args[1] );
      }
      return $buf;
    }
  }

  public function getContentType(){
		$def = aafwMobileDispatcher::isMobile( $_SERVER );
		$content_type = "";
		if ($def['is_mobile']){
			if ( $def['regex'] == "#^DoCoMo#i" ) $content_type = $def['content-type'];
			else                                 $content_type = 'text/html';
			$content_type .= '; charset=Shift_JIS';
		} else{
    	$content_type = 'text/html';
		}
    return $content_type;
  }

  /***************************
   * 使いようが無いtrue返すだけ
   ****************************/
  public function in($data) {
    if(preg_match( '/^http:\/\/.+?\.php\??.*/' ,$data ) )
      return file_get_contents($data);
    else
      return true;
  }

  /***************************
   * $data['__view__']に指定されたPHPが
   * 吐き出すべき値を返す
   ****************************/
  public function out( $data ) {
    if( !is_array( $data ) ) throw new Exception('PHPParser.out : 引数は配列で');
    $view = $data['__view__'];
    $params = $data['__REQ__'];
    unset( $data['__view__'] );
    unset( $data['__REQ__'] );
    $this->action = $data['__ACTION__'];
    $this->values = $data;
    $this->params = $params;
    ob_start();
    include $view;
    return ob_get_clean();
  }

  public function toHidden( $src ) {
    $ret = array();
    foreach( $src as $key => $value ) {
      if( !isset ( $value )  ) continue;
      if( $key === 'PHPSESSID' ) continue;
      if( $key === 'DSN' ) continue;
      $key = htmlspecialchars( $key, ENT_QUOTES );
      if( is_array( $value ) ) {
        foreach( $value as $elm ) {
          if(  !isset ( $elm  ) ) continue;
          $elm = htmlspecialchars( $elm, ENT_QUOTES );
          $ret[] = "<input type='hidden' name='${key}[]' value='$elm' />";
        }
      } else {
        $value = htmlspecialchars( $value, ENT_QUOTES );
        $ret[] = "<input name='$key' type='hidden' value='$value' />";
      }
    }
    return join( "\n" ,$ret );
  }


  /******************************************
   * 配列を再帰的に掘っていって片っ端からHTMLのエスケープ
   * @param $src ハッシュ
   * @return QueryString
   ******************************************/
  public function escapeDeeply( $data ){
    return $this->deep( $data, '$x', 'return is_object( $x ) ? $x : htmlspecialchars( $x, ENT_QUOTES );' );
  }

  /******************************************
   * 配列を再帰的に掘っていって片っ端からHTMLのデコード
   * @param $src ハッシュ
   * @return QueryString
   ******************************************/
  public function unescapeDeeply( $data ){
    return $this->deep( $data, '$x', 'return is_object( $x ) ? $x : htmlspecialchars_decode( $x, ENT_QUOTES );' );
  }

  /******************************************
   * 文字列をカンマ編集する
   * @param $str 文字
   * @return カンマ編集した文字
   ******************************************/
  public function formatCurrency( $str ){
    if ( !$str ) return 0;
    return strrev( preg_replace( '/(\d{3})(?=\d)/', '$1,', strrev( $str ) ) );
  }

  /******************************************
   * 日付っぽい文字列に変換
   * @param $str 文字
   * @return 日付っぽい
   ******************************************/
  public function formatDate( $str, $tmpl = 'YYYY年MM月DD日' ){
    $org = $str;
    $str = strtotime( preg_replace( array( '#(?:年|月)#', '#(?:時|分)#', '#(?:日|秒)#' ), array( '-', ':', '' ), $str ) );
    return str_replace(
      array( 'YYYY', 'YY', 'MM', 'DD', 'II' , 'H', 'I', 'S' ),
      array(
        date( 'Y', $str ),
        substr( date('Y', $str ), 2, 2 ) ,
        date( 'm', $str ),
        date( 'd', $str ),
        $this->getYoubi ( $org ) ,
        date( 'H', $str ),
        date( 'i', $str ),
        date( 's', $str )
      ), $tmpl );
  }

  /************************************
   * 曜日文字列を返す
   * @param 日付文字列
   * @return 曜日
   ************************************/
  public function getYoubi($date){
    $sday = strtotime($date);
    $res = date("w", $sday);
    $day = array("日", "月", "火", "水", "木", "金", "土");
    return $day[$res];
  }

  /******************************************
   * PHPを普通にパースする
   * @param テンプレートのパス
   * @param データ
   * @return パースした結果
   */
  public function parseTemplate( $tmpl, $data = null ){
    ob_start();
    include( AAFW_DIR . '/templates/' .  $tmpl );
    return ob_get_clean();
  }

  /**
   * PHPを普通にパースする（モバイル）
   * @param テンプレートのパス
   * @param データ
   * @return パースした結果
   */
  public function parseTemplateM( $tmpl, $data = null ){
    ob_start();
    include( AAFW_DIR . '/m_views/' .  $tmpl );
    return ob_get_clean();
  }

  /**
   * PHPを普通にパースする
   * @param テンプレートのパス
   * @param データ
   * @return パースした結果
   */
  public function parseTemplateX ( $tmpl, $data = null ){
    $def = aafwMobileDispatcher::isMobile( $_SERVER );
    $config = new aafwConfig ();
    $path = '';
    if     ( $def['is_smart']  && is_file ( $config->SmartTemplatePath  . '/' . $tmpl ) ) return $this->parseTemplateS ( $tmpl, $data );
    elseif ( $def['is_mobile'] && is_file ( $config->MobileTemplatePath . '/' . $tmpl ) ) return $this->parseTemplateM ( $tmpl, $data );
    else                                                                                  return $this->parseTemplate ( $tmpl, $data );
  }

  /******************************************
   * PHPを普通にパースする（スマート）
   * @param テンプレートのパス
   * @param データ
   * @return パースした結果
   ******************************************/
  public function parseTemplateS( $tmpl, $data = null ){
    ob_start();
    $config = new aafwConfig ();
    include( $config->SmartTemplatePath . '/' . $tmpl );
    return ob_get_clean();
  }

  /******************************************
   * ハッシュから一部のハッシュを取り出す
   * @param $src ハッシュ,可変長
   * @return ハッシュ
   ******************************************/
  public function hashSlice(){
    $ret = array();
    $arg = func_get_args();
    $arr = array_shift( $arg );
    if ( count ( $arg )  == 1 && $arg[0] ) $arg = $arg[0];
    foreach( $arg as $key ) {
      if( isset ( $arr[$key] ) ) $ret[$key] = $arr[$key];
    }
    return $ret;
  }

  /*****************************************
   * URLをリンクに改行を<br />に置換
   *****************************************/
  public function toHalfContentDeeply( $data ){
    if( is_array( $data ) ){
      foreach( array_keys( $data ) as $i ){
        $data[$i] = $this->toHalfContentDeeply( $data[$i] );
      }
      return $data;
    } else {
      $data = $this->toHalfContent( $data );
      if( is_array( $data ) ) return $this->toHalfContentDeeply( $data);
      else                    return $data;
    }
    return $this->toHalfContentDeeply( $data );
  }

  public function nl2brAndHtmlspecialchars($string){
  	if( is_object( $string ) ) return $string;
    $string = html_entity_decode( $string );
    $string = str_replace( "\n", "[[br]]", $string );
    $string = htmlspecialchars( $string, ENT_QUOTES );
    $string = str_replace( "[[br]]", "<br />", $string );
    return $string;
  }

  private function toHalfContent( $x , $mode = true){
    if( is_object( $x ) ) return $x;
    $x = html_entity_decode( $x );
    $x = str_replace( "\n", "[[br]]", $x );
    $urls = array();
    if( preg_match_all( '#(https?://[0-9a-zA-Z-_\.@/\?&=~\#%+;\,]+)#', $x, $tmp ) ){
      $i = 0;
      uasort ( $tmp[1] , create_function (
        '$a,$b',
				'return strlen( $a ) == strlen( $b ) ? 0 : ( strlen( $a ) > strlen( $b ) ? -1 : 1 );'
        ));
      foreach( $tmp[1] as $url ){
        if( preg_match( '#[\'"(]' . preg_quote($url, '#') . '[)\'"]#', $x ) && $mode ) continue;
        $x =  str_replace(
          $url,
          '#url_' . $i .'#',
          $x
          );
        $urls['#url_' . $i .'#'] = $this->getDomainName( $_SERVER['SERVER_NAME'] ) == $this->getDomainName( $url ) ? "<a href=\"$url\">$url</a>" : "<a href=\"$url\" target=\"_blank\">$url</a>";
        $i++;
      }
    }
    $x = htmlspecialchars( $x, ENT_QUOTES );
    foreach( $urls as $key => $value )  $x = str_replace( $key, $value, $x );
    $x = str_replace( "[[br]]", "<br />", $x );

    return $x;
  }


  public function toHalfContentNext($x) {
        if( is_object( $x ) ) return $x;
        $x = html_entity_decode($x);
        $x = str_replace( "\n", "\x0bbr\x0b", $x );
        $url_regex = 'https?://[0-9a-zA-Z-_\.@/\?&=~\#%+;\,]+';
        $domain = $this->getDomainName( $_SERVER['SERVER_NAME'] );
        $urls = array();
        if ( preg_match_all ( '#\{(' . $url_regex . ') +: +(\S+?)\}#', $x, $tmp ) ){
            uasort ($tmp[1], function($a,$b){
                if     (strlen($a) == strlen($b)) return 0;
                elseif (strlen($a) >  strlen($b)) return -1;
                else                              return 1;
            });
            for ($i = 0; $i < count($tmp[1]); $i++) {
                $all   = $tmp[0][$i];
                $url   = $tmp[1][$i];
                $label = $tmp[2][$i];
                if (preg_match('#[\'"]' . preg_quote($url, '#') . '[\'"]#', $x)) continue;
                $x =  str_replace ($all, "\x0burl_all_$i\x0b", $x);
                $urls["\x0burl_all_$i\x0b"] = $domain == $this->getDomainName( $url ) ? "<a href=\"$url\">$label</a>" : "<a href=\"$url\" target=\"_blank\">$label</a>";
            }
        }
        if ( preg_match_all ( '#(' . $url_regex . ')#', $x, $tmp ) ){
            uasort ($tmp[1], function($a,$b){
                if     (strlen($a) == strlen($b)) return 0;
                elseif (strlen($a) >  strlen($b)) return -1;
                else                              return 1;
            });
            for ($i = 0; $i < count($tmp[1]); $i++) {
                $url = $tmp[1][$i];
                if (preg_match('#[\'"]' . preg_quote($url, '#') . '[\'"]#', $x)) continue;
                $x =  str_replace ($url, "\x0burl_$i\x0b", $x);
                $urls["\x0burl_$i\x0b"] = $domain == $this->getDomainName( $url ) ? "<a href=\"$url\">$url</a>" : "<a href=\"$url\" target=\"_blank\">$url</a>";
            }
        }
        $x = htmlspecialchars( $x, ENT_QUOTES );
        if(is_array($urls)) foreach( $urls as $key => $value )  $x = str_replace( $key, $value, $x );
        $x = str_replace( "\x0bbr\x0b", "<br />", $x );
        return $x;
    }


  /*********************************
   * 拡張子を抽象的にしてファイルを取得する
   * @param ディレクトリパス
   * @param 拡張子を除いたファイル名
   * @param 優先度を決めた配列、小文字、大文字の順で見ていく
   *********************************/
  public function getSomeFile( $path, $fn ){
    $order = array( 'jpg', 'jpeg', 'gif', 'png' );
    clearstatcache();
    if( !( $x = @opendir( $path ) ) ){
      return '';
    }
    if( !$fn ) return '';
    $path = preg_replace( '#/$#'  , '', $path );
    $fn   = preg_replace( '#\.$#', '', $fn   );
    $dir  = opendir( $path );
    while( $elm = readdir( $dir ) ){
      if( preg_match( '#^\.+$#', $elm ) ) continue;
      foreach( $order as $ext ){
        if( $elm == ( "$fn." . strtolower( $ext ) ) ) return "$path/$fn." . strtolower( $ext );
        if( $elm == ( "$fn." . strtoupper( $ext ) ) ) return "$path/$fn." . strtoupper( $ext );
      }
    }
    return '';
  }

  /*********************************
   * あったら対象のファイルを返す、無ければno_imageを返す
   * @param ファイルのパス
   * @param no_imageのパス
   *********************************/
  public function nvlFile( $file, $no_image, $ret_as_root = true ){
    clearstatcache();
    if( is_file( $file ) ) return $ret_as_root ? ( '/' . $file )     : $file;
    else                   return $ret_as_root ? ( '/' . $no_image ) : $no_image;
  }

  /*********************************
   * 指定されたサイズで文字を切って...を付ける
   * @param 文字
   * @param 文字数
   * @param ...が嫌な場合に指定する
   *********************************/
  public function cutLongText( $text, $count,$adding = '...',$strip_flg=true ){
	if($strip_flg) {
		$text = trim( strip_tags( $text ));
	    $text = preg_replace( '#\s+#', ' ', $text );
	}
    if( $count >= mb_strlen( $text, 'UTF-8' ) ) return $text;
    return mb_substr( $text, 0, $count, 'UTF-8' ) . $adding ;
  }

  /*******************************************
   * input type="text"を作る
   ********************************************/
  public function formText( $name, $value = '' ,$attr = array() ){
    if ( $value == self::ACTION_FORM ) $value = $this->getActionFormValue ( $name );
    return '<input type="text" name="' .  $name  . '" value="' . $this->escapeDeeply( $value ) . '" ' . $this->getAttributes( $attr ) . ' />';
  }

  /*******************************************
   * input type="password"を作る
   ********************************************/
  public function formPassword( $name, $value = '' ,$attr = array() ){
    if ( $value == self::ACTION_FORM ) $value = $this->getActionFormValue ( $name );
    return '<input type="password" name="' .  $name  . '" value="' . $this->escapeDeeply( $value ) . '" ' . $this->getAttributes( $attr ) . ' />';
  }

	/*******************************************
   * input type="number"を作る
   ********************************************/
  public function formNumber( $name, $value = '' ,$attr = array() ){
      if ( $value == self::ACTION_FORM) $value = $this->getActionFormValue ( $name );
    return '<input type="number" name="' .  $name  . '" value="' . $this->escapeDeeply( $value ) . '" ' . $this->getAttributes( $attr ) . ' />';
  }

	/*******************************************
   * input type="email"を作る
   ********************************************/
  public function formEmail( $name, $value = '' ,$attr = array() ){
      if ( $value == self::ACTION_FORM) $value = $this->getActionFormValue ( $name );
    return '<input type="email" name="' .  $name  . '" value="' . $this->escapeDeeply( $value ) . '" ' . $this->getAttributes( $attr ) . ' />';
  }

	/*******************************************
   * input type="tel"を作る
   ********************************************/
  public function formTel( $name, $value = '' ,$attr = array() ){
      if ( $value == self::ACTION_FORM) $value = $this->getActionFormValue ( $name );
    return '<input type="tel" name="' .  $name  . '" value="' . $this->escapeDeeply( $value ) . '" ' . $this->getAttributes( $attr ) . ' />';
  }

  /*******************************************
   * textareaタグを作る
   ********************************************/
  public function formTextArea( $name, $value = '' ,$attr = array() ){
    if ( $value == self::ACTION_FORM ) $value = $this->getActionFormValue ( $name );
    return '<textarea name="' .  $name  .  '"' . $this->getAttributes( $attr ) . '>' . $this->escapeDeeply( $value ) . '</textarea>' ;
  }

  /*******************************************
   * hiddenタグを作る
   ********************************************/
  public function formHidden( $name, $value, $attr = array() ){
    if ( $value == self::ACTION_FORM ) $value = $this->getActionFormValue ( $name );
    return '<input type="hidden" name="' .  $name  . '" value="' . $this->escapeDeeply( $value ) . '" ' . $this->getAttributes( $attr ) . ' />';
  }

  /*******************************************
   * ラジオボタングを作る
   ********************************************/
  public function formRadio( $name, $value, $attr = array(), $options = array(), $attrLabel = array(), $sep = '&nbsp;', $escape = true){
    if ( $value == self::ACTION_FORM ) $value = $this->getActionFormValue ( $name );
    $buf = '';
    if(!is_array($attrLabel)) $attrLabel = array();
    if(!$sep) $sep = '&nbsp;';
    foreach( $options as $key => $row ){
      $buf .= '<input type="radio" id="'. $name . '_' . $key . '" name="' .  $name  . '" value="' . $this->escapeDeeply( $key ) . '" ' . $this->getAttributes( $attr ) . ' ' . ( !is_null( $value ) && $value != '' && $value == $key ? 'checked="checked"' : '' ) . ' />';
      $buf .= '<label for="' . $name . '_' . $key . '" ' . $this->getAttributes( $attrLabel ) . '>' . ($escape ? htmlspecialchars($row) : $row) . '</label>' . $sep;
    }
    return $buf;
  }
  /*******************************************
   * チェックボックスを作る
   ********************************************/
  public function formCheckBox( $name, $value, $attr = array(), $options = array(), $attrLabel = array(), $sep = '&nbsp;', $escape = true){
    if ( $value == self::ACTION_FORM ) $value = $this->getActionFormValue ( $name );
    $buf = '';
    if(!is_array($attrLabel)) $attrLabel = array();
    if(!$sep) $sep = '&nbsp;';
    foreach( $options as $key => $row ){
      $buf .= '<input type="checkbox" id="'. $name . '_' . $key . '" name="' .  $name  . (count( $options ) > 1 ? '[]' : '' ) . '" value="' . $this->escapeDeeply( $key ) . '" ' . $this->getAttributes( $attr ) . ' ' . ( is_array( $value ) && in_array( $key, $value )  ? 'checked="checked"' : '' ) . ' />';
      $buf .= '<label for="' . $name . '_' . $key . '" ' . $this->getAttributes( $attrLabel ) . '>' . ($escape ? htmlspecialchars($row) : $row) . '</label>' . $sep;
    }
    return $buf;
  }
  /*******************************************
   * セレクトボックス
   ********************************************/
  public function formCheckBox2( $name, $value, $attr = array(), $options = array(), $attrLabel = array(), $sep = '&nbsp;', $escape = true){
    if ( $value == self::ACTION_FORM ) $value = $this->getActionFormValue ( $name );
    $buf = '';
    if(!is_array($attrLabel)) $attrLabel = array();
    if(!$sep) $sep = '&nbsp;';
    foreach( $options as $key => $row ){
       $id = uniqid ();
      $buf .= '<input type="checkbox" id="' .$id . '" name="' .  $name  . (count( $options ) > 1 ? '[]' : '' ) . '" value="' . $this->escapeDeeply( $key ) . '" ' . $this->getAttributes( $attr ) . ' ' . ( is_array( $value ) && in_array( $key, $value ) || $key == $value ? 'checked="checked"' : '' ) . ' />';
      $buf .= '<label for="' . $id . '" ' . $this->getAttributes( $attrLabel ) . '>' . ($escape ? htmlspecialchars($row) : $row) . '</label>' . $sep;
    }
    return $buf;
  }
  /*******************************************
   * アイコン付きセレクトボックス
   ********************************************/
  public function formCheckBox3( $name, $value, $attr = array(), $options = array(), $icons = array(), $attrLabel = array(), $sep = '&nbsp;', $escape = true){
    if ( $value == self::ACTION_FORM ) $value = $this->getActionFormValue ( $name );
    $buf = '';
    if(!is_array($attrLabel)) $attrLabel = array();
    if(!$sep) $sep = '&nbsp;';
    foreach( $options as $key => $row ){
      $id = uniqid ();
      $buf .= '<input type="checkbox" id="' .$id . '" name="' .  $name  . (count( $options ) > 1 ? '[]' : '' ) . '" value="' . $this->escapeDeeply( $key ) . '" ' . $this->getAttributes( $attr ) . ' ' . ( is_array( $value ) && in_array( $key, $value ) || $key == $value ? 'checked="checked"' : '' ) . ' />';
      $buf .= '<label for="' . $id . '" ' . $this->getAttributes( $attrLabel ) . '>' . '<img src="' . $icons[$key] . '"/>' . ($escape ? htmlspecialchars($row) : $row) . '</label>' . $sep;
    }
    return $buf;
  }

  /*******************************************
   * Selectタグを作る
   ********************************************/
  public function formSelect( $name, $value, $attr = array(), $options = array() ){
    if ( $value == self::ACTION_FORM) $value = $this->getActionFormValue ( $name );
        $buf = '';
    $buf .= '<select name="' . $name . '" ' . $this->getAttributes( $attr ) .  '>';
    foreach( $options as $key => $row ){
      if(is_array($value)){
        $selected = "";
        foreach($value as $val){
            if($val == $key){
                $selected = ' selected="selected"';
                break;
        }
      }
        $buf .= '<option value="' . $this->escapeDeeply( $key ). '"' . $selected . '>' . htmlspecialchars( $row, ENT_QUOTES ) . '</option>';
      }else{
        $buf .= '<option value="' . $this->escapeDeeply( $key ). '"' . ( !is_null( $value ) && $value != '' && $value == $key  ?  ' selected="selected"'  : '' ) . '>' . htmlspecialchars( $row, ENT_QUOTES ) . '</option>';
      }
    }
    $buf .= '</select>';
    return $buf;
  }

    /*******************************************
     * 静的なファイルのバージョンを生成する
     ********************************************/
    public function setVersion($path) {
        if (!$path) {
            return;
        }
        $baseUrl = aafwApplicationConfig::getInstance()->query('Static.Url');
        return $baseUrl.$path.'?'.filemtime(dirname( __FILE__ ).'/../../../docroot_static'.$path);
    }

    /*******************************************
     * サービスのスクリプトを書く
     ********************************************/
    public function scriptTag($script_name, $isService = true) {
      $base = '/js/';
        if (DEBUG) {
          if ($isService) {
            $base .= 'brandco/services/';
          }
          $script_url = $base . $script_name . '.js';
        } else {
          if ($isService) {
            $base .= 'brandco/dest/';
          } else {
            $base .= 'min/';
          }
          $script_url = $base . $script_name . '.min.js';
        }
        return '<script type="text/javascript" src="'.$this->setVersion($script_url).'"></script>';
    }

  /*******************************************
   * attributeを作る
   ********************************************/
  private function getAttributes( $attr ){
    $str = '';
    foreach( $attr as $key => $value ){
      if( $value == '' ) continue;
      $str .= ' ' .  $key .'="';
      if( is_array( $value ) ){
        // まあ、あんまり使わないで
        if( preg_match( '#^on#', $key ) ) $str .= $value;
        else                              $str .= join ( ' ', $this->escapeDeeply( $value ) );
      } else {
        if( preg_match( '#^on#', $key ) ) $str .= $value;
        else                              $str .= $this->escapeDeeply( $value );
      }
      $str .='"';
    }
    return $str;
  }
  public function showError( $key, $error_key = 'error', $tmpl = '<p style="color:red">{val}</p>' ){
    $errors = $this->values[$error_key];
		if( $errors[$key] ){
			if( is_array( $errors[$key] )){
				$err_msg = '';
				foreach( $errors[$key] as $val ){
					$err_msg .= str_replace( '{val}', $val, $tmpl );
				}
				return $err_msg;
			} else{
				return str_replace( '{val}', $errors[$key], $tmpl );
			}
		}
    return '';
  }

  ///
  /// こっから下はよく分からん^^;
  ///
  public function mob_link ( $url, $label = null, $params = array() ) {
    // QueryStringの生成
    !$params['q']['PHPSESSID'] && $params['q']['PHPSESSID'] = session_id();

    if ( preg_match ( '#^/#', $url ) || $this->getDomainName( $url ) == SC_DOMAIN ) {
      $q   = $this->toQueryString ( $params['q'] );
    } else {
      !$params['q']['redirect_to'] && $params['q']['redirect_to'] = $url;
      $q   = $this->toQueryString ( $params['q'] );
      $url = '/redirector';
    }

    $url .= ( preg_match ( '#\?#', $url ) ? '&': '?' ) . $q;
    $tag = '<a href="' . $url. '"';
    if ( $params['attrs'] ) $tag .= $this->getAttributes ( $params['attrs'] );
    $tag .= '>';
    if ( $label ) $tag .= htmlspecialchars ( $label, ENT_QUOTES );
    else          $tag .= $url;
    $tag .= '</a>';
    return $tag;
  }

    /**
     * ActionFormの値を取得する
     * @param ActionFormの名前 - 配列の入れ子はイケる(間接参照は無理)
     * @return 値
     */
    public function getActionFormValue ( $name ) {
        $value = null;
        if ( !preg_match ( '#^([^\[]+)\[.+?\]#', $name, $tmp ) ) return !is_null ( $this->ActionForm[$name] ) ? $this->ActionForm[$name] : null;
        if ( !$this->ActionForm[trim($tmp[1])] )                 return null;
        $action_form = $this->ActionForm[trim($tmp[1])];
        $name = preg_replace ( '#^[^][]+#' , '', $name );
        for ( $i = 0, $len  = mb_strlen ( $name, 'UTF8' ); $i < $len;  $i++ ) {
            $char = mb_substr ( $name, $i, 1 );
            if ( preg_match ( '#\s#', $char ) ) continue;
            if ( $char == "[" ) {
                $buf = '';
                for ( $i++; $i < $len; $i++ ) {
                    $char = mb_substr ( $name, $i, 1 );
                    if ( $char == "]" ) { $action_form = $action_form[$buf]; break; }
                    else                { $buf .= $char; }
                }
            }
            else  {
                throw new Exception ( 'Syntax Error' );
            }
        }
        return $action_form;
  }

  public function csrf_tag() {
		$csrf_token = hash('sha256',ProcessCsrfToken::CSRF_SALT . session_id());
		return $this->formHidden("csrf_token", $csrf_token);
  }

  public function getAction() {
    return $this->action;
  }
}
