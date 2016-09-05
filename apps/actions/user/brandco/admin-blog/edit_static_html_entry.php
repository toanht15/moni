<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.StaticHtmlEntryValidator');
AAFW::import('jp.aainc.classes.validator.StaticHtmlEntryTemplateValidator');
class edit_static_html_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'edit_static_html_entry';
    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'edit_static_html_entry_form/{entryId}?mid=failed',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /** @var  StaticHtmlEntryService $static_html_entry_service */
    private $static_html_entry_service;
    private $file_info;
    private $categories_id;

    protected $ValidatorDefinition = array();

    public function beforeValidate() {
        // ValidatorDefinition取得
        $static_html_validator = new StaticHtmlEntryValidator();
        $this->ValidatorDefinition = $static_html_validator->getValidatorDefinition($this->POST);
    }

    public function validate() {
        $this->Data['brand'] = $this->getBrand();
        $this->static_html_entry_service = $this->createService('StaticHtmlEntryService');

        // entryチェック
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_STATIC_HTML, $this->Data['brand']->id);
        if (!$idValidator->isCorrectEntryId($this->entryId)) return false;

        // entry取得
        $entry = $this->static_html_entry_service->getEntryByBrandIdAndPageUrl($this->Data['brand']->id, $this->page_url);

        //check page url is existed
        if ($entry) {
            if (($this->entryId && ($this->entryId != $entry->id)) || !$this->entryId) {
                $this->Validator->setError('page_url', 'EXISTED_PAGE_URL');
            }
        }

        $aValid = array('-', '_');
        if (!$this->isEmpty($this->page_url) &&!ctype_alnum(str_replace($aValid, '', $this->page_url))){
            $this->Validator->setError('page_url', 'NOT_ALPHANUMERIC_CHARACTER');
        }

        // og_imageチェック
        if ($this->FILES['og_image']) {
            $fileValidator = new FileValidator($this->FILES ['og_image'], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('og_image', 'NOT_MATCHES');
            } else {
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        //カテゴリーバリデーション
        /** @var StaticHtmlCategoryService $category_service */
        $category_service = $this->createService('StaticHtmlCategoryService');
        $this->categories_id = array();
        foreach ($this->POST as $param => $value) {
            if (preg_match('/^category_/', $param) && $value == 'on') {
                $category_id = explode('_', $param);
                if (!$category_service->getCategoryById($category_id[1])) {
                    return '404';
                }
                $this->categories_id[] = $category_id[1];
            }
        }

        if ($this->POST['write_type'] == StaticHtmlEntries::WRITE_TYPE_TEMPLATE) {
            $static_html_template_validator = new StaticHtmlEntryTemplateValidator();
            if ($static_html_template_validator->isValid($this->POST['template_contents_json']) == false) {
                $this->Validator->setError('template_contents_json', 'SAVE_ERROR');
            }
        }
        if ($this->Validator->getErrorCount()) return false;

        return true;
    }

    function doAction() {
        // og_image保存
        if ($this->FILES['og_image']) {
            // メインバナー画像 保存
            $this->POST['og_image_url'] = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $this->Data['brand']->id . '/static_html_entry/' . StorageClient::getUniqueId()), $this->file_info
            );
        }

        $static_html_entry_transaction = aafwEntityStoreFactory::create('StaticHtmlEntries');
        try {
            $static_html_entry_transaction->begin();

            $cache_manager = new CacheManager();
            $cache_manager->deletePanelCache($this->getBrand()->id);

            /** @var UserService $user_service */
            $user_service = $this->createService('UserService');
            $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

            if ($this->layout_type == StaticHtmlEntries::LAYOUT_PLAIN) {
                $static_html_entry = $this->static_html_entry_service->savePlainModeStaticHtmlEntry($this->brand->id, $user->id, $this->POST);
            } else {
                // static_html_entry更新
                $static_html_entry = $this->static_html_entry_service->createStaticHtmlEntry($this->Data['brand']->id, $user->id, $this->POST);
                if (!$static_html_entry) return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries');

                // Update page entry
                $page_stream_service = $this->createService('PageStreamService');
                $page_stream = $page_stream_service->getStreamByBrandId($this->getBrand()->id);
                $page_entry = $page_stream_service->getEntryByStaticHtmlEntryId($static_html_entry->id);

                if ($page_entry) {
                    if ($this->POST['display'] == 1) {
                        $page_entry->top_hidden_flg = 1;
                    } elseif ($this->isPast($static_html_entry->public_date) && !$page_stream->panel_hidden_flg) {
                        $page_entry->top_hidden_flg = 0;
                    }
                    $page_entry = $page_stream_service->staticHtmlToPageEntry($page_entry, $static_html_entry);
                    $page_stream_service->updateEntry($page_entry);

                    $panel_service = $page_entry->priority_flg ? $this->createService('TopPanelService') : $this->createService('NormalPanelService');

                    if ($this->POST['display'] == 1) {
                        // 下書きに更新した場合、トップパネルから非表示にする
                        $panel_service->deleteEntry($this->getBrand(), $page_entry);
                    } elseif (!$page_entry->isPrePublicPage() && !$page_stream->panel_hidden_flg && $page_entry->hidden_flg == 1) {
                        $panel_service->addEntry($this->getBrand(), $page_entry);
                    }
                }

                // sns_plugin削除
                $this->static_html_entry_service->deleteStaticHtmlSnsPlugins($static_html_entry);

                // sns_plugin設定
                if (count($this->sns_plugins)) {
                    $this->static_html_entry_service->createStaticHtmlSnsPlugins($static_html_entry, $this->sns_plugins, $this->sns_plugin_tag_text);
                }

                // カテゴリー更新
                /** @var StaticHtmlCategoryService $category_service */
                $category_service = $this->createService('StaticHtmlCategoryService');
                $old_entry_categories = $category_service->getCategoryByEntryId($this->POST['entryId']);
                $old_categories = array();
                foreach ($old_entry_categories as $old_entry_category) {
                    $old_categories[] = $old_entry_category->category_id;
                }

                $add_category = array_diff($this->categories_id, $old_categories);

                foreach ($add_category as $add_category_id) {
                    $category_service->createEntryCategory($this->POST['entryId'], $add_category_id);
                }

                $delete_category = array_diff($old_categories, $this->categories_id);

                foreach ($delete_category as $delete_category_id) {
                    $category_service->deleteEntryCategory($this->POST['entryId'], $delete_category_id);
                }

                // Create or Update comment plugin data
                if ($this->hasOption(BrandOptions::OPTION_COMMENT,false)) {
                    /** @var CommentPluginService $comment_plugin_service */
                    $comment_plugin_service = $this->getService('CommentPluginService');
                    $comment_plugin = $comment_plugin_service->getCommentPlugin($this->getBrand()->id, $this->POST['entryId']);

                    if (!$comment_plugin) {
                        $comment_plugin = $comment_plugin_service->createEmptyCommentPlugin();
                        $comment_plugin->brand_id = $this->getBrand()->id;
                        $comment_plugin->static_html_entry_id = $this->POST['entryId'];
                        $comment_plugin->type = CommentPlugin::COMMENT_PLUGIN_TYPE_INTERNAL;
                        $comment_plugin->login_limit_flg = CommentPlugin::COMMENT_PLUGIN_LOGIN_LIMIT_FLG_ON;
                    }

                    $comment_plugin->title = $static_html_entry->title;
                    $comment_plugin->status = $this->POST['cp_status'];
                    $comment_plugin->active_flg = CommentPlugin::COMMENT_PLUGIN_ACTIVE_FLG_ON;
                    $comment_plugin_service->updateCommentPlugin($comment_plugin);

                    $comment_plugin_service->updateCommentPluginShareSettings($comment_plugin->id, $this->POST['cp_sns_list']);
                    $comment_plugin_service->updateCommentPluginAction($comment_plugin->id);
                }
            }

            $this->static_html_entry_service->createStaticHtmlEntryUsers($static_html_entry, $user->id);

            $static_html_entry_transaction->commit();

            if ($static_html_entry->isPublic()){
                $facebook_api_client = new FacebookApiClient();
                $facebook_api_client->accessObjectDebugger($static_html_entry->getUrl());
            }
        } catch (Exception $e) {
            $static_html_entry_transaction->rollback();
            $this->logger = aafwLog4phpLogger::getDefaultLogger();
            $this->logger->error('edit_static_html_entry error:' . $e);

            return 'redirect: ' . Util::rewriteUrl('admin-blog', 'edit_static_html_entry_form', array($static_html_entry->id), array('mid' => 'failed'));
        }

        if ($this->Validator->getErrorCount()) {
            $return = $this->getFormURL();
        } else {
            $this->Data['saved'] = 1;
            $return = 'redirect: ' . Util::rewriteUrl('admin-blog', 'edit_static_html_entry_form', array($static_html_entry->id), array('mid' => 'updated'));
        }
        return $return;
    }
}
