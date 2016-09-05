<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.lib.db.aafwRedisManager');

/**
 * 使い方: sid={ブラウザのcookieに保存されたsession id}を引数に渡して実行します。
 *
 * Class SessionViewer
 */
class SessionViewer extends BrandcoBatchBase {

    private $session_id;

    public function setSessionId($session_id) {
        $this->session_id = $session_id;
    }

    public function executeProcess() {
        if (Util::isNullOrEmpty($this->session_id)) {
            throw new aafwException("The argument must be specified!");
        }
        $session_id = $this->session_id;
        $redis = aafwRedisManager::getRedisInstance();
        try {
            $session = $redis->get("s:" . $session_id);
            if ($session === false) {
                aafwLog4phpLogger::getDefaultLogger()->warn("The session is absent!: id=" . $session_id);
                return;
            }
            if (!DEBUG) {
                $session = gzuncompress($session);
            }
            if ($session === false) {
                aafwLog4phpLogger::getDefaultLogger()->warn("Uncompressing the session has failed!!: id=" . $session_id);
                return;
            }
            $size = mb_strlen($session);
            aafwLog4phpLogger::getDefaultLogger()->info("session output: size={$size}, session={$session}");
        } finally {
            if ($redis !== null) {
                $redis->close();
            }
        }
    }
}
