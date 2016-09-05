<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwException' );
AAFW::import ( 'jp.aainc.aafw.factory.aafwEntityStoreFactory' );
AAFW::import ( 'jp.aainc.aafw.factory.aafwServiceFactory' );
AAFW::import ( 'jp.aainc.aafw.factory.aafwLibraryFactory' );

/**
 * 多くのオブジェクトの元になるヤツ
 * 雑多なメソッドとかいっぱい持ってる(^^;)
 * 適切に分割したい(^^;)
 */
class aafwObject {
    protected $_Strict         = false;
    protected $_Fields         = array ();
    protected $_Values         = array ();
    protected $_OldValues      = array ();
    protected $_Config         = null;
    protected $_Models         = array ();
    protected $_Services       = array ();
    protected $_Libs           = array ();
    protected $_StoreFactory   = null;
    protected $_ServiceFactory = null;
    protected $_LibraryFactory = null;

    public function __construct () {
        $this->_StoreFactory   = new aafwEntityStoreFactory();
        $this->_ServiceFactory = new aafwServiceFactory();
        $this->_LibraryFactory = new aafwLibraryFactory();
    }

    /**
     * 設定ファイルオブジェクトを設定する
     * @param 設定ファイルオブジェクト
     */
    public function setConfig ( $config ) {
        $this->_Config = $config;
    }

    /**
     * 配列にして返す
     * @return 配列
     */
    public function toArray () {
        return $this->_Values;
    }

    /**
     * 配列にして返す
     * @return 配列
     */
    public function getValues () {
        return $this->toArray();
    }

    /**
     * キーを取得する
     * @return 配列
     */
    public function getKeys () {
        return array_keys ( $this->_Values );
    }

    /**
     * 定義に従って全件validate する
     * @return true / false
     */
    public function __get ( $key ) {
        return $this->_Values[$key];
    }

    /**
     * 定義に従って全件validate する ( override )
     * @return true / false
     */
    public function __set ( $key, $value ) {
        if ( $this->_Strict && $this->Fields[$key] && !$this->_validate ( $key, $value ) )  {
            throw new aafwException ( "$key is invalid value [$value]" );
        }

        if ( !isset ( $this->_Values[$key] ) ) $this->_OldValues[$key] = $value;
        $this->_Values[$key] = $value;
    }

    /**
     * 定義に従って全件validate する
     * @return true / false
     */
    public function getOldValue ( $key ) {
        return $this->_Values[$key];
    }

    /**
     * 定義に従って全フィールドをvalidate する
     */
    protected function _validate ( $key, $value ) {
        $def = $this->_Fields[$key];
        if     ( $def['require'] && !$value )                       return false;
        if     ( $def['type'] == 'Num'  && !is_numeric ( $value ) ) return false;
        elseif ( $def['type'] == 'date' && !strtotime ( $value ) )  return false;
        elseif ( $def['type'] == 'Str' )                            return false;
        return true;
    }

    /**
     * ただのエイリアス
     */
    public function convertName( $str ){
        return $this->convertLowerStyle ( $str );
    }

    /**
     * アッパー を アンスコ区切りに変換する
     * @param 文字列
     * @return アンスコ区切り文字列
     */
    public function convertLowerStyle ( $str ) {
        if ( preg_match ('#[A-Z]#', $str ) ) {
            $ret = '';
            $count = strlen( $str );
            for( $i = 0; $i < $count; $i++ ){
                if( !$i ) {
                    $ret .= strtolower( $str[$i] );
                } else {
                    if( preg_match( '#[A-Z]#', $str[$i] ) ) $ret .= '_';
                    $ret .= strtolower( $str[$i] );
                }
            }
        } else {
            $ret = $str;
        }
        return $ret;
    }

    /**
     * アンスコ区切りをアッパーキャメルに
     * @param アンスコ区切り文字列
     * @return アッパーキャメル文字列
     */
    public function convertCamel ( $str ) {
        $ret = '';
        if ( preg_match ('#[a-z]#', $str ) ) {
            $count = strlen ( $str );
            for( $i = 0; $i < $count; $i++ ){
                if     ( !$i )                           $ret .= strtoupper ( $str[$i] );
                elseif ( preg_match( '#_#', $str[$i] ) ) $ret .= strtoupper ( $str[++$i] );
                else                                     $ret .= strtolower ( $str[$i] );
            }
        }
        else {
            $ret = $str;
        }
        return $ret;
    }

    /**
     * 複数形を単数形に
     * @param 複数形の文字列
     * @return 単数形の文字列
     */
    public function convertManyToOne ( $str ) {
        $self = $this;
        return preg_replace_callback ( '#(accesses|ges|ses|tes|les|des|ies|es|s)$#i',  function ( $m ) use ( $self ){
            $m[1] = strtolower ( $m[1]  );
            if ( $m[1] == 'accesses' ) return $self->convertCamel ( 'access' );
            elseif ( $m[1] == 'ies' )   return 'y';
            elseif ( $m[1] == 'ses' )   return 'se';
            elseif ( $m[1] == 'ges' )   return 'ge';
            elseif ( $m[1] == 'tes' )   return 'te';
            elseif ( $m[1] == 'des' )   return 'de';
            elseif ( $m[1] == 'les' )   return 'le';
            else                        return '';
        }, $str );
    }

    /**
     * 単数形を複数形に
     * @param 単数形の文字列
     * @return 複数形の文字列
     */
    public function convertOneToMany ( $str ) {
        $self = $this;
        return preg_replace_callback ( '#(access|y$|s$|sh$|o$|x$|f$|fe$|man$|woman$|child$|foot$|tooth$)#i',  function ( $m ) use ( $self ){
            $m[1] = strtolower ( $m[1] );
            if ( $m[1] == 'access' ) return $self->convertCamel ( 'accesse' );
            elseif ( $m[1] == 'y' )     return 'ie';
            elseif ( $m[1] == 'sh' )    return 'e';
            elseif ( $m[1] == 'o' )     return 'e';
            elseif ( $m[1] == 'x' )     return 'e';
            elseif ( $m[1] == 'fe' )    return 've';
            elseif ( $m[1] == 'f' )     return 've';
            elseif ( $m[1] == 'man' )   return 'men';
            elseif ( $m[1] == 'woman' ) return 'women';
            elseif ( $m[1] == 'child' ) return 'children';
            elseif ( $m[1] == 'foot' )  return 'feet';
            elseif ( $m[1] == 'tooth' ) return 'teeth';
            else                        return '';
        }, $str ) . 's';
    }

    /**
     * モデルをセットする
     * @param モデル名
     * @param モデル
     */
    public function setModel ( $name, $obj ) {
        if ( !( $obj instanceof aafwEntityStoreBase ) )
            throw new aafwException ( 'モデルが不正です' );

        if ( !( $obj instanceof $name ) )
            throw new aafwException ( 'モデルが不正です' );

        $this->_Models[$name] = $obj;
        return $this;
    }

    /**
     * モデルを取得する
     * @param モデル名
     * @return モデル
     */
    public function getModel ( $name ) {
        if ( !$this->_Models[$name] ) {
            if ( !$this->_StoreFactory ) $this->_StoreFactory = new aafwEntityStoreFactory ();
            $this->setModel ( $name, $this->_StoreFactory->create ( $name ) );
        }
        return $this->_Models[$name];
    }

    /**
     * サービスをセットする
     * @param サービス名
     * @param サービス
     */
    public function setService ( $name, $obj ) {
        if ( !( $obj instanceof $name ) )
            throw new aafwException ( 'サービスが不正です' );
        $this->_Services[$name] = $obj;
        return $this;
    }

    /**
     * サービスを取得する
     * @param モデル名
     * @return モデル
     */
    public function getService ( $name, $params = null ) {
        if ( !$this->_Services[$name] ) {
            if ( !$this->_ServiceFactory ) $this->_ServiceFactory = new aafwServiceFactory ();
            $this->setService ( $name, $this->_ServiceFactory->create ( $name, $params ) );
        }
        return $this->_Services[$name];
    }

    /**
     * ライブラリをセットする
     * @param ライブラリ名
     * @param ライブラリ
     */
    public function setLibrary ( $name, $obj ) {
        $tmp = preg_split ( '#\.#', $name );
        $class = array_pop ( $tmp );
        if ( !( $obj instanceof $class ) )
            throw new aafwException ( 'ライブラリが不正です|' . $class );
        $this->_Libs[$name] = $obj;
        return $this;
    }

    /**
     * ライブラリを取得する
     * @param ライブラリ名(フルパス)
     * @param コンストラクタの引数
     * @return ライブラリ
     */
    public function getLibrary ( $name, $params = null ) {
        if ( !$this->_Libs[$name] ) {
            if ( !$this->_LibraryFactory ) $this->_LibraryFactory = new aafwLibraryFactory ();
            $this->setLibrary ( $name, $this->_LibraryFactory->create ( $name, $params ) );
        }
        return $this->_Libs[$name];
    }

    /**
     * クエリストリングをアルファベット順に整列して返す
     * @param url
     * @return アルファベット順に整列したURL
     */
    public function normalizeUrl ( $url ) {
        if ( !$url ) throw new aafwException ( 'URLがありません' );
        if ( !preg_match ( '#^https?.+?\?(.+)$#', $url, $tmp ) ) return $url;
        parse_str ( $tmp[1], $query );
        $keys = array_keys ( $query );
        sort ( $keys );
        $tmp = array ();
        foreach ( $keys as $key ) $tmp[] = urlencode ( $key ) . '=' . urlencode ( $query[$key] );
        return preg_replace ( '#\?.+$#', '?' . join ( '&', $tmp ), $url );
    }

    /**
     * 文字列をカンマ編集する
     * @param $str 文字
     * @return カンマ編集した文字
     */
    public function getDomainName ( $url ){
        preg_match( '#https?://([^/]+)#', $url, $tmp ) && $url = $tmp[1];
        $url = preg_replace( '/^(\S+\.|)(([^\.\s]+\.){1}[a-z]{2}\.[a-z]{2})$/', '$2', $url );
        $url = preg_replace( '/^(\S+\.|)(([^\.\s]+\.){1}jp)$/'                , '$2', $url );
        $url = preg_replace( '/^(\S+\.|)(([^\.\s]+\.){1}[a-z]{3}\.[a-z]{2})$/', '$2', $url );
        $url = preg_replace( '/^(\S+\.|)(([^\.\s]+\.){1}[a-z]{3})$/'          , '$2', $url );
        $url = preg_replace( '/^(\S+\.|)(([^\.\s]+\.){1}[a-z]{2})$/'          , '$2', $url );
        return $url;
    }

    /**
     * 指定されたサイズで文字を切って...を付ける
     * @param 文字
     * @param 文字数
     * @param ...が嫌な場合に指定する
     **/
    public function cutLongText( $text, $count, $adding = '...' ){
//         $text = preg_replace( '#\s+#', ' ', trim( strip_tags( $text ) ) );
        if( $count >= mb_strlen( $text, 'UTF-8' ) ) return $text;
        return mb_substr( $text, 0, $count, 'UTF-8' ) . $adding ;
    }

    /**
     * ハッシュから一部のハッシュを取り出す
     * @param $src ハッシュ,可変長
     * @return ハッシュ
     **/
    public function hashSlice(){
        $ret = array();
        $arg = func_get_args();
        $arr = array_shift( $arg );
        foreach( $arg as $key ) {
            if( $arr[$key] ) $ret[$key] = $arr[$key];
        }
        return $ret;
    }

    /**
     * 曜日文字列を返す
     * @param 日付文字列
     * @return 曜日
     **/
    public function getYoubi($date){
        $sday = strtotime($date);
        $res = date("w", $sday);
        $day = array("日", "月", "火", "水", "木", "金", "土");
        return $day[$res];
    }

    /**
     * 文字列をカンマ編集する
     * @param $str 文字
     * @return カンマ編集した文字
     **/
    public function formatNumber( $str ){
        return strrev( preg_replace( '/(\d{3})(?=\d)/', '$1,', strrev( $str - 0 ) ) );
    }

    /**
     * 文字列をカンマ編集する
     * @param $str 文字
     * @return カンマ編集した文字
     */
    public function formatCurrency ( $str ) {
        return  $this->formatNumber ( $str ) ;
    }

    /**
     * ハッシュからQueryStringっぽいものにする
     * @param $src ハッシュ
     * @return QueryString
     **/
    public function toQueryString( $src = null ) {
        if ( !$src ) $src = $this->toArray();
        $ret = array();
        foreach( $src as $key => $value ) {
            if(  $value == '' ) continue;
            if( $key === 'PHPSESSID' ) continue;
            if( $key === 'DSN' ) continue;
            $key = urlencode( $key );
            if( is_array( $value ) ) {
                foreach( $value as $elm ) {
                    if(  $elm == '' ) continue;
                    $ret[] = $key .'[]=' . urlencode( $elm );
                }
            } else {
                $value = urlencode( $value );
                $ret[] = "$key=$value";
            }
        }
        return join( '&' ,$ret );
    }

    /**
     * 日付っぽい文字列に変換
     * @param $str 文字
     * @return 日付っぽい
     */
    public function formatDate( $str, $tmpl = 'YYYY年MM月DD日' ){
        $str = strtotime( preg_replace( array( '#(?:年|月)#', '#(?:時|分)#', '#(?:日|秒)#' ), array( '-', ':', '' ), $str ) );
        return str_replace( array( 'YYYY', 'YY', 'MM', 'DD', 'H', 'I', 'S' ), array( date( 'Y', $str ), substr( date('Y', $str ), 2, 2 ) , date( 'm', $str ), date( 'd', $str ), date( 'H', $str ), date( 'i', $str ), date( 's', $str ) ), $tmpl );
    }


    /**
     * 互換性のために残しているラッパ
     */
    public function deep( $data, $param, $code ){
        $fnc = create_function ( $param, $code );
        return $this->_Deep ( $fnc, $data );
    }

    /**
     * データ構造を再帰的に掘っていって、末端要素にlambda式を適用する
     */
    public function _Deep( $fnc, $data  ){
        if( is_array( $data ) ){
            foreach( array_keys( $data ) as $i ){
                $result = $this->_Deep( $fnc, $data[$i] );
                if ( !is_null ( $result ) ) $data[$i] = $result;
            }
            return $data;
        }
        elseif ( $data instanceof Iterator ) {
            $result = array ();
            foreach ( $data as $row ) {
                $tmp = $this->_Deep ( $fnc, $row );
                if ( !is_null ( $tmp ) ) $result[] = $tmp;
            }
            return $result;
        }
        else {
            $data = $fnc( $data );
            if ( is_array( $data ) || $data instanceof Iterator) return $this->_Deep( $fnc, $data );
            else                    return $data;
        }
    }

    /******************************************
     * 文字が指定文字長以内かどうか判断する
     * @param $str 文字
     * @param $len  文字長
     * @return 真偽値
     ******************************************/
    public function inStrLen( $str, $len, $mb_flg = false, $cut_crlf = false ){
        if( !$str ) return true;
        if( $cut_crlf ) $str = str_replace(  array( "\n", "\r" ), "", $str );
        if( is_array( $len ) ){
            if ( $mb_flg ){
                if( $len['min'] && $len['min'] > mb_strlen( $str, 'utf8' ) ) return false;
                if( $len['max'] && $len['max'] < mb_strlen( $str, 'utf8' ) ) return false;
            }
            else {
                if( $len['min'] && $len['min'] > strlen( $str ) ) return false;
                if( $len['max'] && $len['max'] < strlen( $str ) ) return false;
            }
            return true;
        } else{
            if ( $mb_flg ) return ( mb_strlen( $str, 'utf8' ) <= $len );
            else           return ( strlen( $str ) <= $len );
        }
    }

	/******************************************
	 * 文字が指定文字長かどうか判断する
	 * @param $str 文字
	 * @param $len  文字長
	 * @return 真偽値
	 ******************************************/
//    public function isStrLen( $str, $len ){
//        if( !$str ) return true;
//        return ( strlen( $str ) == $len );
//    }
	public function isStrLen( $str, $len ){
		if( !$str ) return true;
		if($len > strlen($str)) {
			return true;
		} else {
			return false;
		}
	}


    /******************************************
     * 日付が正しいかどうか判断
     * @param $str 文字
     * @param $len  文字長
     * @return 真偽値
     ******************************************/
    public function isDate( $str ){
        if( !$str ) return true;
        return preg_match( '#(\d{4})(?:[/-]|年)(\d{1,2})(?:[/-]|月)(\d{1,2})#', $str, $tmp ) && checkdate( $tmp[2], $tmp[3], $tmp[1] );
    }

    public function isDateTime( $str ){
        return $this->strtoTime ( $str ) !== false;
    }

    public function strtoTime ( $str ) {
        $str = preg_replace ( '#年月#', '-', $str );
        $str = preg_replace ( '#日秒#', ' ', $str );
        $str = preg_replace ( '#時分#', ':', $str );
        $str = preg_replace ( '#:\s*$#', ':00', $str );
        $str = trim ($str);
        return strtotime ( $str );
    }

    /******************************************
     * 数値かどうか判断する
     * @param $str 文字
     * @return bool
     ******************************************/
    public function isNumeric( $str ){
        if( !$str ) return true;
        return is_numeric( $str );
    }

    /******************************************
     * メールアドレスかどうか判断する
     * @param $str 文字
     * @return bool
     **************
     ****************************/
    public function isMailAddress( $str ){
        if (!$str) return true;

        $regex = array(
            '/^([A-Za-z0-9_]|\-|\.|\+)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,6}$/i',
            '/^([A-Za-z0-9_]|\-|\.|\+)+@(([a-z0-9_]|\-)+\.)holdings$/i'
        );

        foreach ($regex as $pattern) {
            if (preg_match( $pattern, trim( $str ) )) return true;
        }

        return false;
    }

    /******************************************
     * URLかどうか判断する(http)
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isURL( $str ){
        if( !$str ) return true;
        return preg_match( '#^https?://[0-9a-zA-Z-_\#:%\.@/\?&=~]+$#', $str );
    }

    /******************************************
     * URLかどうか判断する(http)
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isInternetURL( $str ){
        if( !$str ) return true;
        return preg_match(
            '#^https?://[a-zA-Z0-9][0-9a-zA-Z-]*\.[0-9a-zA-Z-\.]+[0-9a-zA-Z](?:/[0-9a-zA-Z-_\.\?&=~]+)*/?$#',
            $str
        );
    }

    /******************************************
     * 全角カナだけの文字かを判定
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isZenKana( $str ){
        if( !$str ) return true;
        return preg_match( '#^[ァ-ヶー 　]+$#u', $str );
    }

    /******************************************
     * 全角カナだけの文字かを判定
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isZenHira( $str ){
        if( !$str ) return true;
        return preg_match( '#^[ぁ-んー 　]+$#u', $str );
    }

    /******************************************
     * 郵便番号っぽいかどうか判断する
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isZipCode( $str ){
        if( !$str ) return true;
        return preg_match( '#^\d{3}-\d{4}$#', $str );
    }

    /******************************************
     * 電話番号っぽいかどうか判断する
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isTEL( $str ){
        if( !$str ) return true;
        return preg_match( '#^\d+?-\d+?-\d+?$#', $str );
    }

    /******************************************
     * 携帯メールアドレスっぽい文字列かどうか判断する
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isKtaiMail( $str ){
        if( !$str ) return false;
        $regex = array(
            '#@docomo\.ne\.jp$#',
            '#@softbank\.ne\.jp$#',
            '#@i\.softbank\.jp$#',
            '#@disney\.ne\.jp$#',
            '#@d\.vodafone\.ne\.jp$#',
            '#@h\.vodafone\.ne\.jp$#',
            '#@t\.vodafone\.ne\.jp$#',
            '#@c\.vodafone\.ne\.jp$#',
            '#@r\.vodafone\.ne\.jp$#',
            '#@k\.vodafone\.ne\.jp$#',
            '#@n\.vodafone\.ne\.jp$#',
            '#@s\.vodafone\.ne\.jp$#',
            '#@q\.vodafone\.ne\.jp$#',
            '#@jp-d\.ne\.jp$#',
            '#@jp-h\.ne\.jp$#',
            '#@jp-t\.ne\.j$#',
            '#@jp-c\.ne\.jp$#',
            '#@jp-r\.ne\.jp$#',
            '#@jp-k\.ne\.jp$#',
            '#@jp-n\.ne\.jp$#',
            '#@jp-s\.ne\.jp$#',
            '#@jp-q\.ne\.jp$#',
            '#@ezweb\.ne\.jp$#',
            '#@biz\.ezweb\.ne\.jp$#',
            '#@ido\.ne\.jp$#',
            '#@ezweb\.ne\.jp$#',
            '#@ezweb\.ne\.jp$#',
            '#@sky\.tkk\.ne\.jp$#',
            '#@sky\.tkc\.ne\.jp$#',
            '#@sky\.tu-ka\.ne\.jp$#',
            '#@pdx\.ne\.jp$#',
            '#@di\.pdx\.ne\.jp$#',
            '#@dj\.pdx\.ne\.jp$#',
            '#@dk\.pdx\.ne\.jp$#',
            '#@wm\.pdx\.ne\.jp$#',
            '#@willcom\.com$#',
            '#@emnet\.ne\.jp$#',
        );
        foreach( $regex as $ptn ){
            if( preg_match( $ptn, trim( $str ) ) ) return true;
        }
        return false;
    }

	/******************************************
	 * 半角英字かチェックする
	 * @param $str 文字
	 * @return 真偽値
	 ******************************************/
	public function isAlpha( $str ){
		if( !$str ) return false;
		if(preg_match('/^[a-zA-Z]*$/', $str) >= 1){
			return true;
		}else{
			return false;
		}
	}

    /******************************************
     * 半角英数字かチェックする
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isAlnum( $str ){
        if( !$str ) return false;
        if(preg_match('/^[a-zA-Z0-9]*$/', $str) >= 1){
            return true;
        }else{
            return false;
        }
    }

    /******************************************
     * 半角英数記号かチェックする
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isAlnumSymbol( $str ){
        if( !$str ) return false;
        if(preg_match('/^[!-~]*$/', $str) >= 1){
            return true;
        }else{
            return false;
        }
    }

    /******************************************
     * 半角英数記号かチェックする
     * バリデート用メソッド
     * @param $str 文字
     * @return 真偽値
     ******************************************/
    public function isNotAlnumSymbol( $str ){
        return !$this->isAlnumSymbol($str);
    }

    public function isRealInteger ( $val ) {
        if ( !$val ) return true;
        else         return is_numeric ( $val ) && (int)$val == $val ;
    }

    public function inFileSize ( $val, $size ) {
        if ( !is_file ( $val ) ) return true;

        if ( !is_numeric ( $size ) ) {
            if ( !preg_match ( '#^(\d+)([a-zA-Z]+)$#', $size, $tmp ) )
                throw new Exception ( '定義が駄目です' );
            list ( $all, $num, $unit ) = $tmp;
            $units = array (
                'KB' => 1024,
                'K'  => 1024,
                'M'  => 1024 * 1024,
                'MB' => 1024 * 1024,
                'G'  => 1024 * 1024 * 1024,
                'GB' => 1024 * 1024 * 1024,
            );
            if ( !$units[$unit] ) throw new Exception ( '定義が駄目です' );
            $size = $num * $units[$unit];
        }
        return filesize ( $val ) <= $size;
    }

    /******************************************
     * 過去かどうかチェックする
     * @param $date 日付文字列
     * @return 真偽値
     ******************************************/
    public function isPast( $date ){
        if(!$date) return false;
        if(strtotime(date('Y/m/d H:i')) >= strtotime($date)) {
            return true;
        } else {
            return false;
        }
    }

    public function isEmpty ( $val ) {
        return is_null ( $val ) || ( is_array ( $val ) && !count ( $val ) ) || ( is_scalar ( $val ) &&  trim ( $val ) == '' );
    }


    public function getCommandResult ( $cmd, $params = null ) {
        return shell_exec  ( $this->createCommand ( $cmd, $params ) );
    }

    public function runBackgroundProcess ( $cmd, $params = null ) {
        $str =  $this->createCommand ( $cmd , $params ) ;
        $str .= ' > /dev/null & ';
        return system  ( $str );
    }

    public function createCommand ( $cmd, $params ) {
        if ( !$params ) return $cmd;
        return $cmd . ' '  . join ( ' ', array_map ( function ( $elm ) {
            return '"' . str_replace ( '"', '\\"', $elm ) . '"';
        }, $params ));
    }

    public function getToday ( $fmt = null ) {
        if ( !$fmt ) $fmt = 'YYYY/MM/DD';
        return $this->formatDate ( date ( 'Y/m/d H:i:s' ), $fmt );
    }

    public function getUnixTime () {
        return time();
    }

    public function getHours() {
        $timeHH = array();
        for ($i = 0; $i < 24; $i++) {
            if($i < 10) {
                $j = '0' . $i;
            }else{
                $j = $i;
            }
            $timeHH[$j] = $j;
        }
        return $timeHH;
    }

    public function getMinutes() {
        $timeHH = array();
        for ($i = 0; $i < 60; $i++) {
            if($i < 10) {
                $j = '0' . $i;
            }else{
                $j = $i;
            }
            $timeHH[$j] = $j;
        }
        return $timeHH;
    }

}
