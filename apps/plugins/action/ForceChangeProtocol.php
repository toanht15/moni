<?php

require_once 'base/aafwActionPluginBase.php';

class ForceChangeProtocol extends aafwActionPluginBase {

	protected $HookPoint = 'First';

	public function doService() {

		list( $p, $g, $s, $c, $f, $e, $sv, $r ) = $this->Action->getParams();
		if ( config('Protocol.Secure') == config('Protocol.Normal') ) return;

        if(preg_match('#/oauth/exchange_token#', $_SERVER["REQUEST_URI"], $match)) {
            return;
        }
        if(preg_match('#/oauth/refresh_token#', $_SERVER["REQUEST_URI"], $match)) {
            return;
        }
        if(preg_match('#/user/me#', $_SERVER["REQUEST_URI"], $match)) {
            return;
        }

		// デフォルトhttpsにセット
		if ( $this->Action->Secure !== false ) {
            if ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || '443' == $_SERVER['HTTP_X_FORWARDED_PORT'] ) return;
            if ( $sv['REQUEST_METHOD'] != 'GET' ) {
                return; // TODO : 条件に適合するところにロガーを仕込む。転送はまだしない。
//                return 403;
            }
			return 'redirect301: ' . config('Protocol.Secure') . '://' . $sv['SERVER_NAME'] . '/' . preg_replace( '#^/#', '', $sv['REQUEST_URI'] );
		}
	}
    
}
