<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class CpMakeStepType extends aafwWidgetBase {
    private $pageLimited = 5;

	public function doService( $params = array() ){

        $params['totalPages'] = floor ($params['cp_count'] / $this->pageLimited) + ($params['cp_count'] % $this->pageLimited > 0);
        $params['pageLimit'] = $this->pageLimited;

		return $params;
	}
}