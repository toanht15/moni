<?php
/**
 * へなちょこテンプレートエンジン
 * @author ishida@aainc.co.jp
 * @data 2010/07/21
 **/
class aafwTemplateTag {
  private $Data    = null;
  private $Content = '';
  private $Plugin  = array();
  /**
   * コンストラクタ
   * @param 本文
   * @param データ
   **/
  public function __construct( $content, $data = null ){
    if ( is_file ( $content ) ) $this->Content = mb_convert_encoding ( file_get_contents ( $content ), 'UTF8', 'sjis,utf8,euc-jp' );
    else                        $this->Content = $content;
    $this->Data    = $data;
    // プラグインのロード
    foreach ( array ( 'if', 'loop', 'value' ) as $x ) {
      $base_name = dirname ( __FILE__ ) . '/template_tag_plugin/' . $x;
      $d = opendir ( $base_name );
      $this->Plugin[$x] = array();
      while ( $f = readdir( $d ) ) {
        if ( !is_file ( $base_name . '/' . $f ) ) continue;
        if ( preg_match ( '#^\.+$#', $f ) )       continue;
        if ( !preg_match ( '#\.php$#', $f ) )     continue;
        require_once $base_name . '/' . $f;
        $class = preg_replace ( '#\.php$#', '', $f );
        $obj = new $class ();
        if ( $obj->getPluginType() != $x ) continue;
        $this->Plugin[$x][$obj->getAttrName()] = $obj;
      }
      closedir( $d );
    }
  }

  /**
   * タグ解釈
   * @param 開始インデックス
   * @param データ
   * @param 開始インデックス
   * @param タグ名称
   * @param 評価モード( 1:ちゃんとパースする, 0: インデックス進めるだけ )
   * @return パースの結果
   **/
  public function evalTag  ( $current_data = null, $idx = 0, $end_name = '', $can_eval = 1 ) {
    list ( $in_tags, $buf, $ret, $stack, $quote ) = array ( false, '', '', array(), null );
    if ( is_null ( $this->Data ) )    $this->Data   = $current_data;
    if ( is_null ( $current_data ) )  $current_data = $this->Data;
    for( $i = $idx; $i < mb_strlen ( $this->Content, 'UTF8' ); $i++ ){
      $char = mb_substr ( $this->Content, $i, 1, 'UTF8' );

      if ( $quote ){
        if ( $char == '\\' ){
          $buf .= $char;
          $buf .= $this->Content[++$i];
        }
        // 引用符終了のお知らせ
        elseif ( $quote == $char ){
          $quote = null;
          $buf .= $char;
        }
        // 引用符中なので無条件にappend
        else {
          $buf .= $char;
        }
      }

      // タグ開始のお知らせ
      elseif ( $char == '<' ){
        $ret    .= $buf;
        list ( $in_tags, $buf ) = array ( true , '' );
      }

      // クォート開始のお知らせ
      elseif ( $in_tags && ( $char == '"' || $char == "'" ) ){
        $quote = $char;
        $buf  .= $char;
      }

      // タグ終了のお知らせ
      elseif ( $in_tags && $char == '>' ) {
        // 閉じタグ
        if ( preg_match ( '/^#\/(.+)$/', $buf, $tmp ) ) {
          // 評価モードか未評価でもスタック空なら返却
          if ( $can_eval || !$stack ){
            if ( $tmp[1] == $end_name ) {
              if ( $can_eval ) return array( $i, $ret );
              else             return array( $i, '' );
            }
            // 不整合の場合にはこっそり戻す
            else { 
              $ret .= '<' . $buf . '>'; 
            }
          }
          // 未評価モードはスタック消化だけ
          elseif ( $stack[count( $stack ) -1] == $tmp[1] ) {
            array_shift ( $stack );
          }
          // 未評価でかつタグの不整合なので元に戻す
          else { 
            $ret .= '<' . $buf . '>'; 
          }
        }
        // ループタグ IFタグ
        elseif ( preg_match ( '/^#(LOOP|IF)_(.+)$/', $buf, $tmp ) ) {
          list ( $after_idx, $str ) = array ( $i, '' );
          $tag = $this->getTagInfo( $buf );
          // 評価モード
          if ( $can_eval ) {
            $name = preg_replace ( '#^.+?_#', '', $tag['tag_name'] );
            if     ( $tmp[1] == 'LOOP' ) list ( $after_idx, $str ) = $this->evalLoop ( $i, $name, $current_data, $tag['attrs'] );
            elseif ( $tmp[1] == 'IF' )   list ( $after_idx, $str ) = $this->evalIF   ( $i, $name, $current_data, $tag['attrs'] );
            else                         { /** LOOPでもIFでもない場合は無視でOK? **/ }
          }
          // 未評価モード
          else {
            $stack[] = $tag['tag_name'];
          }
          $ret .= $str;
          $i    = $after_idx;
        }
        // ELSEタグ
        elseif ( $buf == '#ELSE'  ){
          // 評価中ならEND まで未評価で進む
          if ( $can_eval ) {
            list ( $i ) = $this->evalTag ( $current_data, $i, $end_name, false );
            if ( $this->Content[$i+1] == "\n") $i++;
          }
          // 未評価ならENDまで評価捨てる
          else {
            $ret = '';
            $can_eval = true;
          }
        }
        // 置換タグ
        elseif ( preg_match ( '/^#/', $buf ) ) {
          $tag = $this->getTagInfo ( $buf );
          $tag_value = $current_data[preg_replace( '/^#/', '', $tag['tag_name'] )];
          foreach ( $tag['attrs'] as $row ){
            if ( !$this->Plugin['value'][$row['label']] ) continue;
            $tag_value =  $this->Plugin['value'][$row['label']]->doMethod( $row['value'], $tag_value );
          }
          $ret .= $tag_value;
        }
        // 知らんタグはそのままこっそり戻す
        else {
          $ret .= '<' . $buf . '>';
        }
        $buf = '';
        $in_tags = false;
      }
      // タグの外側はそのまま連結
      else {
        $buf .= $char;
      }
    }
    // 通常通りのend
    if ( !$end_name ) return $ret . $buf ;
    // タグ評価中 タグ不整合で終了の場合
    else              return array ( $i, $ret. $buf );
  }

  /**
   * IF処理
   * @param インデックス
   * @param キー名
   * @return パースの結果
   **/
  public function evalIF ( $idx, $name, $data, $attrs = array() ) {
    $tmp = $this->Data;
    foreach ( $data as $key => $val ) $tmp[$key] = $val;
    list ( $after, $cont ) = $this->evalTag (
      $tmp,
      $idx + 1 + ( mb_substr ( $this->Content, $idx + 1, 1, 'UTF8' ) == "\n" ? 1 : 0 ),
      'IF_' . $name,
      $tmp[$name] ? 1 : 0
    );
    return array ( $after + 1, $cont );
  }

  /**
   * ループ処理
   * @param 開始インデックス
   * @param ループ名
   * @return パースの結果
   **/
  public function evalLoop ( $idx, $name, $data, $attrs = array() ) {
    $ret = '';
    $after = $idx;
    if ( is_array ( $data[$name] ) ) {
      foreach ( $data[$name] as $key => $row ) {
        $tmp = $this->Data;
        if ( !is_array ( $row ) ) $row = array ( 'IDX' => $key  ,'VAL' => $row );
        foreach ( $row as $key2 => $val )  $tmp[$key2] = $val;
        $row = $tmp;
        list ( $after, $cont ) = $this->evalTag ( $row, $idx + 1 + ( mb_substr ( $this->Content, $idx + 1, 1, 'UTF8' ) == "\n" ? 1 : 0 ), 'LOOP_' . $name, 1 );
        $ret .= $cont;
      }
    } else {
      list ( $after, $cont ) = $this->evalTag ( array(), $idx + 1 + ( mb_substr ( $this->Content, $idx + 1, 1, 'UTF8' ) == "\n" ? 1 : 0 ) , 'LOOP_' . $name, 0 );
    }
    return array ( $after + 1 ,$ret );
  }

  /**************************************************
   * タグをパースする
   **************************************************/
  public function getTagInfo( $chars ){
    $in_quote = null;
    $attr     = '';
    $elms     = array();
    if( !preg_match( '#^\#(\S+)( ?.*)$#s', $chars, $tmp ) ) return $chars;
    $ret = array( 'tag_name' =>  $tmp[1], 'attrs' => array() );
    for( $i = 0; $i < mb_strlen( $tmp[2], 'UTF8' ); $i++ ){
      $char = mb_substr ( $tmp[2], $i, 1 );
      if ( $in_quote ) {
        if (  $in_quote && $char == '\\' ) {
          $attr .= $char . $tmp[2][++$i];
        }
        elseif (  $in_quote == $char )  {
          $in_quote = null;
          $attr .= $char;
        } else{
          $attr .= $char;
        }
      } else{
        if( !$in_quote && !trim( $char ) ){
          $attr && $elms[] = trim($attr);
          $attr = '';
        }
        elseif ( !$in_quote && ( $char == "'" || $char == '"' ) ) {
          $in_quote = $char;
          $attr    .= $char;
        }
        else {
          $attr .= $char;
        }
      }
    }
    if ( $attr ) $elms[] = $attr;
    foreach ( $elms as $attr ){
      $ret['attrs'][] = $this->parseAttr( $attr );
    }
    return $ret;
  }

  /**************************************************
   * Attributeをパースする
   * @param key="value"のペア
   **************************************************/
  public function parseAttr ( $chars ){
    $label = '';
    $value = '';
    $in_quote = 0;
    $esc      = 0;
    $buf = '';

    for( $i = 0; $i < strlen ( $chars ); $i++) {
      $char = $chars[$i];
      if( !$in_quote  && $char === ' ' ) continue;
      if( $esc === 1 ){
        $esc = 0;
        $buf = $buf . $char;
      } else{
        if( $in_quote === $char ){
          $in_quote = null;
        } else{
          if( $in_quote ){
            if( $char === '\\' )  $esc  = 1;
            else                  $buf = $buf . $char;
          } else {
            if( $char === "'" || $char === '"' ) {
              $in_quote = $char;
            } elseif( $char === '=' ) {
              $label  =  $buf;
              $buf    = '';
            } else {
              $buf = $buf . $char;
            }
          }
        }
      }
    }
    $value =  $buf;
    return array( 'label' => $label, 'value' => $value );
  }
}

