<?php
AAFW::import ( 'jp.aainc.vendor.Net.IPv4' );

/******************************
 * 携帯かどうかの判別をする
 ******************************/
class aafwMobileDispatcher {
  private static $Definition = array(
    'docomo' => array(
      'regex'     => '#^DoCoMo#i',
      'is_mobile' => 1,
      'type'      => 'docomo',
      'charset'   => 'sjis',
      'content-type' => 'application/xhtml+xml',
      'zen' => 'istyle=1 style="font-size:x-small;<#OP>"',
      'han' => 'istyle="2" format="*M" mode="hankakukana" maxlength="50" style="font-size:x-small;-wap-input-format:&quot;*&lt;ja:hk&gt;&quot;;-wap-input-format:*M;<#OP>"',
      'num' => 'istyle="4" format="*N" mode="numeric" maxlength="4" style="font-size:x-small;-wap-input-format:&quot;*&lt;ja:n&gt;&quot;;-wap-input-format:*N;<#OP>"',
      'alpha' => 'istyle="3" format="m" mode="alphabet" style="font-size:x-small;-wap-input-format:&quot;*&lt;ja:en&gt;&quot;;-wap-input-format:*m;<#OP>"',
    ),
    'softbank' => array(
      'is_mobile' => 1,
      'regex'    => '#^(?:J\-PHONE|Vodafone|MOT\-[CV]|SoftBank)#i',
      'type'      => 'softbank',
      'charset'   => 'sjis',
      'zen' => 'mode="hiragana" style="font-size:x-small;<#OP>"',
      'han' => 'mode="katakana" style="font-size:x-small;-wap-input-format:&quot;*&lt;ja:hk&gt;&quot;;-wap-input-format:*M;<#OP>"',
      'num' => 'mode="numeric" style="-wap-input-format:&quot;*&lt;ja:n&gt;&quot;;-wap-input-format:*N;font-size:x-small;<#OP>"',
      'alpha' => 'mode="alphabet" -wap-input-format:&quot;*&lt;ja:en&gt;&quot;;-wap-input-format:*m;font-size:<#OP>"',
    ),
    'kddi'    => array(
      'is_mobile' => 1,
      'regex'     => '#(?:^KDDI\-|UP\.Browser)#i',
      'type'      => 'kddi',
      'charset'   => 'sjis',
      'zen' => 'istyle=1 style="font-size:x-small;-wap-input-format:*M;<#OP>"',
      'han' => 'istyle=2 style="<#OP>font-size:x-small;"',
      'num' => 'istyle=4 style="font-size:x-small;-wap-input-format:*N;<#OP>"',
      'alpha' => 'istyle=3 style="font-size:x-small;-wap-input-format:*m;<#OP>"',
    ),
    'emobile' => array(
      'is_mobile' => 1,
      'regex'     => '#^emobile#i' ,
      'type'      => 'emobile',
      'charset'   => 'sjis',
    ),
    'iphone' => array(
      'is_mobile' => 0,
      'is_smart' => 1,
      'regex' => '#iphone#i',
      'type'  => 'iphone',
      'charset' => 'utf8',
    ),
    'android' => array(
      'is_mobile' => 0,
      'is_smart' => 1,
      'regex' => '#Android#i',
      'type'  => 'android',
      'charset' => 'utf8',
    ),
    'pc' => array(
      'is_mobile' => 0,
      'regex'     => '#XXXXXXXX#i' ,
      'type' => 'pc',
      'charset' => 'utf8',
    ),
    'bot' => array(
      'is_mobile' => 1,
      'regex'     => '#Googlebot|Googlebot-Mobile|Google-Sitemaps|Y!J-SRD|Y!J-MBS|BaiduMobaider#i' ,
      'type'      => 'bot',
      'charset'   => 'sjis',
    ),
  );
  private static $IPInfomation = array (
      'Docomo' => array (
        '210.153.84.0/24',
        '210.136.161.0/24',
        '210.153.86.0/24',
        '124.146.174.0/24',
        '124.146.175.0/24',
        '202.229.176.0/24',
        '202.229.177.0/24',
        '202.229.178.0/24',
      ),
      'EZweb' => array (
        '210.230.128.224/28',
        '121.111.227.160/27',
        '61.117.1.0/28',
        '219.108.158.0/27',
        '219.125.146.0/28',
        '61.117.2.32/29',
        '61.117.2.40/29',
        '219.108.158.40/29',
        '219.125.148.0/25',
        '222.5.63.0/25',
        '222.5.63.128/25',
        '222.5.62.128/25',
        '59.135.38.128/25',
        '219.108.157.0/25',
        '219.125.145.0/25',
        '121.111.231.0/25',
        '121.111.227.0/25',
        '118.152.214.192/26',
        '118.159.131.0/25',
        '118.159.133.0/25',
        '118.159.132.160/27',
        '111.86.142.0/26',
        '111.86.141.64/26',
        '111.86.141.128/26',
        '111.86.141.192/26',
        '118.159.133.192/26',
        '111.86.143.192/27',
        '111.86.143.224/27',
        '111.86.147.0/27',
        '111.86.142.128/27',
        '111.86.142.160/27',
        '111.86.142.192/27',
        '111.86.142.224/27',
        '111.86.143.0/27',
        '111.86.143.32/27',
        '111.86.147.32/27',
        '111.86.147.64/27',
        '111.86.147.96/27',
        '111.86.147.128/27',
        '111.86.147.160/27',
        '111.86.147.192/27',
        '111.86.147.224/27',
      ),
      'SoftBank' => array (
        '123.108.237.0/27',
        '202.253.96.224/27',
        '210.146.7.192/26',
        '210.175.1.128/25',
        '210.138.60.124/0',
      ),
    );

    private static $MobileCarrierDomain = array (
      'Docomo' => array (
        '/^[a-zA-Z0-9\.\_\-]+@docomo\.ne\.jp$/',
        '/^[a-zA-Z0-9\.\_\-]+@docomo\-camera\.ne\.jp$/',
        '/^[a-zA-Z0-9\.\_\-]+@mopera\.net$/',
      ),
      'EZweb' => array (
        '/^[a-zA-Z0-9\.\_\-]+@ezweb\.ne\.jp$/',
        '/^[a-zA-Z0-9\.\_\-]+@.+\.biz\.ezweb\.ne\.jp$/',
      ),
      'SoftBank' => array (
        '/^[a-zA-Z0-9\.\_\-]+@softbank\.ne\.jp$/',
        '/^[a-zA-Z0-9\.\_\-]+@disney\.ne\.jp$/',
        '/^[a-zA-Z0-9\.\_\-]+@i\.softbank\.jp$/',
        '/^[a-zA-Z0-9\.\_\-]+@[cdhknqrst]\.vodafone\.ne\.jp$/',
      ),
      'Other' => array (
        '/^[a-zA-Z0-9\.\_\-]+@pdx\.ne\.jp$/',
        '/^[a-zA-Z0-9\.\_\-]+@.+\.pdx\.ne\.jp$/',
        '/^[a-zA-Z0-9\.\_\-]+@willcom\.com$/',
        '/^[a-zA-Z0-9\.\_\-]+@emnet\.ne\.jp$/',
      )
    );

  public static function isMobile( $sv ){
    foreach( self::$Definition as $key => $val ){
      if( $key == 'pc' ) continue;
      if( preg_match( $val['regex'], $sv['HTTP_USER_AGENT'] ) && ( $val['type'] == 'bot' || self::isMobile_ip($sv['REMOTE_ADDR']) ) ){
        $val['mobile_id']= self::getSerialNumber($_SERVER); //mobile_id にセットされていない？　2011/03/09 T.Ishii
        return $val;
      }
    }
    return self::$Definition['pc'];
  }

  public static function isMobile_ip( $ip ){
    foreach (self::$IPInfomation as $val) {
      foreach ($val as $value) {
        if (Net_IPv4::ipInNetwork($ip, $value)) {
          return true;
        }
      }
    }
  }

  public static function getDoctype($sv){
    foreach( self::$Definition as $key => $val ){
      if( preg_match( $val['regex'], $_SERVER['HTTP_USER_AGENT']) ){
        if($val['type']=='docomo') return '<!DOCTYPE html PUBLIC "-//i-mode group (ja)//DTD XHTML  i-XHTML(Locale/Ver.=ja/2.3) 1.0//EN" "i-xhtml_4ja_10.dtd">';
        elseif($val['type']=='softbank') return '<!DOCTYPE html PUBLIC "-//JPHONE//DTD XHTML Basic 1.0  Plus//EN" "xhtml-basic10-plus.dtd">';
        elseif($val['type']=='kddi') return '<!DOCTYPE html PUBLIC "-//OPENWAVE//DTD XHTML 1.0//EN"  "http://www.openwave.com/DTD/xhtml-basic.dtd">';

      }
    }
    return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0  Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

  }

  //シリアルNOの取得
  function getSerialNumber($sv){

    foreach( self::$Definition as $key => $val ){
      if( preg_match( $val['regex'], $sv['HTTP_USER_AGENT']) ){
        if($val['type']=='docomo'){
          //    return $sv['HTTP_X_DCMGUID'];
          //GETの値とか返してはダメ
          if($sv['HTTP_X_DCMGUID']) return $sv['HTTP_X_DCMGUID'];
          elseif($_POST['g2id']) return $_POST['g2id'];
          elseif($_GET['g2id']) return base64_decode($_GET['g2id']);
          //x                 return $sv['HTTP_X_DCMGUID'];
        }
        elseif($val['type']=='softbank'){
          return $sv['HTTP_X_JPHONE_UID'];
        }
        elseif($val['type']=='kddi'){
          return $sv['HTTP_X_UP_SUBNO'];
        }
      }
    }
    return ;
  }

  public static function isMobile_mail( $d ){
    foreach (self::$MobileCarrierDomain as $val) {
      foreach ($val as $value) {
        if( preg_match( $value, $d) ) return true;
      }
    }
    return false;
  }

  /*
   * Mobile用のｽﾀｲﾙを生成する
   * param string
   *      zen   = zenkaku
   *      han   = hankaku
   *      num   = numeric
   *      alpha = alphabet & num & symbol
   * param  option(widthなどstyleに追加する要素
   * param  Mobile    */
  public static function getMobileInputStyle($style ,$options=array() ,$m)
  {
    //$type =  self::isMobile($m['type']);
    foreach($options as $op => $key){
      $option_str .= $op . ":" . $key . ";";
    }
    return (str_replace('<#OP>',$option_str, self::$Definition[$m['type']][$style]));
  }
  /*使い方
   *<input name="nickname" type="text" value=""  maxlength="13" <?php asign(aafwMobileDispatcher::getMobileInputStyle('zen',array('width'=>'80%'), $this->Mobile)) ?> /><br />
   *<input name="nickname" type="text" value=""  maxlength="13" <?php asign(aafwMobileDispatcher::getMobileInputStyle('han',array('width'=>'80%'), $this->Mobile)) ?>/> <br />
   *<input name="nickname" type="text" value=""  maxlength="13" <?php asign(aafwMobileDispatcher::getMobileInputStyle('num',array('width'=>'80%'), $this->Mobile)) ?> /><br />
   *<input name="nickname" type="text" value=""  maxlength="13" <?php asign(aafwMobileDispatcher::getMobileInputStyle('alpha',array('width'=>'80%'), $this->Mobile)) ?>/><br />
   */

}
