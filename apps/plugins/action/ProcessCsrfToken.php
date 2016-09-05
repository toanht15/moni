<?php

/**
 * CSRFトークンチェックプラグイン
 */

AAFW::import('jp.aainc.aafw.base.aafwActionPluginBase');

class ProcessCsrfToken extends aafwActionPluginBase {

	const CSRF_SALT = "brandco_csrf_token";


	protected $HookPoint = 'First';

	const REASON_NO_REFERER = "Referer checking failed : no Referer";
	const REASON_BAD_REFERER = "Referer checking failed : does not match";
	const REASON_BAD_TOKEN = "CSRF token missing or incorrect";

	private static $exclude_methods = array('GET', 'HEAD', 'OPTIONS', 'TRACE');

	public function doService() {
		if ($this->Action->CsrfProtect) {

			list($p, $g, $s, $c, $f, $e, $sv, $r) = $this->Action->getParams();

			// Request Method checking ( http://www.ietf.org/rfc/rfc2616.txt )
			$request_method = $sv["REQUEST_METHOD"];
			if (in_array($request_method, self::$exclude_methods)) {
				return;
			}

			// CSRF Token checking
			$csrf_token = hash('sha256', self::CSRF_SALT . session_id());
			$request_csrf_token = $p["csrf_token"];
			if ($csrf_token !== $request_csrf_token) {
				return $this->reject(self::REASON_BAD_TOKEN);
			}
			return;
		}
	}

	/**
	 * @param $error_message
	 * @return int
	 */
	private function reject($error_message) {
		$logger = aafwLog4phpLogger::getDefaultLogger();
		$logger->info("process_csrf_token reject" . $error_message . " Action = " . $this->getActionName());
		return 403;
	}

	/**
	 * @return string
	 */
	private function getActionName() {
		return '/' . $this->Action->Site . '/' . $this->Action->PackageName . '/' . get_class($this->Action);
	}

	/**
	 * @param $server
	 * @return bool
	 */
	private function isSecure($server) {
		if (isset($server['HTTPS']) &&
			($server['HTTPS'] === 'on' || $server['HTTPS'] == 1)
		) {
			return true;
		}
		return false;
	}

	/**
	 * @param $url1
	 * @param $url2
	 * @return bool
	 */
	private function sameOrigin($url1, $url2) {
		$a1 = parse_url($url1);
		$a2 = parse_url($url2);
		return array($a1["scheme"], $a1["host"]) === array($a2["scheme"], $a2["host"]);
	}

}
