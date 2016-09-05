<?PHP
/*
//memcachedを使用するためのClass
//
//
*/
AAFW::import( 'jp.aainc.aafw.aafwApplicationConfig' );
extension_loaded('memcache') || dl('memcache.so') || exit(1);
class aafwMemcacheManager {
	private static $singleton = null;
  public static $memcache_object = null;

	private function __construct() {
    $app = aafwApplicationConfig::getInstance();
		$this->memcache_object	= new Memcache();
    $this->memcache_object->addServer( $app->MemcacheInfo['server'], $app->MemcacheInfo['port'] );


		}

	public function __destruct() {
/*
		if ( !is_null( $this->memcache_object ) ) {
      $this->memcache_object->close();
		}
*/
	}

	public static function singleton() {
    if ( is_null( self::$memcache_object ) ) {
      self::$memcache_object	= new aafwMemcacheManager();
		}
		return self::$memcache_object;
	}

	public function close() {
    /// これよくわからないのでコメントアウト
		//if ( $this->memcache_object->close() ) {
    //		$this->memcache_object	= null;
    //		return true;
    //	}
		return false;
	}

	public function flush() {
		return $this->memcache_object->flush();
	}

	public function delete( $key, $timeout=0 ) {
		return $this->memcache_object->delete( $key, $timeout );
	}

	public function get( $key, &$flag=null ) {
		return $this->memcache_object->get( $key, $flag );
	}

	public function set( $key, $var, $flag=0, $expire=86400 ) {
		//60秒×60分×24時間
		if ( $expire > 60*60*24 ) {
			$expire	= 60*60*24;
		}
			$this->memcache_object->set( $key, $var, $flag, $expire);
		return;
	}

	public function fetchAll() {
		return $this->memcache_object->fetchAll;
	}
}
