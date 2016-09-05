<?php
AAFW::import('jp.aainc.classes.services.base.BrandcoActionManagerBaseService');
AAFW::import('jp.aainc.aafw.base.aafwPOSTActionBase');
abstract class BrandcoManagerPOSTActionBase extends aafwPOSTActionBase implements BrandcoActionManagerBaseInterface {

	use BrandcoActionManagerBaseService;
}