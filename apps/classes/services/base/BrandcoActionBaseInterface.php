<?php
/**
 * Created by IntelliJ IDEA.
 * User: kanebako
 * Date: 2014/03/10
 * Time: 午後4:54
 * To change this template use File | Settings | File Templates.
 */

interface BrandcoActionBaseInterface {

	// 管理者権限でログインしているかどうか
	const BRANDCO_MODE_USER = 'user';
	const BRANDCO_MODE_ADMIN = 'admin';
	const USERS_NUMBER_LIMIT = 1000;

	public function setMoniplaCore ( $core );

	public function getMoniplaCore ();

	public function setMode ( $mode );

	public function getMode ();

	public function setTwitter ( $obj );

	public function getTwitter ();

	public function getBrand ();

	public function getBrandsUsersRelation ();

	public function doService();

	public function setLogin ( BrandsUsersRelation $brandns_users_relations );

	public function isLogin ();

	public function isLoginAdmin ();

	public function getFormURL ();
}