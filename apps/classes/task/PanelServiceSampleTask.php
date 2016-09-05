<?php
/**
 * Created by IntelliJ IDEA.
 * User: sekine-hironori
 * Date: 2014/02/
 * Time: 10:16
 * To change this template use File | Settings | File Templates.
 */

AAFW::import('jp.aainc.classes.task.CrawlerTask');

class PanelServiceSampleTask extends CrawlerTask {

	public function __construct($crawler_type) {
		$this->service_factory = new aafwServiceFactory ();
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

	public function prepare() {
		// TODO: Implement prepare() method.
	}

	public function crawl() {

		/** @var  $brand_global_menu_service BrandGlobalMenuService */
		$brand_global_menu_service = $this->service_factory->create("BrandGlobalMenuService");

		$entries = $brand_global_menu_service->getAllHiddenEntries(1);
		foreach($entries as $entry) {
			echo $entry->pub_date . "\n";
			echo strtotime($entry->pub_date) . "\n";
		}


		/** @var  $brand_service BrandService */
		$brand_service = $this->service_factory->create("BrandService");

		/** @var  $stream_service FacebookStreamService */
		//$stream_service = $this->service_factory->create("FacebookStreamService");
		$stream_service = $this->service_factory->create("TwitterStreamService");
		
		$brand = $brand_service->getBrandByDirectoryName("sekine");

		// -------------
		// Normal
		// -------------

		/** @var  $panel_service PanelService */
		$panel_service = $this->service_factory->create("NormalPanelService");

		// 追加
		for ($i = 1; $i < 10; $i++) {
			$entry = $stream_service->getEntryById($i);
			$panel_service->addEntry($brand, $entry);
		}

		// 取得（offset, limit)
		$entry_values = $panel_service->getEntriesByOffsetAndLimit($brand,0, 1);

		// 取得（page)
		$entry_values = $panel_service->getEntriesByPage($brand, 1, 10);

		// 非表示
 		$delete_entry = $stream_service->getEntryById(1);
 		$panel_service->deleteEntry($brand, $delete_entry);

		// ドラッグ&ドロップ
 		$entry = $stream_service->getEntryById(2);
 		$next_entry = $stream_service->getEntryById(9);
 		$panel_service->moveEntry($brand, $next_entry, $entry);


		// -------------
		// Top
		// -------------

		/** @var  $panel_service PanelService */
		$panel_service = $this->service_factory->create("TopPanelService");


		// トップ固定
		for ($i = 2; $i < 6; $i++) {
			$entry = $stream_service->getEntryById($i);
			$panel_service->fixEntry($brand, $entry);
		}

		// 取得（page)
		$entry_values = $panel_service->getEntriesByOffsetAndLimit($brand,0, 1);

		// 取得（page)
		$entry_values = $panel_service->getEntriesByPage($brand, 1, 10);

		// トップ固定をはずす
		$entry = $stream_service->getEntryById(3);
		$panel_service->unFixEntry($brand, $entry);

		// 非表示
		$entry = $stream_service->getEntryById(4);
		$panel_service->deleteEntry($brand, $entry);

		// ドラッグ&ドロップ
//		$entry = $stream_service->getEntryById(2);
//		$next_entry = $stream_service->getEntryById(9);
//		$panel_service->moveEntry($brand, $next_entry, $entry);

	}

	public function finish() {
	}
}