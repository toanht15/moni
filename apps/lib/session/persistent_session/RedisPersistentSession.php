<?php
/**
 * Created by IntelliJ IDEA.
 * User: kanebako
 * Date: 2014/06/05
 * Time: 午後5:13
 * クッキーとUserAgentを使って、セッション情報をセキュアに永続化する
 *
 * ①（毎回）Cookie固有のIdentifier, Tokenをもたせる（新規ならgenerate）
 * ②（ログイン）Token regenerate
 * ③（毎回）UserAgentとIdentifierとTokenのセットが不一致の時、セッション情報削除
 */

AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.lib.db.aafwRedisManager');

class RedisPersistentSession {
    public $ttl = 15552000; // 180 days
    protected $redis;
    protected $prefix;
    protected $psess;
    protected $token;
    protected $identifier;
    protected $fingerprint;
    protected $second_fingerprint;

    const TokenKey = '_aamp_t';
    const IdentifierKey = '_aamp_i';
    const FingerprintKey = 'f';
    const SecondFingerprintKey = 'sf';
    const SessionIdKey = 's';

    public function __construct($prefix = 'psess:') {

        $identifier = $_COOKIE[self::IdentifierKey];
        $token = $_COOKIE[self::TokenKey];

        if(!$identifier)$identifier = self::generateIdentifier();
        if(!$token)$token = self::generateToken();

        $this->setToken($token);
        $this->setIdentifier($identifier);
        $this->setFingerPrint(md5($_SERVER['HTTP_USER_AGENT'])); // userAgentのmd5
        $this->setSecondFingerprint(hash('sha256', Util::getIpAddress()));

        $this->redis = aafwRedisManager::getRedisInstance();
        $this->prefix = $prefix;
    }

    public function getIdentifier($prefix = false) {
        if($prefix) {
            $identifier = $this->prefix . $this->identifier;
        } else{
            $identifier = $this->identifier;
        }
        return $identifier;
    }

    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }

    public function getFingerprint() {
        return $this->fingerprint;
    }

    public function setFingerPrint($fingerprint) {
        $this->fingerprint = $fingerprint;
    }

    public function getSecondFingerprint() {
        return $this->second_fingerprint;
    }

    public function setSecondFingerprint($second_fingerprint) {
        $this->second_fingerprint = $second_fingerprint;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function check() {
        $id = $this->getIdentifier(true);
        $hash = $this->redis->hGetAll($id);

        if(!count($hash)) return false; // ハッシュが存在しない

        if ($hash[self::TokenKey] === $this->getToken() && $hash[self::SessionIdKey]) {

            if ($hash[self::FingerprintKey] === $this->getFingerprint() ||
                $hash[self::SecondFingerprintKey] === $this->getSecondFingerprint()
            ) {
                // トークンと指紋が一致したらセッションIDをセット
                session_id($hash[self::SessionIdKey]);
                return true;
            }
        }

        // 不正なトークンでアクセスを試みた時、セッションを破棄する
        session_start();
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        $this->redis->delete(self::IdentifierKey);
        session_destroy();

        return false;
    }

    public static function generateToken() {
        return substr(md5(uniqid(mt_rand(), true)), 15);//トークンは15文字ぐらいで
    }

    public static function generateIdentifier() {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Reset Token & Set SessionId
     * @param $sessionId
     * @param bool $resetToken
     */
    public function setSessionId($sessionId, $resetToken = false) {
        if($resetToken) {
            // トークンをリセット
            $this->setToken($this->generateToken());
        }
        $id = $this->getIdentifier(true);

        $this->redis->multi(Redis::PIPELINE);
        $this->redis->hMset($id, array(
            self::TokenKey => $this->getToken(),
            self::SessionIdKey => $sessionId,
            self::FingerprintKey => $this->getFingerprint(),
            self::SecondFingerprintKey => $this->getSecondFingerprint()));
        $this->redis->expire($id, $this->ttl);
        $this->redis->exec();

        $params = session_get_cookie_params();
        setcookie(self::IdentifierKey, $this->getIdentifier(), time()+$this->ttl, '/', Util::getMappedServerName(),
            $params["secure"], $params["httponly"]);
        setcookie(self::TokenKey, $this->getToken(), time()+$this->ttl, '/', Util::getMappedServerName(),
            $params["secure"], $params["httponly"]);

        // FingerprintはUserAgentなのでCookieにセットしない
    }
}