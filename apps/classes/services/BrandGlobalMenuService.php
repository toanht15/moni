<?php
AAFW::import('jp.aainc.classes.services.base.MenuService');
AAFW::import('jp.aainc.classes.services.FacebookStreamService');
AAFW::import('jp.aainc.classes.services.TwitterStreamService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandGlobalMenuService extends MenuService {

    const HIDDEN_ENTRY_COUNT = 6;

    public function __construct() {

        $this->menus = $this->getModel("BrandGlobalMenus");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        parent::__construct();
    }

    public function getMenusByBrandIdAndMenuIds($brandId, $menuIds) {
        $filter = array(
            'conditions' => array(
                'id' => $menuIds,
                'brand_id' => $brandId,
            ),
        );

        return $this->menus->findOne($filter);
    }

    /**
     * @param $brand_id
     * @param int $count
     * @return array
     */
    public function getAllHiddenEntries($brand_id, $count = self::HIDDEN_ENTRY_COUNT) {

        // TODO リファクタリング

        $service_factory = new aafwServiceFactory ();

        /** @var $facebook_stream_service FacebookStreamService */
        $facebook_stream_service = $service_factory->create("FacebookStreamService");

        /** @var $twitter_stream_service TwitterStreamService */
        $twitter_stream_service = $service_factory->create("TwitterStreamService");

        /** @var $youtube_stream_service YoutubeStreamService */
        $youtube_stream_service = $service_factory->create("YoutubeStreamService");

        /** @var RssStreamService $rss_stream_service */
        $rss_stream_service = $service_factory->create("RssStreamService");

        /** @var $link_entry_service LinkEntryService */
        $link_entry_service = $service_factory->create("LinkEntryService");

        /** @var $link_entry_service LinkEntryService */
        $instagram_stream_service = $service_factory->create('InstagramStreamService');

        $photo_stream_service = $service_factory->create('PhotoStreamService');

        $page_stream_service = $service_factory->create('PageStreamService');

        $order = array(
            'name' => "pub_date",
            'direction' => "desc",
        );

        $entries = array();

        $facebook_entries = $facebook_stream_service->getAllHiddenEntries($brand_id, 1, $count, $order);
        foreach ($facebook_entries as $facebook_entry) {
            $entries[] = $facebook_entry;
        }

        $twitter_entries = $twitter_stream_service->getAllHiddenEntries($brand_id, 1, $count, $order);
        foreach ($twitter_entries as $twitter_entry) {
            $entries[] = $twitter_entry;
        }

        $youtube_entries = $youtube_stream_service->getAllHiddenEntries($brand_id, 1, $count, $order);
        foreach ($youtube_entries as $youtube_entry) {
            $entries[] = $youtube_entry;
        }

        $rss_entries = $rss_stream_service->getAllHiddenEntries($brand_id, 1, $count, $order);
        foreach ($rss_entries as $rss_entry) {
            $entries[] = $rss_entry;
        }

        $link_entries = $link_entry_service->getAllHiddenEntries($brand_id, 1, $count, $order);
        foreach ($link_entries as $link_entry) {
            $entries[] = $link_entry;
        }

        $instagram_entries = $instagram_stream_service->getAllHiddenEntries($brand_id, 1, $count, $order);
        foreach ($instagram_entries as $instagram_entry) {
            $entries[] = $instagram_entry;
        }

        $photo_entries = $photo_stream_service->getAllHiddenEntries($brand_id);
        foreach ($photo_entries as $photo_entry) {
            $entries[] = $photo_entry;
        }

        $static_html_entry_service = $service_factory->create('StaticHtmlEntryService');
        $static_html_entry_ids = $static_html_entry_service->getPublicEntryIdByBrandId($brand_id);
        $page_entries = $page_stream_service->getAllHiddenEntriesByStaticEntryIds($static_html_entry_ids);
        foreach ($page_entries as $page_entry) {
            $entries[] = $page_entry;
        }


        usort($entries, function ($a, $b) {
            $a_pub_date = strtotime($a->pub_date);
            $b_pub_date = strtotime($b->pub_date);
            if ($a_pub_date == $b_pub_date) {
                return 0;
            }
            return ($a_pub_date < $b_pub_date) ? 1 : -1;
        });

        return array_slice($entries, 0, $count);
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

    public function getGlobalMenuByCpLink($cp_id, $brand_id, $directory_name) {

        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'link:regexp' => '^https?://'. Util::getMappedServerName($brand_id) . '/' . Util::resolveDirectoryPath($brand_id, $directory_name) . 'campaigns/'.$cp_id.'($|\?)'
            ),
        );
        return $this->menus->find($filter);
    }
}
