<?php
AAFW::import('jp.aainc.aafw.base.aafwControllerPluginBase');
AAFW::import('jp.aainc.classes.services.BrandService');
AAFW::import('jp.aainc.classes.services.base.BrandcoActionManagerBaseService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class Router extends aafwControllerPluginBase {

    use BrandcoActionManagerBaseService;

    /**
     * ドメイン・マッピングかつディレクトリ名を指定しないパターンのブランドのときに、
     * 元からディレクトリ名をしていない、パス・スルーすべきアクション名を指定します。
     *
     * 基本的に「actions/user/api」および「actions/user/auth」配下のすべてのアクションが対象となります。
     * もし、上記のフォルダにアクションを追加するときは、このアレイに新しく追加するアクション名を追加してください。
     *
     * @var array
     */
    private $PassThroughActions = array(
        'brandco/api/brands_users_relation',
        'brandco/api/get_brand',
        'brandco/api/withdraw',
        'brandco/auth/callback',
        'brandco/auth/google_callback',
        'brandco/auth/instgram_callback');

    protected $HookPoint = 'First';
    protected $Priority = 1;

    /** @var  BrandService brand_service */
    private $brand_service;

    public function __construct($controller) {
        parent::__construct($controller);
        $this->brand_service = new BrandService();
    }

    /**
     * リクエスト情報を元に、実行対象のアクションへのルーティングを実行します。
     */
    public function doService() {

        list($post, $get, $session, $cookie, $files, $env, $server, $request) = $this->Controller->getParams();
        if (@$get['action']) {
            if (@$get['directory_name']) {
                $get['package'] = 'brandco/' . $get['package'];
                $this->Controller->rewriteParams($post, $get, $session, $cookie, $files, $env, $server, $request);
            }
            return;
        }

        $mapped_brand_id = Util::getMappedBrandId();
        $is_default_domain = $mapped_brand_id === Util::NOT_MAPPED_BRAND;
        if ($is_default_domain) {
            $parsed_request_uri = Util::parseRequestUri($server['REQUEST_URI']);
            $this->loadSchemas($parsed_request_uri['package'], $parsed_request_uri['action']);
            if ($parsed_request_uri['directory_name']) {
                $brand_page_setting = $this->getBrandPageSetting($parsed_request_uri['directory_name']);
                if ($this->isReplaceTopPage($server['REQUEST_URI'], $parsed_request_uri, $brand_page_setting)) {
                    $parsed_request_uri = Util::parseRequestUri($brand_page_setting->top_page_url);
                }
            }
        } else {
            $brand = $this->brand_service->getBrandById($mapped_brand_id);
            BrandInfoContainer::getInstance()->initialize($brand);
            $parsed_request_uri = Util::parseRequestUri('/DUMMY' . $server['REQUEST_URI']);
            $mapped_server_name = Util::getMappedServerName($brand->id);

            $has_directory_name = false;
            $fqcn = $parsed_request_uri['package'] . '/' . $parsed_request_uri['action'];
            if ($brand->directory_name === $mapped_server_name && !in_array($fqcn, $this->PassThroughActions)) {
                $this->clearDirectoryName($parsed_request_uri);
            } else {
                $parsed_request_uri = Util::parseRequestUri($server['REQUEST_URI']);
                $has_directory_name = true;
            }

            $brand_page_setting = BrandInfoContainer::getInstance()->getBrandPageSetting();
            if ($this->isReplaceTopPage($server['REQUEST_URI'], $parsed_request_uri, $brand_page_setting)) {
                $top_page_url = $brand_page_setting->top_page_url;
                if ($has_directory_name) {
                    $parsed_request_uri = Util::parseRequestUri($top_page_url);
                } else {
                    $top_page_url = '/DUMMY' . $top_page_url;
                    $parsed_request_uri = Util::parseRequestUri($top_page_url);
                    $this->clearDirectoryName($parsed_request_uri);
                }
            }
            $this->loadSchemas($parsed_request_uri['package'], $parsed_request_uri['action']);
        }

        $get['__path'] = $parsed_request_uri['__path'];
        $get['directory_name'] = $parsed_request_uri['directory_name'];
        $get['req'] = $parsed_request_uri['req'];
        $get['exts'] = $parsed_request_uri['exts'];
        $get['action'] = $parsed_request_uri['action'];
        $get['package'] = $parsed_request_uri['package'];

        if (DEBUG) {
            aafwLog4phpLogger::getDefaultLogger()->debug("router result: " . json_encode($get, JSON_PRETTY_PRINT));
        }

        $this->Controller->rewriteParams($post, $get, $session, $cookie, $files, $env, $server, $request);
    }

    /**
     * BrandPageSettingを取得します。
     *
     * @param $directory_name
     * @return mixed
     */
    private function getBrandPageSetting($directory_name) {
        $brand = $this->brand_service->getBrandByDirectoryName($directory_name);
        if ($brand) {
            BrandInfoContainer::getInstance()->initialize($brand);
            return $brand_page_setting = BrandInfoContainer::getInstance()->getBrandPageSetting();
        } else {
            null;
        }
    }

    /**
     * request URIをトップページへのアクセスとして扱うかどうかを判定します。
     *
     * @param array $request_uri
     * @return bool
     */
    private function isReplaceTopPage($request_uri = array(), $parsed_request_uri, $brand_page_setting) {
        if ($brand_page_setting === null || !$brand_page_setting->top_page_url || !count($request_uri)) {
            return false;
        }

        return (($parsed_request_uri['package'] == 'brandco/' && $parsed_request_uri['action'] == 'index') ||
            $request_uri['REQUEST_URI'] == $brand_page_setting->top_page_url);
    }

    /**
     * ドメイン・マッピングかつディレクトリ名がないパターンのときに、parsed_request_uriから不必要な項目を削除します。
     *
     * @param $parsed_request_uri
     * @return mixed
     */
    public function clearDirectoryName(&$parsed_request_uri) {
        unset($parsed_request_uri['__path'][0]); // delete DUMMY
        $parsed_request_uri['directory_name'] = null;
    }

    /**
     * 必要なスキーマをキャッシュ・サーバからまとめて取得します。
     *
     * @param $package
     * @param $action
     */
    private function loadSchemas($package, $action) {
        $table_names = null;
        if ($package === 'brandco/' && $action === 'campaigns') {
                $table_names = array(
                    'brand_social_accounts',
                    'brands_users_relations',
                    'brand_page_settings',
                    'brand_contracts',
                    'cps',
                    'cp_join_limit_sns',
                    'cp_action_groups',
                    'cp_users',
                    'cp_restricted_addresses',
                    'cp_actions',
                    'cp_next_actions',
                    'cp_message_delivery_reservations',
                    'cp_message_delivery_targets',
                    'cp_instant_win_actions',
                    'cp_transactions',
                    'instant_win_prizes',
                    'cp_user_action_messages',
                    'cp_user_action_statuses',
                    'managers',
                    'brand_global_settings'
                );
        } else if ($package === 'brandco/messages' && $action === 'thread') {
            $table_names = array(
                    'brand_social_accounts',
                    'brands_users_relations',
                    'brand_page_settings',
                    'brand_contracts',
                    'users',
                    'cp_users',
                    'cp_actions',
                    'cp_user_action_messages',
                    'cp_user_action_statuses',
                    'cps',
                    'cp_join_limit_sns',
                    'cp_action_groups',
                    'cp_restricted_addresses',
                    'managers',
                    'cp_next_actions',
                    'cp_entry_actions',
                    'cp_questionnaire_actions',
                    'cp_join_finish_actions',
                    'brandco_social_accounts',
                    'cp_message_delivery_reservations',
                    'cp_message_delivery_targets',
                    'brand_global_settings',
                    'cp_profile_questionnaires',
                    'cp_transactions'
                );
        } else if ($package === 'brandco/photo' && $action === 'detail') {
                $table_names = array(
                    'brand_social_accounts',
                    'brands_users_relations',
                    'brand_page_settings',
                    'brand_contracts',
                    'photo_streams',
                    'photo_entries',
                    'crawler_hosts',
                    'crawler_types',
                    'crawler_urls',
                    'photo_users',
                    'users',
                    'cp_users',
                    'cp_actions',
                    'cp_user_action_messages',
                    'cp_user_action_statuses',
                    'cps',
                    'cp_join_limit_sns',
                    'cp_action_groups',
                    'cp_restricted_addresses',
                    'managers',
                    'cp_next_actions',
                    'cp_entry_actions',
                    'cp_questionnaire_actions',
                    'cp_join_finish_actions',
                    'brandco_social_accounts',
                    'cp_message_delivery_reservations',
                    'cp_message_delivery_targets',
                    'brand_global_settings',
                    'cp_photo_actions',
                    'photo_user_shares',
                    'cp_message_actions'
                );
        } else if ($package === 'brandco/' && $action === 'page') {
            $table_names = array(
                    'brand_social_accounts',
                    'brands_users_relations',
                    'brand_page_settings',
                    'brand_contracts',
                    'static_html_entries',
                    'static_html_sns_plugins',
                    'static_html_entry_users',
                    'users',
                    'managers',
                    'static_html_categories',
                    'static_html_category_relations',
                    'static_html_entry_categories',
                    'static_html_category_sns_plugins',
                    'brand_global_settings',
                    'rss_streams',
                    'rss_entries',
                    'crawler_hosts',
                    'crawler_types',
                    'crawler_urls'
            );
        } else if ($package === "brandco/messages" && $action === "api_update_personal_info_and_execute_entry") {
            $table_names = array(
                    'brand_social_accounts',
                    'brands_users_relations',
                    'brand_page_settings',
                    'brand_contracts',
                    'brand_global_settings',
                    'brand_global_menus',
                    'users',
                    'cp_users',
                    'cp_actions',
                    'cp_user_action_messages',
                    'cp_user_action_statuses',
                    'cps',
                    'brandco_social_accounts',
                    'user_applications',
                    'user_attributes',
                    'user_search_info',
                    'shipping_addresses',
                    'profile_questionnaires_questions_relations',
                    'profile_questionnaire_questions',
                    'profile_question_choice_requirements',
                    'profile_question_choices',
                    'profile_question_choice_answers',
                    'profile_question_free_answers',
                    'profile_choice_answer_histories',
                    'profile_free_answer_histories',
                    'cp_action_groups',
                    'cp_next_actions',
                    'cp_join_limit_sns',
                    'cp_restricted_addresses',
                    'managers',
                    'profile_questionnaires',
                    'profile_questionnaire_answers',
                    'old_new_question_relations',
                    'cp_message_delivery_reservations',
                    'cp_message_delivery_targets',
                    'cp_shipping_address_actions',
                    'cp_entry_actions',
                    'cp_questionnaire_actions',
                    'prefectures',
                    'regions'
            );
        } else if ($package === "brandco/messages" && $action === "join") {
            $table_names = array(
                'brand_social_accounts',
                'brands_users_relations',
                'brand_page_settings',
                'brand_contracts',
                'users',
                'cps',
                'cp_actions',
                'cp_action_groups',
                'cp_next_actions',
                'cp_users',
                'cp_join_limit_sns',
                'cp_restricted_addresses',
                'managers',
                'cp_user_action_messages',
                'cp_user_action_statuses',
                'brandco_social_accounts',
                'cp_message_delivery_reservations',
                'cp_message_delivery_targets',
                'cp_entry_actions',
                'cp_questionnaire_actions',
                'resend_cp_user_status_logs',
                'cp_profile_questionnaires',
                'cp_shipping_address_actions'
            );
        } else if ($package === "brandco/auth" && $action === "signup") {
            $table_names = array(
                'brand_social_accounts',
                'brands_users_relations',
                'brand_page_settings',
                'brand_contracts',
                'admin_invite_tokens',
                'managers',
                'users',
                'user_attributes',
                'user_search_info',
                'shipping_addresses',
                'brand_global_settings',
                'profile_questionnaires_questions_relations',
                'profile_questionnaire_questions',
                'profile_question_choice_requirements',
                'profile_question_choices',
                'profile_question_choice_answers',
                'profile_question_free_answers',
                'profile_choice_answer_histories',
                'profile_free_answer_histories',
                'prefectures',
                'regions',
                'aa_functions'
            );
        } else if ($package === "brandco/admin-cp" && $action === "api_click_link") {
            $table_names = array(
                'brand_social_accounts',
                'brands_users_relations',
                'brand_page_settings',
                'brand_contracts',
                'users',
                'cps',
                'cp_actions',
                'cp_action_groups',
                'cp_next_actions',
                'cp_users',
                'cp_message_delivery_reservations',
                'cp_message_delivery_targets',
                'cp_join_limit_sns',
                'cp_restricted_addresses',
                'managers',
                'operation_log_admin_data',
                'brand_global_settings',
                'clicked_email_link_logs'
            );
        }

        if ($table_names !== null) {
            aafwEntityStoreBase::loadCatalogs($table_names);
        }
    }
}