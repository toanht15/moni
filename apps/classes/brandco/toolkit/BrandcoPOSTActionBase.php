<?php
AAFW::import('jp.aainc.classes.services.base.BrandcoActionBaseService');
AAFW::import('jp.aainc.aafw.base.aafwPOSTActionBase');
abstract class BrandcoPOSTActionBase extends aafwPOSTActionBase implements BrandcoActionBaseInterface {

    use BrandcoActionBaseService;
}
