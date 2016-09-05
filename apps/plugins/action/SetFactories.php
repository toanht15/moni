<?php
AAFW::import('jp.aainc.aafw.base.aafwActionPluginBase');
AAFW::import('jp.aainc.aafw.factory.aafwEntityStoreFactory');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');
AAFW::import('jp.aainc.classes.thrift.ThriftModelFactory');
AAFW::import('jp.aainc.classes.socialmedias.SocialMediaFactory');
class SetFactories extends aafwActionPluginBase {
	protected $HookPoint = 'First';
	protected $Priority = 1;

	public function doService() {
		list($p, $g, $s, $c, $f, $e, $sv, $r) = $this->Action->getParams();
		
		foreach ($this->Action->getModelDefinitions() as $class) {
			$this->Action->setModel($class, aafwEntityStoreFactory::create($class));
		}
		$this->Action->setServiceFactory(new aafwServiceFactory ());
		$this->Action->rewriteParams($p, $g, $s, $c, $f, $e, $sv, $r);
		return '';
	}
}
