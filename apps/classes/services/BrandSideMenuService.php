<?php
AAFW::import('jp.aainc.classes.services.base.MenuService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandSideMenuService extends MenuService {

	public function __construct() {
		$this->menus = $this->getModel("BrandSideMenus");
		parent::__construct();
	}

	/**
	 * @param $brand_id
	 * @param $menu_ids
	 * @param $post
	 */
	public function saveMenusByBrandIdAndMenuIdsAndPosts($brand_id, $menu_ids, $post) {

		$this->menus->begin();

		try {

			// まずは現状のメニューを削除する。
			$this->deletePhysicalAllMenus($brand_id);

			$i = 1;
			foreach ($menu_ids as $menu_id) {
				$menu = $this->createEmptyMenu();
				$menu->brand_id = $brand_id;
				$menu->link = $post["link" . "_" . $menu_id];
				$menu->name = $post["title" . "_" . $menu_id];

				if ($post["hidden_flg_" . $menu_id] === "1") {
					$menu->hidden_flg = 1;
				} else {
					$menu->hidden_flg = 0;
				}

				if ($post["is_blank_flg_" . $menu_id] === "on") {
					$menu->is_blank_flg = 1;
				} else {
					$menu->is_blank_flg = 0;
				}

				$menu->list_order = $i;
				$this->updateMenu($menu);
				$i++;
			}
			BrandInfoContainer::getInstance()->clear($brand_id);
			$this->menus->commit();

		} catch (Exception $e) {
			$this->logger->error("BrandGlobalMenuService#saveMenusByMenuIds Error");
			$this->logger->error($e);
			$this->menus->rollback();
		}
	}
}