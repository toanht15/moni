<?php
/**
 * Created by IntelliJ IDEA.
 * User: kanebako.ryo
 * Date: 14/01/16
 * Time: 14:59
 * To change this template use File | Settings | File Templates.
 */

class LoginUtil {

    /**
     * monipla2の管理画面にログインする機能
     * @return bool
     */
    public static function isLoginAdmin(){
		$cookie_value = $_COOKIE['ADMIN_LOGIN_CHECK'];
		$check_values = explode("{_}", $cookie_value);
		$admin_id = $check_values[0];
		$check_value = $check_values[1];
		$service_factory = new aafwServiceFactory ();
		$enterpriseService = $service_factory->create('Monipla2EnterpriseService');
		$admin_data = $enterpriseService->getEnterpriseById($admin_id);
		if (empty($admin_data)) {
			return false;
		} else {
			if (md5($admin_data->date_created) != $check_value) {
				return false;
			} elseif( !$_COOKIE['MAAA'] && $admin_data->cancellation_flg ){
				return false;
			}
		}
		return $admin_data;
	}
}