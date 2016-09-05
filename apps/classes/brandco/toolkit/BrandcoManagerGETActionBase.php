<?php
AAFW::import('jp.aainc.classes.services.base.BrandcoActionManagerBaseService');
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
abstract class BrandcoManagerGETActionBase extends aafwGETActionBase implements BrandcoActionManagerBaseInterface {

	use BrandcoActionManagerBaseService;
}