<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class StaticHtmlEntryService extends aafwServiceBase {

	private $entries;
    private $sns_plugins;
    private $entry_users;
    private $external_page_login_types;
    private $embed_entries;

    const FAN_COUNT_REGEX = '/#FAN_COUNT#/';

    const STAMP_RALLY_JOINED_CP_COUNT_REGEX = '/#SR_JOINED_CP_COUNT#/';

	public function __construct() {
		$this->entries = $this->getModel("StaticHtmlEntries");
        $this->sns_plugins = $this->getModel("StaticHtmlSnsPlugins");
        $this->entry_users = $this->getModel("StaticHtmlEntryUsers");
        $this->external_page_login_types = $this->getModel("StaticHtmlExternalPageLoginTypes");
        $this->embed_entries = $this->getModel('StaticHtmlEmbedEntries');

        $this->users = $this->getModel("Users");
	}

    /**
     * @return mixed
     */
    public function getAllEntries() {
        return $this->entries->findAll();
    }

    /**
     * @param $brand_id
     * @return array|bool
     */
    public function getPublicEntryIdByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id,
            'hidden_flg' => 0
        );

        $entries = $this->entries->find($filter);
        if (!$entries) return false;

        $entry_ids = array();
        foreach($entries as $entry) {
            $entry_ids[] = $entry->id;
        }

        return $entry_ids;
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getEntries($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );

        return $this->entries->find($filter);
    }

	public function getEntriesByBrandId($brandId, $page = 1, $limit = 20, $params = array(), $order = null) {
		$filter = array(
			'conditions' => array(
				"brand_id" => $brandId,
			),
			'pager' => array(
				'page' => $page,
				'count' => $limit,
			),
			'order' => $order,
		);
		if( isset($params['keyword']) ){
			$filter['conditions']['body:like'] = "%" . $params['keyword'] . "%";
		}
		if( isset($params['hidden_flg']) ){
			$filter['conditions']['hidden_flg'] = $params['hidden_flg'];
		}

		return $this->entries->find($filter);
	}

    public function getPublicEntriesByBrandId($brandId, $page = 1, $limit = 20, $days_ago = null, $order = null) {

        $now = date('Y/m/d H:i:s');

        if ($days_ago) {

            $previous = date('Y/m/d H:i:s', strtotime($now . '-' . $days_ago . 'day'));

            $conditions = [
                "brand_id" => $brandId,
                "hidden_flg" => 0,
                'public_date:>=' => $previous,
                'public_date:<=' => $now
            ];

        } else {
            $conditions = [
                "brand_id" => $brandId,
                "hidden_flg" => 0,
                'public_date:<=' => $now
            ];
        }

        $filter = array(
            'conditions' => $conditions,
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
        );

        return $this->entries->find($filter);
    }

    public function countPublicEntry() {
        $now = date('Y/m/d H:i:s');
        $filter = array(
            'conditions' => array(
                "hidden_flg" => 0,
                'public_date:<=' => $now
            ),
        );
        return $this->entries->count($filter);
    }

    public function countPublicEntryByBrandId($brand_id) {
        $now = date('Y/m/d H:i:s');
        $filter = array(
            'conditions' => array(
                "hidden_flg" => 0,
                'public_date:<=' => $now,
                'brand_id' => $brand_id
            ),
        );
        return $this->entries->count($filter);
    }

	public function getEntryByBrandIdAndEntryId($brandId, $entryId) {
		$filter = array(
			"id" => $entryId,
			"brand_id" => $brandId,
		);

		return $this->entries->findOne($filter);
	}

	public function getEntryByBrandIdAndPageUrl($brandId, $pageUrl) {
		$filter = array(
			"page_url" => $pageUrl,
			"brand_id" => $brandId,
		);

		return $this->entries->findOne($filter);
	}

	public function count($brandId){
		$filter = array(
			"brand_id" => $brandId
		);
		return $this->entries->count($filter);
	}

	public function createEmptyEntry() {
		return $this->entries->createEmptyObject();
	}

	public function createEntry($entry) {

			$this->entries->save($entry);
	}

	public function getEntryById($entryId) {
		$conditions = array(
			"id" => $entryId,
		);
		return $this->entries->findOne($conditions);
	}

    public function getEntryByIds ($entry_ids, $page = 1, $limit = 20 ) {
        $filter = array(
            "conditions" => array(
                "id" => $entry_ids,
                "hidden_flg" => 0
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            "order" => array(
                "name" => "public_date",
                "direction" => "desc"
            )
        );
        return $this->entries->find($filter);
    }

    public function getPublicEntryByIds ($entry_ids, $page = 1, $limit = 20 ) {
        $filter = array(
            "conditions" => array(
                "id" => $entry_ids,
                "hidden_flg" => 0,
                "public_date:<=" => date('Y-m-d H:i:s')
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            "order" => array(
                "name" => "public_date",
                "direction" => "desc"
            )
        );
        return $this->entries->find($filter);
    }

    public function getAllEntryByIds ($entry_ids, $page = 1, $limit = 20 ) {
        $filter = array(
            "conditions" => array(
                "id" => $entry_ids,
                "public_date:<=" => date('Y-m-d H:i:s')
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            "order" => array(
                "name" => "public_date",
                "direction" => "desc"
            )
        );
        return $this->entries->find($filter);
    }

    public function countPublicEntryByIdsWithPager($entry_ids, $page = 1, $limit = 20 ) {
        $filter = array(
            "conditions" => array(
                "id" => $entry_ids,
                "hidden_flg" => 0,
                "public_date:<=" => date('Y-m-d H:i:s')
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            "order" => array(
                "name" => "public_date",
                "direction" => "desc"
            )
        );
        return $this->entries->count($filter);
    }


    public function countAllEntryByIdsWithPager($entry_ids, $page = 1, $limit = 20 ) {
        $filter = array(
            "conditions" => array(
                "id" => $entry_ids,
                "public_date:<=" => date('Y-m-d H:i:s')
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            "order" => array(
                "name" => "public_date",
                "direction" => "desc"
            )
        );
        return $this->entries->count($filter);
    }


    public function countPublicEntryByIds($entry_ids) {
        $filter = array(
            "conditions" => array(
                "id" => $entry_ids,
                "hidden_flg" => 0,
                "public_date:<=" => date('Y-m-d H:i:s')
            )
        );
        return $this->entries->count($filter);
    }

	public function updateEntry($entry){
		$entry->updated_at = date('Y-m-d H:i:s');
        $this->entries->save($entry);
	}

	public function deleteEntry($brandId, $entry_id){
		$filter = array(
				"conditions" => array(
						"brand_id" => $brandId,
						"id" => $entry_id,
				),
				"page" => array(),
				"order" => array(),
		);
		$entry = $this->entries->findOne($filter);
		$this->entries->delete($entry);
	}

    public function getEntriesByIds($brand_id, $entry_ids) {
        $filter = array(
            "conditions" => array(
                "brand_id" => $brand_id,
                "id" => $entry_ids,
            ),
        );
        return $this->entries->find($filter);
    }

    public function deleteEntryCategoryRelation($entry_id) {
        $entry_category_modal = $this->getModel('StaticHtmlEntryCategories');
        $entry_category_relations = $entry_category_modal->find(array('static_html_entry_id' => $entry_id));
        foreach ($entry_category_relations as $relation) {
            $entry_category_modal->deletePhysical($relation);
        }
    }

    public function deleteEntries($brand, $entry_ids, $user_id) {
        $service_factory = new aafwServiceFactory ();
        $page_stream_service = $service_factory->create('PageStreamService');

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($brand->id);

        $entries = $this->getEntriesByIds($brand->id, $entry_ids);

        foreach ($entries as $entry) {
            $this->deleteEntryCategoryRelation($entry->id);

            $page_entry = $page_stream_service->getEntryByStaticHtmlEntryId($entry->id);
            if ($page_entry) {
                if ($page_entry->hidden_flg == 0) {
                    $panel_service = $page_entry->priority_flg ? $service_factory->create('TopPanelService') : $service_factory->create('NormalPanelService');
                    $panel_service->deleteEntry($brand, $page_entry);
                }

                $page_stream_service->deleteEntry($page_entry);
            }

            $this->entries->delete($entry);

            $this->createStaticHtmlEntryUser($entry, $user_id);
        }
    }

    public function publicEntries($brand, $entry_ids, $user_id) {
        $service_factory = new aafwServiceFactory ();
        $page_stream_service = $service_factory->create('PageStreamService');

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($brand->id);

        $entries = $this->getEntriesByIds($brand->id, $entry_ids);
        $page_stream = $page_stream_service->getStreamByBrandId($brand->id);

        foreach($entries as $entry) {
            $entry->hidden_flg = 0;
            $entry->public_date = $this->getToday();
            $this->entries->save($entry);

            $page_entry = $page_stream_service->getEntryByStaticHtmlEntryId($entry->id);

            if ($page_entry) {
                if (!$page_stream->panel_hidden_flg) {
                    $page_entry->top_hidden_flg = 0;
                }
                $page_entry = $page_stream_service->staticHtmlToPageEntry($page_entry, $entry);

                if (!$page_stream->panel_hidden_flg && $page_entry->hidden_flg == 1) {
                    $panel_service = $page_entry->priority_flg ? $service_factory->create('TopPanelService') : $service_factory->create('NormalPanelService');
                    $panel_service->addEntry($brand, $page_entry);
                } else {
                    $page_stream_service->updateEntry($page_entry);
                }
            }

            $this->createStaticHtmlEntryUser($entry, $user_id);
        }
    }

    public function draftEntries($brand, $entry_ids, $user_id) {
        $service_factory = new aafwServiceFactory ();
        $page_stream_service = $service_factory->create('PageStreamService');

        $cache_manager = new CacheManager();
        $cache_manager->deletePanelCache($brand->id);

        $entries = $this->getEntriesByIds($brand->id, $entry_ids);

        foreach($entries as $entry) {
            $entry->hidden_flg = 1;
            $this->entries->save($entry);

            $page_entry = $page_stream_service->getEntryByStaticHtmlEntryId($entry->id);

            if ($page_entry) {
                $page_entry->top_hidden_flg = 1;
                $page_entry = $page_stream_service->staticHtmlToPageEntry($page_entry, $entry);

                $panel_service = $page_entry->priority_flg ? $service_factory->create('TopPanelService') : $service_factory->create('NormalPanelService');
                $panel_service->deleteEntry($brand, $page_entry);
            }

            $this->createStaticHtmlEntryUser($entry, $user_id);
        }
    }

    public function createStaticHtmlEntryUser($static_html_entry, $user_id){
        $static_html_entry_user = $static_html_entry->createEmptyStaticHtmlEntryUser();
        $static_html_entry_user->static_html_entry_id = $static_html_entry->id;
        $static_html_entry_user->user_id = $user_id;
        $this->entry_users->save($static_html_entry_user);
    }

    /**
     * @param $brand_id
     * @param $user_id
     * @param $data
     * @return mixed
     */
    public function createStaticHtmlEntry($brand_id, $user_id ,$data) {
        $static_html_entry = $this->getEntryById($data['entryId']);
        if(!$data['entryId'] || !$static_html_entry->id) {
            $static_html_entry = $this->createEmptyEntry();
            $static_html_entry->create_user_id = $user_id;
        }

        $static_html_entry->update_user_id = $user_id;
        $static_html_entry->brand_id = $brand_id;
        if (!$data['page_url']) {
            $static_html_entry->page_url = uniqid();
        }else{
            $static_html_entry->page_url = $data['page_url'];
        }
        $static_html_entry->title = $data['title'];
        $static_html_entry->write_type = $data['write_type'];

        if($static_html_entry->write_type == StaticHtmlEntries::WRITE_TYPE_BLOG) {
            $static_html_entry->body = $data['body'];
            $static_html_entry->encode_body = base64_encode($data['body']); // 絵文字対応
            $static_html_entry->extra_body = $data['extra_body'] ? $data['extra_body'] : '';
            $static_html_entry->encode_extra_body = base64_encode($data['extra_body']); // 絵文字対応
        }

        $static_html_entry->meta_title = $data['meta_title'];
        $static_html_entry->meta_description = $data['meta_description'];
        $static_html_entry->meta_keyword = $data['meta_keyword'];
        $static_html_entry->og_image_url = $data['og_image_url'];
        $static_html_entry->public_date = $data['public_date'] . ' ' . $data['public_time_hh'] . ':' . $data['public_time_mm'];
        $static_html_entry->top_panel_display_flg = $data['top_panel_display_flg'];
        $static_html_entry->sns_plugin_tag_text = $data['sns_plugin_tag_text'];
        $static_html_entry->image_url = $data['image_url'];
        $static_html_entry->hidden_flg = $data['display'];
        $static_html_entry->title_hidden_flg = $data['title_hidden_flg'] ? $data['title_hidden_flg'] : 0;
        $static_html_entry->layout_type = $data['layout_type'];
        $static_html_entry->embed_flg = $data['embed_flg'];
        $static_html_entry = $this->entries->save($static_html_entry);
        if($static_html_entry->write_type == StaticHtmlEntries::WRITE_TYPE_TEMPLATE) {
            $this->updateStaticHtmlTemplate($static_html_entry, $data['template_contents_json']);
        }
        return $static_html_entry;
    }

    /**
     * @param $brand_id
     * @param $user_id
     * @param $data
     * @return mixed
     */
    public function savePlainModeStaticHtmlEntry($brand_id, $user_id ,$data) {
        $static_html_entry = $this->getEntryById($data['entryId']);
        if (!$data['entryId'] || !$static_html_entry->id) {
            $static_html_entry = $this->createEmptyEntry();
            $static_html_entry->create_user_id = $user_id;
        }

        $static_html_entry->update_user_id = $user_id;
        $static_html_entry->brand_id = $brand_id;

        if (!$data['page_url']) {
            $static_html_entry->page_url = uniqid();
        } else {
            $static_html_entry->page_url = $data['page_url'];
        }
        $static_html_entry->title = $data['title'];
        $static_html_entry->write_type = $data['write_type'];

        $static_html_entry->body = $data['body'];
        $static_html_entry->encode_body = base64_encode($data['body']); // 絵文字対応
        $static_html_entry->public_date = $data['public_date'] . ' ' . $data['public_time_hh'] . ':' . $data['public_time_mm'];
        $static_html_entry->layout_type = $data['layout_type'];

        return $this->entries->save($static_html_entry);
    }

    /**
     * @param $static_html_entry
     * @param array $static_html_template_json
     * @return array
     */
    private function updateStaticHtmlTemplate($static_html_entry, $static_html_template_json) {
        $service_factory = new aafwServiceFactory();

        /** @var StaticHtmlEntryTemplateService $static_html_entry_template_service */
        $static_html_entry_template_service = $service_factory->create('StaticHtmlEntryTemplateService');
        $static_html_entry_template_service->deleteExistsTemplates($static_html_entry->id);

        $static_html_entry_template_service->insertTemplates($static_html_entry->id, $static_html_template_json);
    }

    /**
     * @param $static_html_entry
     * @param array $sns_plugin_ids
     * @return array
     */
    public function createStaticHtmlSnsPlugins($static_html_entry, $sns_plugin_ids = array()) {
        $static_html_sns_plugins = array();

        foreach ($sns_plugin_ids as $sns_plugin_id) {
            $static_html_sns_plugin = $static_html_entry->createRelatedEmptyObject('StaticHtmlSnsPlugins');
            $static_html_sns_plugin->static_html_entry_id = $static_html_entry->id;
            $static_html_sns_plugin->sns_plugin_id = $sns_plugin_id;
            $static_html_sns_plugins[] = $this->sns_plugins->save($static_html_sns_plugin);
        }
        return $static_html_sns_plugins;
    }

    /**
     * @param $static_html_entry
     */
    public function deleteStaticHtmlSnsPlugins($static_html_entry) {
        $static_html_sns_plugins = $static_html_entry->getStaticHtmlSnsPlugins();
        foreach ($static_html_sns_plugins as $static_html_sns_plugin) {
            $this->sns_plugins->deletePhysical($static_html_sns_plugin);
        }
    }

    public function createStaticHtmlExternalPageLoginTypes($static_html_entry, $social_media_ids = array()) {
        foreach ($social_media_ids as $social_media_id) {
            $static_html_external_page_login_type = $static_html_entry->createRelatedEmptyObject('StaticHtmlExternalPageLoginTypes');
            $static_html_external_page_login_type->static_html_entry_id = $static_html_entry->id;
            $static_html_external_page_login_type->social_media_id = $social_media_id;
            $this->external_page_login_types->save($static_html_external_page_login_type);
        }
    }

    /**
     * @param $static_html_entry
     */
    public function deleteStaticHtmlExternalPageLoginTypes($static_html_entry) {
        $static_html_external_page_login_types = $static_html_entry->getStaticHtmlExternalPageLoginTypes();
        foreach ($static_html_external_page_login_types as $static_html_external_page_login_type) {
            $this->external_page_login_types->deletePhysical($static_html_external_page_login_type);
        }
    }

    public function createStaticHtmlEmbedEntries($static_html_entry, $pubic_flg) {

        $static_html_embed_entry = $static_html_entry->createRelatedEmptyObject('StaticHtmlEmbedEntries');
        $static_html_embed_entry->static_html_entry_id = $static_html_entry->id;
        $static_html_embed_entry->public_flg = $pubic_flg;

        $this->embed_entries->save($static_html_embed_entry);
    }

    /**
     * @param $static_html_entry
     */
    public function deleteStaticHtmlEmbedEntries($static_html_entry) {
        $static_html_embed_entries = $static_html_entry->getStaticHtmlEmbedEntries();
        foreach ($static_html_embed_entries as $static_html_embed_entry) {
            $this->embed_entries->deletePhysical($static_html_embed_entry);
        }
    }

    /**
     * @param $entry_id
     * @return mixed
     */
    public function getStaticHtmlSnsPluginsByEntryId($entry_id) {
        return $this->sns_plugins->find(array('static_html_entry_id' => $entry_id));
    }

    public function createStaticHtmlEntryUsers($static_html_entry, $user_id) {
        $static_html_entry_user = $static_html_entry->createRelatedEmptyObject('StaticHtmlEntryUsers');
        $static_html_entry_user->static_html_entry_id = $static_html_entry->id;
        $static_html_entry_user->user_id = $user_id;
        $this->entry_users->save($static_html_entry_user);
    }

    public function updateStaticHtmlEntryUsers($static_html_entry, $user_id, $type) {
        $filter = array(
            'type' => $type
        );
        $static_html_entry_user = $static_html_entry->getStaticHtmlEntryUser($filter);
        $static_html_entry_user->user_id = $user_id;
        $this->entry_users->save($static_html_entry_user);
    }

    public function getAuthorStaticHtmlEntry($static_html_entry) {
        $author = array();

        if ($static_html_entry->isExistsStaticHtmlEntryUsers()) {

            $filter = array(
                'conditions' => array(
                    'static_html_entry_id' => $static_html_entry->id
                ),
                'order' => array(
                    'name' => 'id',
                    'direction' => 'asc',
                )
            );

            $entry_users = $this->entry_users->find($filter);

            // 作成情報
            $create_entry_user = $entry_users->current();
            $author['create_user'] = $create_entry_user->getUser()->name;
            $author['create_date'] = $create_entry_user->updated_at;

            // 更新情報
            $entry_user = end($entry_users->toArray());
            $update_user = $this->users->findOne($entry_user->user_id);
            $author['update_user'] = $update_user->name;
            $author['update_date'] = $entry_user->updated_at;
        }
        return $author;
    }

    public function evalFanCountMarkdown($content,$fanCount){
        return preg_replace(self::FAN_COUNT_REGEX,$fanCount,$content);
    }

    public function getUserJoinedStampRallyCpCount($templateJson, $user_id) {

        if (!$user_id) {
            return -1;
        }

        $templates = json_decode($templateJson);

        $count = 0;
        $cp_ids = array();
        foreach ($templates as $template) {
            if ($template->type == StaticHtmlTemplate::TEMPLATE_TYPE_STAMP_RALLY) {
                $cp_ids = array_merge($cp_ids, $template->template->cp_ids);
                break;
            }
        }

        if(count($cp_ids) == 0){
            return 0;
        }

        $cp_user_service = $this->getService('CpUserService');
        foreach ($cp_ids as $cp_id) {
            if ($cp_user_service->isJoinFinish($cp_id, $user_id)) {
                $count++;
            }
        }

        return $count;
    }

    public function evalUserJoinedStampRallyCpsCount($content, $cp_count) {
        return preg_replace(self::STAMP_RALLY_JOINED_CP_COUNT_REGEX, $cp_count, $content);
    }
    
    public function isActivePage($entry){
        if(!$entry || $entry->hidden_flg || !$this->isPast($entry->public_date)){
            return false;
        }
        return true;
    }
}
