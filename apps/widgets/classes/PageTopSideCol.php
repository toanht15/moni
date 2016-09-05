<?php
AAFW::import('jp.aainc.widgets.base.TopSideColBase');

class PageTopSideCol extends TopSideColBase {
	
	public function doAction( $params = array() ){
        /** @var StaticHtmlCategoryService $static_html_tag_service */
        $static_html_tag_service = $this->getService('StaticHtmlCategoryService');
        $params['top_categories'] = $static_html_tag_service->getCategoriesAtDepth(0, $params['brand']->id);

		return $params;
	}

    public function canShowSNSBox() {
        return false;
    }
}