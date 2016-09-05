<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserMessagesThreadValidator');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.products.productsRepository');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');

/**
 * 注文完了
 * Class orderComplete
 */
class order_complete extends BrandcoGETActionBase
{
	public $NeedOption = array();

	public function validate()
	{
		return true;
	}

	public function doAction()
	{
		return 'user/brandco/products/settlement.php';
	}

}
