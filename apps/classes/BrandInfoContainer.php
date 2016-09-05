<?php

AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.lib.container.aafwContainer');

/**
 * アクセスしたブランドの情報を保持するためのコンテナ。
 *
 * クリア以外の操作を実施するときは、必ずinitializeメソッドで事前に初期化してください。
 *
 * Class BrandInfoContainer
 */
class BrandInfoContainer {

    const KEY_BRAND = 'b';

    const KEY_BRAND_PAGE_SETTING = 'bps';

    const KEY_BRAND_CONTRACT = 'bc';

    const KEY_BRAND_OPTIONS = 'bo';

    const KEY_BRAND_GLOBAL_SETTINGS = 'bgs';

    const KEY_BRAND_GLOBAL_MENUS = 'bgm';

    const EMPTY_BRAND_ID = -1;

    private static $instance;

    private $cache = null;

    /** @var CacheManager cache_manager */
    private $cache_manager;

    private $brand = null;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new BrandInfoContainer();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->cache_manager = new CacheManager();
    }

    /**
     * コンテナを初期化します。
     *
     * オンライン(Web)ではRouterで設定します。
     * マネジャー画面から呼ぶときは、使用時に個別に設定してください。
     *
     * @param $brand
     */
    public function initialize($brand) {
        if ($brand === null) {
            throw new aafwException("The argument brand id null!");
        }
        $this->brand = $brand;
        unset($this->cache);
    }

    public function getBrand() {
        return $this->brand;
    }

    public function getBrandContract() {
        if ($this->cache === null) {
            $this->loadCache();
        }
        return $this->cache[self::KEY_BRAND_CONTRACT];
    }

    public function getBrandPageSetting() {
        if ($this->cache === null) {
            $this->loadCache();
        }
        return $this->cache[self::KEY_BRAND_PAGE_SETTING];
    }

    public function getBrandOptions() {
        if ($this->cache === null) {
            $this->loadCache();
        }
        return $this->cache[self::KEY_BRAND_OPTIONS];
    }

    public function getBrandGlobalSettings() {
        if ($this->cache === null) {
            $this->loadCache();
        }
        return $this->cache[self::KEY_BRAND_GLOBAL_SETTINGS];
    }

    public function getBrandGlobalMenus() {
        if ($this->cache === null) {
            $this->loadCache();
        }
        return $this->cache[self::KEY_BRAND_GLOBAL_MENUS];
    }

    /**
     * キャッシュしたブランドに関する情報を全てクリアします。
     *
     * @param null $brand_id
     * @throws aafwException
     */
    public function clear($brand_id) {
        $this->cache_manager->clearBrandInfo($brand_id);
    }

    private function loadCache() {
        if ($this->brand === null) {
            return;
        }

        $brand = $this->brand;
        $cache_info = $this->cache_manager->getBrandInfo($brand->id);
        if (!$cache_info) {
            $brand_id = $brand->id;

            /** @var BrandContractService $brand_contract_service */
            $brand_contract_service = aafwServiceFactory::create('BrandContractService');

            /** @var BrandPageSettingService $brand_page_setting_service */
            $brand_page_setting_service = aafwServiceFactory::create('BrandPageSettingService');

            /** @var BrandGlobalSettingService $brand_global_setting_service */
            $brand_global_setting_service = aafwServiceFactory::create('BrandGlobalSettingService');

            /** @var BrandGlobalMenuService $service */
            $service = aafwServiceFactory::create('BrandGlobalMenuService');

            $this->cache = array(
                self::KEY_BRAND_CONTRACT => $brand_contract_service->getBrandContractByBrandId($brand_id),
                self::KEY_BRAND_PAGE_SETTING => $brand_page_setting_service->getPageSettingsByBrandId($brand_id),
                self::KEY_BRAND_GLOBAL_SETTINGS => $brand_global_setting_service->getBrandGlobalSettingsByBrandId($brand_id),
                self::KEY_BRAND_OPTIONS => $brand->getBrandOptions(),
                self::KEY_BRAND_GLOBAL_MENUS => $service->getDisplayMenuByBrandId($brand_id)
            );

            // arrayに変換して保存します。
            $global_setting_info = array();
            foreach ($this->cache[self::KEY_BRAND_GLOBAL_SETTINGS] as $global_setting) {
                $global_setting_info[] = $global_setting->toArray();
            }

            $brand_option_info = array();
            foreach ($this->cache[self::KEY_BRAND_OPTIONS] as $brand_option) {
                $brand_option_info[] = $brand_option->toArray();
            }

            $brand_menu_info = array();
            foreach ($this->cache[self::KEY_BRAND_GLOBAL_MENUS] as $brand_menu) {
                $brand_menu_info[] = $brand_menu->toArray();
            }

            $cache_info = array(
                self::KEY_BRAND_CONTRACT => $this->cache[self::KEY_BRAND_CONTRACT] !== null ? $this->cache[self::KEY_BRAND_CONTRACT]->toArray() : null,
                self::KEY_BRAND_PAGE_SETTING => $this->cache[self::KEY_BRAND_PAGE_SETTING] !== null ? $this->cache[self::KEY_BRAND_PAGE_SETTING]->toArray() : null,
                self::KEY_BRAND_GLOBAL_SETTINGS => $global_setting_info,
                self::KEY_BRAND_OPTIONS => $brand_option_info,
                self::KEY_BRAND_GLOBAL_MENUS => $brand_menu_info
            );
            $this->cache_manager->setBrandInfo($brand_id, $cache_info);
            return;
        }

        $global_settings = array();
        foreach ($cache_info[self::KEY_BRAND_GLOBAL_SETTINGS] as $global_setting) {
            $global_settings[] = aafwEntityStoreBase::newEntity("BrandGlobalSettings", $global_setting);
        }

        $brand_options = array();
        foreach ($cache_info[self::KEY_BRAND_OPTIONS] as $brand_option) {
            $brand_options[] = aafwEntityStoreBase::newEntity("BrandOptions", $brand_option);
        }

        $brand_menus = array();
        foreach ($cache_info[self::KEY_BRAND_GLOBAL_MENUS] as $brand_menu) {
            $brand_menus[] = aafwEntityStoreBase::newEntity("BrandGlobalMenus", $brand_menu);
        }

        $this->cache = array(
            self::KEY_BRAND_CONTRACT => isset($cache_info[self::KEY_BRAND_CONTRACT]) ? aafwEntityStoreBase::newEntity("BrandContracts", $cache_info[self::KEY_BRAND_CONTRACT]) : null,
            self::KEY_BRAND_PAGE_SETTING => isset($cache_info[self::KEY_BRAND_PAGE_SETTING]) ? aafwEntityStoreBase::newEntity("BrandPageSettings", $cache_info[self::KEY_BRAND_PAGE_SETTING]) : null,
            self::KEY_BRAND_GLOBAL_SETTINGS => new aafwContainer($global_settings),
            self::KEY_BRAND_OPTIONS => new aafwContainer($brand_options),
            self::KEY_BRAND_GLOBAL_MENUS => new aafwContainer($brand_menus)
        );
    }
}