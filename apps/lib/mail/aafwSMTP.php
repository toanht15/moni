<?php
/**
 * 余計なことしないでSMTPに対話するだけのクラス( Connection Pooling付き )
 */
class aafwSMTP
{
    private $Server = 'localhost';
    private $Port = '25';
    private $AuthUser = '';
    private $AuthPassword = '';
    private $canSocketPooling = false;
    private $RecentLog = array();
    private $PoolingConnection = array();

    /**
     * コンストラクタ
     * @param 各プロパティを上書きできる
     */
    public function __construct($settings = array())
    {
        foreach ($settings as $key => $value) $this->$key = $value;
    }

    /**
     * ソケットをopenする
     * @return オープンしたソケットのファイルポインタ
     */
    public function open()
    {
        if ($this->PoolingConnection) return $this->PoolingConnection;

        $this->PoolingConnection = fsockopen($this->Server, $this->Port);

        if ($this->AuthUser && $this->AuthPassword) {

            fputs($this->PoolingConnection, "EHLO " . $this->host . "\r\n");
            $this->RecentLog['EHLO'] = fgets($this->PoolingConnection, 250);

            fputs($this->PoolingConnection, "AUTH LOGIN\r\n");
            $this->RecentLog['AUTH LOGIN'] = fgets($this->PoolingConnection, 334);

            fputs($this->PoolingConnection, base64_encode($this->AuthUser) . "\r\n");
            $this->RecentLog['AUTH USER'] = fgets($this->PoolingConnection, 334);

            fputs($this->PoolingConnection, base64_encode($this->AuthPassword) . "\r\n");
            $this->RecentLog['AUTH PASS'] = fgets($this->PoolingConnection, 235);

        } else {
            fputs($this->PoolingConnection, "HELO " . $this->Server . "\r\n");
            $this->RecentLog['HELO'] = fgets($this->PoolingConnection, 128);
        }

        return $this->PoolingConnection;
    }

    /**
     * ひとつ前の送信ログを取得する
     * @param ログ名称 - HELO, MAIL FROM, RCPT TO, DATA, SUBJECT BODY, SENT
     * @return ログ
     */
    public function getRecentLog($name)
    {
        return $this->RecentLog[$name];
    }

    /**
     * ひとつ前の送信ログを全て取得する
     * @return ログの配列
     */
    public function getLogs()
    {
        return $this->RecentLog;
    }

    /**
     * メールを送る
     * @param array (
     *   'to' => 送信先メールアドレス
     *   'form' => 送信元メールアドレス
     *   'subject' => '件名',
     *   'body' => '本文',
     *   'header' => array ( その他のヘッダ ),
     *   );
     * @return 送信可否 true / false
     */
    public function send($params)
    {
        $sock = $this->open();
        $this->RecentLog = array();

        // 送信者指定
        fputs($sock, "MAIL FROM:" . $params['envelope_from'] . "\r\n");
        $this->RecentLog['MAIL FROM'] = fgets($sock, 128);

        //宛先指定
        if ($params['envelope_to']) fputs($sock, "RCPT TO:" . $params['envelope_to'] . "\r\n");
        else                          fputs($sock, "RCPT TO:" . $params['to'] . "\r\n");
        $this->RecentLog['RCPT TO'] = fgets($sock, 128);

        //DATAを送信後、ピリオドオンリーの行を送るまで本文。
        fputs($sock, "DATA\r\n");
        $this->RecentLog['DATA'] = fgets($sock, 128);
        if ($params['header']) fputs($sock, trim($params['header']) . "\r\n");
        if ($params['to']) fputs($sock, 'To: ' . trim($params['to']) . "\r\n");
        if ($params['subject']) fputs($sock, 'Subject: ' . trim($params['subject']) . "\r\n");
        fputs($sock, "\r\n");

        //本文送信
        fputs($sock, $params['body'] . "\r\n");
        $this->RecentLog['SUBJECT BODY'] = fgets($sock, 128);

        //ピリオドのみの行を送信。
        fputs($sock, "\r\n.\r\n");
        $this->RecentLog['SENT'] = fgets($sock, 128);

        //成功すると250 OK～と返してくるので
        if (!preg_match("#^250#", $this->RecentLog['SENT'])) return false;

        // ソケット閉じる
        if (!$this->canSocketPooling) $this->close();
        else                            fputs($sock, "\r\n");
        return true;
    }

    /**
     *  ソケット閉じる
     */
    public function close()
    {
        if ($this->PoolingConnection) {
            fputs($this->PoolingConnection, "QUIT\r\n");
            fclose($this->PoolingConnection);
            $this->PoolingConnection = null;
        }
    }
}
