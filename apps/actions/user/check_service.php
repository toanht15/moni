<?PHP
//********************************************************
//Config
//********************************************************
$allowips = array(
    '61.118.106.118',
    '122.218.250.122',
    '112.78.204.174',
    '210.138.60.124',
    '113.38.98.12',
//    '192.168.50.1',
);

// アラート送信メール
$g_from_address         = "system@aainc.co.jp";
$g_aamember_address     = "system@aainc.co.jp";
$g_to_mail_address      = "system@aainc.co.jp";

// DB情報
$g_db_name              = "db_brandco";      // DB_NAME
$g_db_user              = "bc_user";      // DB_USER
$g_db_pass              = "nzigj0ea2";  // DB_PASSWORD
//$g_db_user              = "root";      // DB_USER
//$g_db_pass              = "allied55";  // DB_PASSWORD

// DBサーバ（更新）
$g_db_server            = array("192.168.4.242");
//$g_db_server            = array("127.0.0.1");
// DBサーバ（参照）
$g_db_slave             = array("192.168.4.243");
//$g_db_server            = array("127.0.0.1");

$g_redis_host           = "monipla-redis.8gksjs.ng.0001.apne1.cache.amazonaws.com";
//$g_redis_host           = "localhost";
$g_redis_port           = "6379";

//********************************************************
//DB監視PG
//********************************************************
ini_set("mbstring.internal_encoding","UTF-8");

//********************************************************
//display access url
//********************************************************
$g_hostname = exec("hostname");
if ('443' == $_SERVER['HTTP_X_FORWARDED_PORT']) {
    echo $g_hostname . ":" . "https://" . $_SERVER["SERVER_NAME"] . $_SERVER['REQUEST_URI'] . "<br />";
} else {
    echo $g_hostname . ":" . "http://" . $_SERVER["SERVER_NAME"] . $_SERVER['REQUEST_URI'] . "<br />";
}

//********************************************************
//IPチェック
//********************************************************
$remote_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

$allowip_flg=0;
foreach ($allowips as $ip){
    $ip = chop($ip);
    if (''  === $ip) continue;
    if ('#' === substr($ip, 0, 1)) continue;
    list($allowIp, $mask) = explode("/", $ip);
    if ($ip == $remote_ip){
        $allowip_flg++;
    }
}
if ($allowip_flg == 0) {
    echo "cannot access this page.";
    exit;
}

//********************************************************
//MAIN
//********************************************************

$error_flg="0";

foreach($g_db_server as $db_server){

    //死活監視
    $r_string = system("mysqladmin ping --host=".$db_server." --user=".$g_db_user." --password=".$g_db_pass);

    if($r_string!="mysqld is alive"){
        //死活監視NGの場合
        echo "<br>DBSERVER CONNETCTION ERROR ";
        $mailtitle="DBサーバーコネクションエラー。";
        $mailmsg="DBサーバー".$db_server."がコネクトできません。";
//              mb_send_mail($g_aamember_address, $mailtitle, $mailmsg." 日時:".date("Y/n/j H:i:s"),"From:".$g_from_address."\r\n");

    }else{

        $db = mysql_connect($db_server, $g_db_user, $g_db_pass, true);
        mysql_select_db($g_db_name,$db);
        mysql_query("SET NAMES utf8", $db);
        //死活監視
        $sql ="show variables LIKE 'max_connections';";
        $result=mysql_query($sql);
        while ($row = mysql_fetch_array($result) ){
            $max_connection_limit=$row['Value'];
        }

    }


    $sql ="show status ";
    $result=mysql_query($sql);
    while ($row = mysql_fetch_array($result) ){
        //現在のコネクション数
        //MAXコネクションの9割を闘値として設定
        if($row['Variable_name']=="Threads_connected"){
            if($row['Value']>$max_connection_limit * 0.9){
                $emsg.="<br />DBサーバー".$db_server."のコネクション数が多すぎます。";
                //max_connections取得NGの場合
                $mailtitle="DBサーバーコネクションリミットエラー。";
                $mailmsg="DBサーバー".$db_server."のコネクション数が多すぎます。";
//                              mb_send_mail($g_aamember_address, $mailtitle, $mailmsg." 日時:".date("Y/n/j H:i:s"),"From:".$g_from_address."\r\n");
            }
        }
    }


}

$i=0;
foreach($g_db_slave as $db_server){

    $i++;
    $db = mysql_connect($db_server, $g_db_user, $g_db_pass, true);
    mysql_select_db($g_db_name,$db);
    $sql =" show slave status ";
    $result=mysql_query($sql);
    while ($row = mysql_fetch_array($result) ){
        //現在のコネクション数
        //MAXコネクションの9割を闘値として設定
        if($row['Slave_IO_Running']!="Yes" or $row['Slave_SQL_Running']!="Yes"){
            //max_connections取得NGの場合
            echo "<br>REPLICATION ERROR(slave".$db_server.")";
            $mailtitle="DB REPLICATION ERROR";
            $mailmsg="DBサーバー".$db_server."のレプリケーションがエラーになっています。";
//                      mb_send_mail($g_aamember_address, $mailtitle, $mailmsg." 日時:".date("Y/n/j H:i:s"),"From:".$g_from_address."\r\n");
        }else{
            echo "<br>DB CHECK OK(slave".$i.")";
        }
    }


}

$redis = exec("redis-cli -h ".$g_redis_host." -p ".$g_redis_port." ping");

if($redis=="PONG"){
    echo "<br>REDIS CHECK OK";
} else{
    echo "<br>REDIS ERROR";
}

//無理くり呼んでるからexit
exit;
