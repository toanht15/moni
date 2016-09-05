<?php
AAFW::import('jp.aainc.classes.services.base.BrandcoActionBaseService');
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
abstract class BrandcoGETActionBase extends aafwGETActionBase implements BrandcoActionBaseInterface {

    use BrandcoActionBaseService;
}