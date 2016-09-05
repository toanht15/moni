<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 2014/04/15
 * Time: 15:19
 */
AAFW::import('jp.aainc.lib.db.aafwRedisManager');

class RedisSessionHandler implements SessionHandlerInterface
{
    public $ttl = 15552000; // 180 days
    protected $redis;
    protected $prefix;

    public function __construct($prefix = 's:') {
        $this->prefix = $prefix;
    }

    public function open($savePath, $sessionName) {
        $this->redis = aafwRedisManager::getRedisInstance();
        return true;
    }

    /**
     * ストア先のサーバーのConnectionを削除する
     **/
    public function close() {
        if ($this->redis) {
            $this->redis->close();
            $this->redis = null;
        }
        return true;
    }

    /**
     * データを取り出して、そのまま返却する
     **/
    public function read($id) {
        $id = $this->prefix . $id;
        $sessData = $this->redis->get($id);
        if ($sessData) {
            $this->redis->expire($id, $this->ttl);
        }
        if(!DEBUG) {
            $sessData = gzuncompress($sessData);
        }
        return $sessData;
    }

    /**
     * ストア先に、$session_idで$dataを保存する
     **/
    public function write($id, $data) {
        if(!DEBUG) {
            $data = gzcompress($data);
        }
        $id = $this->prefix . $id;
        $this->redis->multi(Redis::PIPELINE);
        $this->redis->set($id, $data);
        $this->redis->expire($id, $this->ttl);
        $this->redis->exec();
        return true;
    }

    /**
     * ストア先から$session_idのデータを削除する
     **/
    public function destroy($id) {
        $this->redis->del($this->prefix . $id);
        return true;
    }

    /**
     * ストアされたデータから$max_life_timeに従って、有効期限切れのデータを削除する
     **/
    public function gc($maxLifetime) {
        // no action necessary because using EXPIRE
        return true;
    }
}

$sessHandler = new RedisSessionHandler();
session_set_save_handler(
    array($sessHandler, 'open'),
    array($sessHandler, 'close'),
    array($sessHandler, 'read'),
    array($sessHandler, 'write'),
    array($sessHandler, 'destroy'),
    array($sessHandler, 'gc')
);