<?php
AAFW::import ( 'jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase' );
AAFW::import('jp.aainc.classes.CacheManager');
class api_drag_panel extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_drag_panel';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;
	
	public function beforeValidate() {
	}
	public function validate() {
		return true;
	}
	function doAction() {


        $brand = $this->getBrand();

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($brand->id);

		/**
		 * @var $panel_service PanelService
		 */
		$top_panel_service = $this->createService ( "TopPanelService" );
		$normal_panel_service = $this->createService ( "NormalPanelService" );
		
		/**
		 * @var $brand_service BrandService
		 */
		$top_panel_count = $top_panel_service->count ( $brand );
		
		if ($top_panel_count > $this->old_index && $top_panel_count >= $this->next_index) {
			if ($this->old_index >= $this->next_index) {
				$this->next_index -= 1;
			}
			// move from top panel to top panel
			$entry_value = $top_panel_service->getEntryByIndex ( $brand, $this->old_index );
			$entry = $top_panel_service->getEntryByEntryValue ( $entry_value );
			if ($this->next_index >= $top_panel_count) {
				$top_panel_service->moveToEnd ( $brand, $entry );
			} else {
				
				$next_entry_value = $top_panel_service->getEntryByIndex ( $brand, $this->next_index );
				$next_entry = $top_panel_service->getEntryByEntryValue ( $next_entry_value );
				
				$top_panel_service->moveEntry ( $brand, $next_entry, $entry );
			}
			
		} elseif ($top_panel_count <= $this->old_index && $top_panel_count <= $this->next_index) {
			$this->old_index = $this->old_index - $top_panel_count;
			$this->next_index = $this->next_index - $top_panel_count;
			if ($this->old_index >= $this->next_index) {
				$this->next_index -= 1;
			}
			
			// move from normal panel to normal panel
			$entry_value = $normal_panel_service->getEntryByIndex ( $brand, $this->old_index );
			$entry = $normal_panel_service->getEntryByEntryValue ( $entry_value );
			
			if ($this->next_index >= $normal_panel_service->count ( $brand )) {
				$normal_panel_service->moveToEnd($brand,$entry);
			} else {
				
				$next_entry_value = $normal_panel_service->getEntryByIndex ( $brand, $this->next_index );
				$next_entry = $normal_panel_service->getEntryByEntryValue ( $next_entry_value );
				
				$normal_panel_service->moveEntry ( $brand, $next_entry, $entry );
			}
		}

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
	}
}