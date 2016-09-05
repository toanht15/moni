<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.StaticHtmlEntryValidator');
AAFW::import('jp.aainc.classes.validator.StaticHtmlEntryTemplateValidator');
class create_static_html_entry extends BrandcoPOSTActionBase {
    protected $ContainerName = 'create_static_html_entry';
    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'create_static_html_entry_form?mid=failed',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /**@var StaticHtmlEntryService $static_html_entry_service **/
    protected $static_html_entry_service;
    protected $brand;
    private $file_info;
    protected $ValidatorDefinition = array();
    protected $logger;
    private $categories_id;

    public function beforeValidate() {
        $this->static_html_entry_service = $this->createService('StaticHtmlEntryService');
        $this->brand = $this->getBrand();

        // ValidatorDefinition設定
        $static_html_validator = new StaticHtmlEntryValidator();
        $this->ValidatorDefinition = $static_html_validator->getValidatorDefinition($this->POST);
    }

    public function validate() {
        // ページ存在チェック
        $entry = $this->static_html_entry_service->getEntryByBrandIdAndPageUrl($this->brand->id, $this->page_url);
        if ($entry) $this->Validator->setError('page_url', 'EXISTED_PAGE_URL');

        $aValid = array('-', '_');
        if (!$this->isEmpty($this->page_url) &&!ctype_alnum(str_replace($aValid, '', $this->page_url))){
            $this->Validator->setError('page_url', 'NOT_ALPHANUMERIC_CHARACTER');
        }

        // ファイルチェック
        if ($this->FILES['og_image']) {
            $fileValidator = new FileValidator($this->FILES['og_image'], FileValidator::FILE_TYPE_IMAGE);
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

        // ファイル保存
        if ($this->FILES['og_image']) {
            // メインバナー画像 保存
            $this->POST['og_image_url'] = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $this->brand->id . '/static_html_entry/' . StorageClient::getUniqueId()), $this->file_info
            );
        }

        $static_html_entry_transaction = aafwEntityStoreFactory::create('StaticHtmlEntries');

        try{
            $static_html_entry_transaction->begin();

            /** @var UserService $user_service */
            $user_service = $this->createService('UserService');
            $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

            // プレーンモードの時はpageEntryは作らない
            if ($this->layout_type == StaticHtmlEntries::LAYOUT_PLAIN) {
                $static_html_entry = $this->static_html_entry_service->savePlainModeStaticHtmlEntry($this->brand->id, $user->id, $this->POST);
            } else {
                $cache_manager = new CacheManager();
                $cache_manager->deletePanelCache($this->getBrand()->id);

                // エントリーを保存
                $static_html_entry = $this->static_html_entry_service->createStaticHtmlEntry($this->brand->id, $user->id, $this->POST);

                // Save page_entry
                $page_stream_service = $this->createService('PageStreamService');
                $page_stream = $page_stream_service->getStreamByBrandId($this->getBrand()->id);
                $page_entry = $page_stream_service->createEmptyEntry();

                $page_top_hidden_flg = !$page_stream->panel_hidden_flg && $this->POST['display'] == 0 && $this->isPast($static_html_entry->public_date);
                if ($page_top_hidden_flg) {
                    $page_entry->top_hidden_flg = 0;
                }

                $page_entry->stream_id = $page_stream->id;
                $page_entry = $page_stream_service->staticHtmlToPageEntry($page_entry, $static_html_entry);

                $page_stream_service->updateEntry($page_entry);

                if ($page_top_hidden_flg) {
                    $panel_service = $this->createService('NormalPanelService');
                    $panel_service->addEntry($this->getBrand(), $page_entry);
                }

                // SNSプラグイン保存
                $this->static_html_entry_service->deleteStaticHtmlSnsPlugins($static_html_entry);
                if (count($this->sns_plugins)) {
                    $this->static_html_entry_service->createStaticHtmlSnsPlugins($static_html_entry, $this->sns_plugins);
                }

                // カテゴリー連携作成
                /** @var StaticHtmlCategoryService $category_service */
                $category_service = $this->createService('StaticHtmlCategoryService');
                foreach ($this->categories_id as $category_id) {
                    $category_service->createEntryCategory($static_html_entry->id, $category_id);
                }

                // Create comment plugin data
                if ($this->hasOption(BrandOptions::OPTION_COMMENT, false) && $this->POST['cp_status'] == CommentPlugin::COMMENT_PLUGIN_STATUS_PUBLIC) {
                    /** @var CommentPluginService $comment_plugin_service */
                    $comment_plugin_service = $this->getService('CommentPluginService');
                    $comment_plugin = $comment_plugin_service->createEmptyCommentPlugin();

                    $comment_plugin->brand_id = $this->getBrand()->id;
                    $comment_plugin->static_html_entry_id = $static_html_entry->id;
                    $comment_plugin->type = CommentPlugin::COMMENT_PLUGIN_TYPE_INTERNAL;
                    $comment_plugin->login_limit_flg = CommentPlugin::COMMENT_PLUGIN_LOGIN_LIMIT_FLG_ON;
                    $comment_plugin->title = $static_html_entry->title;
                    $comment_plugin->status = $this->POST['cp_status'];
                    $comment_plugin->active_flg = CommentPlugin::COMMENT_PLUGIN_ACTIVE_FLG_ON;
                    $comment_plugin_service->updateCommentPlugin($comment_plugin);

                    $comment_plugin_service->updateCommentPluginShareSettings($comment_plugin->id, $this->POST['cp_sns_list']);
                    $comment_plugin_service->updateCommentPluginAction($comment_plugin->id);
                }
            }

            // 作成者、編集者を保存
            $this->static_html_entry_service->createStaticHtmlEntryUsers($static_html_entry, $user->id);

            $static_html_entry_transaction->commit();

            if ($static_html_entry->isPublic()) {
                $facebook_api_client = new FacebookApiClient();
                $facebook_api_client->accessObjectDebugger($static_html_entry->getUrl());
            }

        } catch (Exception $e) {
            $static_html_entry_transaction->rollback();
            $this->logger = aafwLog4phpLogger::getDefaultLogger();
            $this->logger->error('create_static_html_entry error:' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-blog', 'create_static_html_entry_form', array(), array('mid' => 'register-failed'));
        }

        $this->Data['saved'] = 1;
        return 'redirect: ' . Util::rewriteUrl('admin-blog', 'edit_static_html_entry_form', array($static_html_entry->id), array('mid' => 'action-created'));
    }
}
