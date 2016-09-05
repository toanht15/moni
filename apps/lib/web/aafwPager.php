<?php
AAFW::import ( 'jp.aainc.aafw.web.aafwWidgetBase' );
class aafwPager extends aafwWidgetBase {
  private $_Mark = "\x0bpage\x0b";
  public function doService ( $params ) {
    if ( !$params['TotalCount'] ) return ;
    if ( !$params['PageLimit'] )           $params['PageLimit']   = 5;
    if ( intval ( $params['Count'] ) < 1 ) $params['Count']       = 20;
    if ( !$params['URLBase'] )             $params['URLBase']     = $this->analyzeURLBase ( $_SERVER['REQUEST_URI'] );
    if ( !$params['CurrentPage'] )         $params['CurrentPage'] = $this->analyzeCurrentPage ( $params['URLBase'], $_SERVER['REQUEST_URI'] );

    $params['TotalPage'] = $this->calcTotalPage ( $params['TotalCount'], $params['Count'] );
    $params['Start'] = $this->calcStart ( $params['CurrentPage'], $params['PageLimit'], $params['TotalPage'] );
    $params['End']   = $this->calcEnd   ( $params['CurrentPage'], $params['PageLimit'], $params['TotalPage'] );
    return $params;
  }

  /**
   * 総ページを計算する
   * @param 総件数
   * @param 1ページ辺りの件数
   * @return 総ページ数
   */
  public function calcTotalPage (  $total_count, $count ) {
    if ( !$count  ) return 0;
    return floor ( $total_count / $count ) + ( $total_count % $count > 0 );
  }

  /**
   * ページャーに表示する開始位置を設定する
   * @param 現在ページ
   * @param 1度に表示するページ数
   * @param 総ページ
   * @return 開始位置
   */
  public function calcStart ( $current, $page_limit, $total_page ) {
      if($page_limit % 2) {
          $start = $current - floor ( $page_limit / 2 );
      } else{
          $start = $current - $page_limit / 2 + 1;
      }

      if($start < 1){
          // スタートが1未満なら1を返す
          return 1;
      }

      $end   = $this->calcEnd ( $current, $page_limit , $total_page );
      if( $page_limit - ( $end - $start + 1 ) > 0 ){
          // $endが末尾に到達しているときの、$start位置を求める。
          return ($total_page - $page_limit > 0) ? $total_page - $page_limit + 1 : 1;
      }

      return $start;
  }

  /**
   * ページャーに表示する最終位置を設定する
   * @param 現在ページ
   * @param 1度に表示するページ数
   * @param 総ページ
   * @return 開始位置
   */
  public function calcEnd ( $current, $page_limit, $total_page ) {
    $end = $current + floor ( $page_limit / 2 );
    if ( $total_page < $page_limit) $page_limit = $total_page;
    if     ( $end >=  $total_page ) return $total_page;
    elseif ( $end <=  $page_limit ) return $page_limit;
    else                            return $end;
  }

  /**
   * ページャーに渡すURLのテンプレートを作成する
   * @param 現在のURL
   * @return テンプレート
   */
  public function analyzeURLBase ( $url ) {
    $urls = parse_url ( $url );
    parse_str ( $urls['query'], $tmp );
    $tmp['p'] = "\x0bpage\x0b";
    $query = array ();
    $keys  = array_keys ( $tmp );
    sort ( $keys );
    foreach ( $keys as $key ) {
      if ( $key == 'p' ) {
          $query[] = urlencode ( $key ) . '=' . $tmp[$key];
      } else {
          if (is_array($tmp[$key])){
              foreach($tmp[$key] as $value){
                  $query[] = urlencode ( $key . '[]' ) . '=' . urlencode ( $value );
              }
          }else{
             $query[] = urlencode ( $key ) . '=' . urlencode ( $tmp[$key] );
          }
      }
    }
    return  $urls['path'] . '?' . join ( '&', $query );
  }

  /**
   * ページャーのテンプレートから現在のページを算出する
   * @param テンプレート
   * @param 現在のURL
   * @return 現在のページ
   */
  public function analyzeCurrentPage ( $base_url, $url ) {
    $terminate_char = '';
    $result = '';
    for ( $i = 0; $i < strlen ( $base_url ); $i++ ) {
      if ( $base_url[$i] == "\x0b" && substr ( $base_url, $i, strlen ( $this->_Mark ) ) == $this->_Mark ) {
        $terminate_char = substr ( $base_url, $i + strlen ( $this->_Mark ), 1 );
        for ( ;$i  < strlen ( $url ); $i++ ) {
          if ( $url[$i] == $terminate_char ) break;
          $result .= $url[$i];
        }
        break;
      }
    }
    if ( intval ( $result ) < 1 )  $result = 1;
    return $result;
  }
}
