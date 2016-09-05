<?php
/**
 * Created by IntelliJ IDEA.
 * User: kanebako
 * Date: 2014/03/10
 * Time: 午後4:54
 * To change this template use File | Settings | File Templates.
 */

AAFW::import('jp.aainc.aafw.aafwApplicationConfig');
AAFW::import('jp.aainc.classes.services.ApplicationService');
AAFW::import('jp.aainc.lib.session.persistent_session.RedisPersistentSession');
AAFW::import('jp.aainc.aafw.web.aafwWidgets');
AAFW::import('jp.aainc.classes.services.base.BrandcoActionBaseInterface');
AAFW::import('jp.aainc.classes.entities.BrandOption');
AAFW::import('jp.aainc.classes.BrandInfoContainer');
AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.classes.GAMPClient');
AAFW::import('jp.aainc.classes.core.UserAttributeManager' );

trait BrandcoActionBaseService {

    protected $facebook               = null;
    protected $facebook_user          = null;
    protected $twitter                = null;
    protected $twitter_user           = null;
    protected $google                 = null;
    protected $google_user            = null;
    protected $googlePlus             = null;
    protected $instagram              = null;

    protected $moniplaCore            = null;
    protected $config                 = null;
    protected $mode                   = self::BRANDCO_MODE_USER;

    /** @var Brand */
    protected $brand                  = null;

    /** @var BrandsUsersRelation */
    protected $brands_users_relation  = null;

    protected $brand_fan_count = null;

    protected $set_log_flg            = false;
    public    $NeedAdminLogin         = false;
    public    $CsrfProtect            = false;
    public    $NeedPublic             = false;

    protected $aa_function            = '';

    public function setMoniplaCore ( $core ) {
        $this->moniplaCore = $core;
    }

    public function getMoniplaCore () {
        if ( $this->moniplaCore == null ) {
            AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
            $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        }
        return $this->moniplaCore;
    }

    public function setMode ( $mode ) {
        $this->mode = $mode;
    }

    public function getMode () {
        return $this->mode;
    }

    public function setFacebook ( $obj ) {
        $this->facebook = $obj;
    }

    public function getFacebook () {
        if ( $this->facebook == null )  {
            AAFW::import('jp.aainc.classes.FacebookApiClient');
            $this->facebook = new FacebookApiClient();
        }
        return $this->facebook;
    }

    public function getFacebookUser () {
        if ( $this->facebook_user == null )  {
            AAFW::import('jp.aainc.classes.FacebookApiClient');
            $this->facebook_user = new FacebookApiClient(FacebookApiClient::BRANDCO_MODE_USER);
        }
        return $this->facebook_user;
    }

    public function setTwitter ( $obj ) {
        $this->twitter = $obj;
    }

    public function getTwitter () {
        if ( $this->twitter == null )  {
            AAFW::import('jp.aainc.vendor.twitter.Twitter');
            $this->twitter = new Twitter($this->config->query('@twitter.Admin.ConsumerKey'), $this->config->query('@twitter.Admin.ConsumerSecret'));
        }
        return $this->twitter;
    }

    public function getTwitterUser($tw_oauth_token,$tw_oauth_token_secret){
        if ( $this->twitter_user == null )  {
            AAFW::import('jp.aainc.vendor.twitter.Twitter');
            $this->twitter_user = new Twitter($this->config->query('@twitter.User.ConsumerKey'), $this->config->query('@twitter.User.ConsumerSecret'),
                $tw_oauth_token, $tw_oauth_token_secret);
        }
        return $this->twitter_user;
    }

    public function getGoogle(){
    	if( $this->google == null){
            AAFW::import('jp.aainc.vendor.google.Google_Client');
            AAFW::import('jp.aainc.vendor.google.contrib.Google_PlusService');
            AAFW::import('jp.aainc.vendor.google.contrib.Google_YouTubeService');
            AAFW::import('jp.aainc.vendor.google.contrib.Google_Oauth2Service');
    		$client = new Google_Client();
    		$client->setClientId($this->config->query('@google.Google.ClientID'));
    		$client->setClientSecret($this->config->query('@google.Google.ClientSecret'));
    		$client->setRedirectUri(Util::getHttpProtocol().'://'. Util::getDefaultBRANDCoDomain() . '/'.$this->config->query('@google.Google.RedirectUri'));
    		$scope = array();
    		$apiBase = $this->config->query('@google.Google.ApiBaseUrl');
    		foreach ($this->config->query('@google.Google.Scope') as $url) {
    			array_push($scope, $apiBase.'/'.$url);
    		}
    		$client->setScopes($scope);
    		$this->google = $client;
    	}
    	return $this->google;
    }

    public function getGoogleUser(){
        if( $this->google == null){
            AAFW::import('jp.aainc.vendor.google2.Client');
            AAFW::import('jp.aainc.vendor.google2.Service.Google_Service_Oauth2');
            AAFW::import('jp.aainc.vendor.google2.Service.Google_Service_YouTube');
            $client = new Google_Client();
            $client->setClientId($this->config->query('@google.User.ClientID'));
            $client->setClientSecret($this->config->query('@google.User.ClientSecret'));
            $client->setRedirectUri(Util::getHttpProtocol().'://'. Util::getDefaultBRANDCoDomain() . '/'.$this->config->query('@google.User.RedirectUri'));
            $scope = array();
            $apiBase = $this->config->query('@google.User.ApiBaseUrl');
            foreach ($this->config->query('@google.User.Scope') as $url) {
                array_push($scope, $apiBase.'/'.$url);
            }
            $client->setScopes($scope);
            $this->google = $client;
        }
        return $this->google;
    }

    public function getInstagram() {
        if ($this->instagram == null) {
            AAFW::import('jp.aainc.vendor.instagram.Instagram');

            $this->instagram = new Instagram();
            $this->instagram->setClientId($this->config->query('@instagram.Admin.ClientID'));
            $this->instagram->setClientSecret($this->config->query('@instagram.Admin.ClientSecret'));
            $this->instagram->setScope($this->config->query('@instagram.Admin.Scopes'));

            $redirect_uri = Util::getHttpProtocol() . '://' . Util::getDefaultBRANDCoDomain() . '/' . $this->config->query('@instagram.Admin.RedirectUri');
            $this->instagram->setRedirectUri($redirect_uri);

            //CSRF対策
            //TODO prevent from session_regenerate_id
            $this->instagram->setCsrfToken(session_id());
        }

        return $this->instagram;
    }

    /**
     * @return Brand
     */
    public function getBrand () {
        if ( $this->brand == null ) {
            $this->brand = BrandInfoContainer::getInstance()->getBrand();
        }
        return $this->brand;
    }

    public function getBrandsUsersRelation () {
        if ( $this->brands_users_relation == null )  {
            $brand = $this->getBrand();
            $userId = $this->getSession('pl_monipla_userId');
            $loginBrandIds = $this->getSession('pl_loginBrandIds');

            // セッションにブランドログイン情報とユーザー情報を持っているのをチェック
            if ($userId && $loginBrandIds && $loginBrandIds[$brand->id]) {
                /** @var BrandsUsersRelationService $brands_users_relation_service */
                $brands_users_relation_service = $this->createService('BrandsUsersRelationService');
                $this->brands_users_relation = $brands_users_relation_service->getBrandsUsersRelation($brand->id, $userId, array('withdraw_flg' => 0));
            }
        }
        return $this->brands_users_relation;
    }

    public function getFanCountInfo() {
        if ($this->brand_fan_count === null) {
            $cache_manager = new CacheManager();
            $this->brand_fan_count = $cache_manager->getBrandFanCount($this->getBrand()->id);
            if ($this->brand_fan_count === false) {
                $this->brand_fan_count = $this->getBrandUsersNum();
                $cache_manager->setBrandFanCount($this->getBrand()->id, $this->brand_fan_count);
            }
        }

        $fan_count_info['users_num'] = $this->brand_fan_count;
        $fan_count_info['is_users_num_visible'] = $this->checkUsersNum($this->brand_fan_count);

        return $fan_count_info;
    }

    public function getBrandUsersNum() {
        $manager_kpi_service = $this->createService('ManagerBrandKpiService');
        $column_name = 'jp.aainc.classes.manager_brand_kpi.BrandsUsersNum';
        $date = date('Y-m-d');

        $kpi_column = $manager_kpi_service->getColumn(array('import' => $column_name));
        $users_num = $kpi_column->getValue($date, $this->getBrand()->id);

        if (!$users_num || $users_num == '-') {
            $users_num = $manager_kpi_service->doExecute($kpi_column->import, $date, $this->getBrand()->id);
        }

        return $users_num ? $users_num : 0;
    }

    public function checkUsersNum($users_num) {
        return $users_num >= self::USERS_NUMBER_LIMIT ? true : false;
    }

    public function isAccessibleOnClosedMode() {
        if (isset($this->ClosedModeAccess) && $this->ClosedModeAccess === true) {
            return true;
        }

        return false;
    }

    public function doService() {
        if ($this->getBrand()) {
            if(!$this->isLoginManager()) {
                $contract = BrandInfoContainer::getInstance()->getBrandContract();
                if ($contract->getCloseStatus() === BrandContracts::MODE_CLOSED && !$this->isAccessibleOnClosedMode()) {
                    return 'redirect: ' . Util::rewriteUrl('', 'closed');
                } elseif($contract->getCloseStatus() === BrandContracts::MODE_SITE_CLOSED) {
                    return '404';
                }
            }

            if ($this->getBrand()->test_page == Brand::BRAND_TEST_PAGE ) {

                $brand_page_setting = BrandInfoContainer::getInstance()->getBrandPageSetting();

                switch (true) {
                    case !$brand_page_setting->test_id || !$brand_page_setting->test_pass:
                    case !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']):
                    case $_SERVER['PHP_AUTH_USER'] != $brand_page_setting->test_id:
                    case $_SERVER['PHP_AUTH_PW'] != $brand_page_setting->test_pass:
                        header('WWW-Authenticate: Basic realm="Please log in with brand\'s account"');
                        header('Content-Type: text/plain; charset=utf-8');

                        die('このページを見るにはログインが必要です');
                }
            }
        }

        $this->config         = aafwApplicationConfig::getInstance();
        $this->Data['pageStatus'] = $this->getLoginInfo();
        $this->Data['pageStatus']['brand'] = $this->getBrand();

        $this->getBrandGlobalSettings();

        if($this->Data['pageStatus']['can_set_header_tag_setting']){
            $this->setHeaderTagText();
        }

        $this->Data['brand'] = $this->getBrand();
        $this->Data['directory_name'] = $this->GET['directory_name'];

        if(!$this->brand) {
            return '404';
        }

        if(!$this->isPublic($this->brand->id, BrandInfoContainer::getInstance()->getBrandPageSetting())) {
            if(!$this->isLoginAdmin()) {
                return '404';
            }
        }

        if($this->isValidPageOption() === false) {
            return $this->redirectDefaultPage();
        }

        if ($_SERVER['HTTP_REFERER'] && (!in_array(parse_url($_SERVER['HTTP_REFERER'])['host'], array(Util::getMappedServerName($this->getBrand()->id), $this->config->query('Domain.aaid'))))) {

            $this->setSession('bc_ref_'.$this->brand->id, $_SERVER['HTTP_REFERER']);

            if (!$this->isLogin()) {
                $this->setSession('referrer_'.$this->brand->id, $_SERVER['HTTP_REFERER']);
            }
        }

        if ($this->GET['fid']) {

            $this->setSession('bc_fid_'.$this->brand->id, $this->GET['fid']);

            if (!$this->isLogin()) {
                $this->setSession('fid_'.$this->brand->id, $this->GET['fid']);
            }
        }

        return parent::doService(!$this->brand);
    }

    private function isValidPageOption() {
        //GETのみチェックする。
        if($_SERVER['REQUEST_METHOD'] != 'GET'){
            return true;
        }

        if( isset($this->NeedOption) ) {
            if(count($this->NeedOption) == 0){
                return true;
            }else{
                foreach($this->NeedOption as $option) {
                    if ($this->getBrand()->hasOption($option, BrandInfoContainer::getInstance()->getBrandOptions())) {
                        return true;
                    }
                }
                return false;
            }
        }

        $request = util::parseRequestUri($_SERVER['REQUEST_URI']);
        aafwLog4phpLogger::getHipChatLogger()->info('no needOption! package = ' . $request['package'] .' action = ' . $request['action']);
        return false;
    }

    private function redirectDefaultPage(){
        $options = BrandInfoContainer::getInstance()->getBrandOptions();
        if( $this->getBrand()->hasOption(BrandOptions::OPTION_TOP, $options) ) {
            return 'redirect: ' . Util::rewriteUrl('', '');
        } elseif( $this->getBrand()->hasOption(BrandOptions::OPTION_CP, $options) && $this->Data['pageStatus']['isLoginAdmin'] ) {
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'public_cps', array(), array('type' => Cp::TYPE_CAMPAIGN));
        }

        return '404';
    }

    private function getBrandGlobalSettings(){

        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');

        $can_set_header_tag_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SET_HEADER_TAG_TEXT);
        $this->Data['pageStatus']['can_set_header_tag_setting'] = !Util::isNullOrEmpty($can_set_header_tag_setting);

        $can_set_sign_up_mail_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SET_SIGN_UP_MAIL);
        $this->Data['pageStatus']['can_set_sign_up_mail'] = !Util::isNullOrEmpty($can_set_sign_up_mail_setting);

        $olympus_brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::OLYMPUS_CUSTOM_HEADER_FOOTER);
        $this->Data['pageStatus']['is_olympus_header_footer'] = !Util::isNullOrEmpty($olympus_brand_global_setting);

        $whitebelg_brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::WHITEBELG_CUSTOM_HEADER_FOOTER);
        $this->Data['pageStatus']['is_whitebelg_header_footer'] = !Util::isNullOrEmpty($whitebelg_brand_global_setting) && !$this->isLoginAdmin()?true:false;

        $kenken_brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::KENKEN_CUSTOM_HEADER_FOOTER);
        $this->Data['pageStatus']['is_kenken_header_footer'] = !Util::isNullOrEmpty($kenken_brand_global_setting);

        $sugao_brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::SUGAO_CUSTOM_LOGIN_AND_TWITTER_ACTION);
        $this->Data['pageStatus']['is_sugao_brand'] = !Util::isNullOrEmpty($sugao_brand_global_setting);

        $uq_brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::UQ_CUSTOM_HEADER_FOOTER);
        $this->Data['pageStatus']['is_uq_account'] = !Util::isNullOrEmpty($uq_brand_global_setting);
        $this->Data['pageStatus']['is_uq_header_footer'] = !Util::isNullOrEmpty($uq_brand_global_setting) && !$this->isLoginAdmin()?true:false;

        $hide_header_login_button_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_HEADER_LOGIN_BUTTON);
        $this->Data['pageStatus']['hide_header_login_button'] = !Util::isNullOrEmpty($hide_header_login_button_global_setting);

        $hide_brand_logo_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_BRAND_LOGO);
        $this->Data['pageStatus']['hide_brand_logo'] = !Util::isNullOrEmpty($hide_brand_logo_global_setting);

        $hide_inquiry_link_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_INQUIRY_LINK);
        $this->Data['pageStatus']['hide_inquiry_link'] = !Util::isNullOrEmpty($hide_inquiry_link_global_setting);

        $hide_footer_menu_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_FOOTER_MENU);
        $this->Data['pageStatus']['hide_footer_menu'] = !Util::isNullOrEmpty($hide_footer_menu_global_setting);
    }

    private function setHeaderTagText(){

        $brandPageSetting = BrandInfoContainer::getInstance()->getBrandPageSetting();

        $this->Data['pageStatus']['header_tag_text'] = $brandPageSetting->header_tag_text;

    }

    public function setLogout(BrandsUsersRelation $brands_users_relations) {
        $pl_loginBrandIds = $this->getSession('pl_loginBrandIds');

        $this->brands_users_relation = null;
        unset($pl_loginBrandIds[$brands_users_relations->brand_id]);

        if (is_array($pl_loginBrandIds) && $pl_loginBrandIds != null) {
            $this->setSession('pl_loginBrandIds', $pl_loginBrandIds);
        } else {
            foreach (preg_grep( '#^pl#', array_keys($this->SESSION) ) as $key) {
                $this->setSession($key,null);
            }
        }
    }

    public function setLogin ( BrandsUsersRelation $brands_users_relations ) {
        if ($this->getSession('pl_monipla_userId') && $this->getSession('pl_monipla_userId') == $brands_users_relations->user_id ) {
            $loginBrandIds = $this->getSession('pl_loginBrandIds');
        }
        $loginBrandIds[$brands_users_relations->brand_id] = 1;
        $this->setSession('pl_loginBrandIds', $loginBrandIds);
        $this->setSession('pl_monipla_userId', $brands_users_relations->user_id);

        if($brands_users_relations->admin_flg) {
            // 管理者のログイン情報を記録
            $login_log_admin_data_service = $this->createService('LoginLogAdminDataService');
            $login_log_admin_data_service->setLoginLog( $brands_users_relations->brand_id, $brands_users_relations->user_id );
        }

        // ユーザ側のログイン情報を記録(管理者については、ユーザ側にも記録する)
        $login_log_data_service = $this->createService('LoginLogDataService');
        $login_log_data_service->setLoginLog( $brands_users_relations->brand_id, $brands_users_relations->user_id );

        $brands_users_relations_service = $this->createService('BrandsUsersRelationService');
        $brands_users_relations_service->setLoginInfo( $brands_users_relations );
    }

    public function isLogin () {
        // ブランドユーザー関連テーブルに存在する
        return $this->getBrandsUsersRelation() ? true : false;
    }

    public function isLoginAdmin () {

        // セッションにブランドログイン情報を持っている && ブランドユーザー関連テーブルに存在する
        if($this->isLogin()) {
            $brands_users_relation = $this->getBrandsUsersRelation();

            if($brands_users_relation->admin_flg || $this->isLoginManager()) {

                // オペレーションログを記録する
                if(!$this->set_log_flg) {
                    $operationLog = $this->createService('OperationLogAdminDataService');
                    $operationLog->setOperationReferer($_SERVER['HTTP_REFERER']); //セッションにリファラの値をセット
                    $from_manager = $this->isLoginManager() ? OperationLogAdminDataService::MANAGER_LOGIN : OperationLogAdminDataService::NOT_MANAGER_LOGIN;
                    $operationLog->setOperationLog($brands_users_relation->user_id, $brands_users_relation->brand_id, $from_manager);
                    $this->set_log_flg = true;
                }
                return true;
            }
        }
        return false;
    }

    public function isLoginManager($managerAccount=null) {
        if ($managerAccount === null) {
            $managerAccount = $this->getManager();
        }

        if (!$managerAccount->id) return false;

        if ($managerAccount->authority == Manager::AGENT) {
            if (!$this->allowedBrandAgent($managerAccount)) return false;
        }

        return true;
    }

    /**
     * 業務委託先がブランドの管理者権限を許可されているか
     * @param $managerAccount
     * @return bool
     */
    public function allowedBrandAgent($managerAccount) {
        /** @var BrandsAgent $brandAgentStore */
        $brandAgentStore = $this->getModel('BrandsAgents');
        $condition = [
            'manager_id' => $managerAccount->id,
            'brand_id' => $this->getBrand()->id
        ];
        return $brandAgentStore->findOne($condition) ? true : false;
    }

    public function getManager () {
        $managerUserId = $this->getSession('managerUserId');

        if($managerUserId === null && $this->GET[ManagerService::TOKEN_KEY]) {
            if(Util::isManagerIp()) {
                $managerUserId = ManagerService::verifyOnetimeToken($this->GET[ManagerService::TOKEN_KEY]);
            }
        }
        /** @var ManagerService $managerAccount */
        $managerAccount = $this->createService('ManagerService');
        if ($managerUserId) {
            $managerAccount = $managerAccount->getManagerFromHash($managerUserId);
            if ($managerAccount) {
                $this->setSession('managerUserId', $managerUserId);
                return $managerAccount;
            }
        }
        return $managerAccount->createEmptyManager();
    }

    public function getFormURL () {
        $form_url = null;
        $baseUrl = Util::getBaseUrl();

        if ( $this->Form['package'] ) $form_url = 'redirect: '.$baseUrl. preg_replace ( '#^/#' , '', $this->Form['package'] ) . '/' . $this->Form['action'];
        else                          $form_url = 'redirect: '.$baseUrl. preg_replace ( '#^/#' , '', $this->Form['action'] );
        $params = $this->REQUEST;
        return preg_replace_callback ( '#\{(.+?)\}#', function ( $m ) use ( $params ) {
            return $params[$m[1]];
        }, $form_url );
    }

    public function getLoginInfo(){
        $info = array();
        $info['isLogin'] = $this->isLogin();
        // プレビュー、スマホの時はfalse
        $info['isLoginAdmin'] = ($this->preview) ? false : ( Util::isSmartPhone() ? false : $this->isLoginAdmin() );
        $info['freeAreaPreview'] = ($this->preview && $this->free_area_entry_preview == true) ? $this->free_area_entry_preview : false;
        $info['userInfo'] = $this->isLogin() ? (object)$this->getSession('pl_monipla_userInfo') : null;

        $info['manager'] = $this->getManager();
        $info['isAgent'] = $info['manager']->authority == Manager::AGENT;
        $info['isLoginManager'] = $this->isLoginManager($info['manager']);

        $info['public_flg'] = BrandInfoContainer::getInstance()->getBrandPageSetting()->public_flg;
        $info['NeedAdminLogin'] = $this->NeedAdminLogin;

        return $info;
    }

    public function isPublic($brandId, $page_setting = null) {
        /** @var BrandPageSettingService $page_setting_service */
        $page_setting_service = $this->createService('BrandPageSettingService');

        //公開状態の時
        if($page_setting_service->isPublic($brandId, $page_setting) || $this->NeedPublic) {
            return true;
        }

        //managerでログインしている時
        if($this->isLoginManager()) {
            return true;
        }

        return false;
    }

    public function deleteErrorSession() {
        if(!$this->SESSION[ 'ActionContainer' ][$this->getContainerType()][ $this->ContainerName ]['Errors']
            || count($this->SESSION[ 'ActionContainer' ][$this->getContainerType()][ $this->ContainerName ]['Errors']->getErrorCount()) < 1) {
            $this->SESSION[ 'ActionContainer' ][$this->getContainerType()][ $this->ContainerName ] = null;
        }
    }

    /**
     * Override
     */
    public function getContainerType() {
        return $this->getBrand()->id;
    }


    /**
     * Return -1 if user don't have this type of SNS
     * @param $user_info
     * @param $sns_type
     * @return int
     */
    public function getSNSAccountId($user_info, $sns_type) {
        foreach ($user_info->socialAccounts as $social_account) {
            if ($social_account->socialMediaType == $sns_type) {
                return $social_account->socialMediaAccountID;
            }
        }

        return -1;
    }

    public function getAAFunction() {
        $aa_functions = $this->getModel('AaFunctions');
        $aa_function = $aa_functions->findOne(array('package' => $this->GET['package'], 'action' => $this->GET['action']));
        return $this->aa_function.$aa_function->note;
    }

    public function addAAFunction($string) {
        return $this->aa_function = $this->aa_function.$string;
    }

    public function canShareExternalPage() {

        if($this->isLoginManager()){
            return true;
        }

        return false;
    }

    public function setSearchConditionSession($cpId,$value) {
        $session = $this->getBrandSession('searchCondition');
        if($value == null){
            unset($session[$cpId]);
        }else{
            $session[$cpId] = $value;
        }
        $this->setBrandSession('searchCondition', $session);
    }

    public function getSearchConditionSession($cpId) {
        $session = $this->getBrandSession('searchCondition');
        return $session[$cpId];
    }

    public function isSkipAgeAuthentication(){

        if($_COOKIE['restrict_age']){
            return true;
        }

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::AUTHENTICATION_PAGE);

        if(Util::isNullOrEmpty($brand_global_setting)){
            return true;
        }

        $brand_page_setting = BrandInfoContainer::getInstance()->getBrandPageSetting();
        if(!$brand_page_setting->privacy_required_restricted || !$brand_page_setting->age_authentication_flg){
            return true;
        }

        if($this->isLoginAdmin()){
            return true;
        }

        if($this->isLogin()){

            $user_info = $this->getLoginInfo()['userInfo'];

            $userAttributeManager = new UserAttributeManager($user_info, $this->getMoniplaCore());

            $birthday = $userAttributeManager->getBirthDay();

            if($birthday){
                $user_age = Util::getUserAge($birthday);
                if($user_age >= $brand_page_setting->restricted_age){
                    return true;
                }
            }
        }

        return false;
    }

    public function checkValidSNSAccount($user_info) {
        return $user_info && is_array($user_info->socialAccounts) && !empty($user_info->socialAccounts);
    }

    public function setSpecialFanCookie() {
        /** @var BrandGlobalSettingService $brandGlobalSettingService */
        $brandGlobalSettingService = $this->createService('BrandGlobalSettingService');
        $brandGlobalSetting = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::IS_SNS_CONNECTING_DISABLED);

        if (Util::isNullOrEmpty($brandGlobalSetting)) return;

        if (!$this->isLogin()) return;

        $userInfo = (object)$this->getSession('pl_monipla_userInfo');

        if ($this->checkValidSNSAccount($userInfo)) {

            if (isset($_COOKIE['special_fan'])) {
                // Unsupported domain mapping brands
                unset($_COOKIE['special_fan']);
                setcookie('special_fan', '', time() - 1, "/" . $this->getBrand()->directory_name);
            }
            return;
        }

        $brand_users = array(
            '452' => array(11059,12167,16318,42087,43024,72303,84272,84959,114332,135444,151919,212599,239347,323263,585206,681014,703649,770029,770030,770031,770032,770033,770034,770035,770036,770037,770038,770039,770040,770041,770042,770043,770044,770045,770046,770047,770048,770049,770050,770051,770052,770053,770054,770055,770056,770057,770058,770059,770060,770061,770062,770063,770064,770065,770066,770067,770068,770069,770070,770071,770072,770073,770074,770075,770076,770077,770078,770079,770080,770081,770082,770083,770084,770085,770086,770087,770088,770089,770090,770091,770092,770093,770094,770095,770096,770097,770098,760392,767571,767570),
            '453' => array(699,7179,16318,155607,184896,256411,283146,770099,770100,770101,770102,770103,770104,770105,770106,770107,770108,770109,770110,770111,770112,770113,770114,770115,770116,770117,770118,770119,770120,770121,770122,770123,770124,770125,770126,770127,770128,770129,770130,770131,770132,770133,770134,770135,770136,770137,770138,770139,770140,770141,770142,770143,770144,770145,770146,770147,770148,770149,770150,770151,770152,770153,770154,770155,770156,770157,770158,770159,770160,770161,770162,770163,770164,770165,770166,770167,770168,770169,770170,770171,770173,770174,770175,770176,770177,770178,770179,770180,770181,770182,770183,770184,770185,770186,770187,770188,770189,770190,770191,770192,760392,767577,767576)
        );
        if (!in_array($this->getSession('pl_monipla_userId'), $brand_users[$this->getBrand()->id])) return;

        // Unsupported domain mapping brands
        $expire = 6 * 30 * 24 * 60 * 60;
        setcookie('special_fan', "true", time() + $expire, "/" . $this->getBrand()->directory_name);
    }

    public function canAddEmbedPage(){

        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');

        $embed_page_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_ADD_EMBED_PAGE);
        
        if(!Util::isNullOrEmpty($embed_page_setting)){
            return true;
        }

        return false;
    }

    public function setCpFromIdSession($cpId){

        if($this->getSession('cp_fid_'.$cpId)) {
            return;
        }

        if ($this->getSession('bc_fid_'.$this->getBrand()->id)) {
            $this->setSession('cp_fid_'.$cpId, $this->getSession('bc_fid_'.$this->getBrand()->id));
            $this->setSession('bc_fid_'.$this->getBrand()->id, null);
        } else if ($this->GET['fid']) {
            $this->setSession('cp_fid_'.$cpId, $this->GET['fid']);
        }

    }
    
    public function setCpRefererSession($cpId){

        if($this->getSession('cp_ref_'.$cpId)) {
            return;
        }

        if ($this->getSession('bc_ref_'.$this->getBrand()->id)) {
            $this->setSession('cp_ref_'.$cpId, $this->getSession('bc_ref_'.$this->getBrand()->id));
            $this->setSession('bc_ref_'.$this->getBrand()->id, null);
        } else if ($_SERVER['HTTP_REFERER'] && (!in_array(parse_url($_SERVER['HTTP_REFERER'])['host'], array(Util::getMappedServerName(), $this->config->query('Domain.aaid'))))) {
            $this->setSession('cp_ref_'.$cpId, $_SERVER['HTTP_REFERER']);
        }
    }

    public function updateThirdPartyUserRelation($user_id) {
        $monitoring_third_parties = aafwApplicationConfig::getInstance()->query('ThirdPartyKeys');
        foreach ($monitoring_third_parties as $third_party) {
            if ($this->getThirdPartySession($third_party)) {
                /** @var  $third_party_user_relation_service ThirdPartyUserRelationService*/
                $third_party_user_relation_service  = $this->createService('ThirdPartyUserRelationService');
                $third_party_user_relation_service->updateThirdPartyUserRelation($user_id, $this->getThirdPartySession($third_party));
                $this->setThirdPartySession($third_party, null);
            }
        }
    }

    public function preUpdateThirdPartyUserRelation() {
        $monitoring_third_parties = aafwApplicationConfig::getInstance()->query('ThirdPartyKeys');
        foreach ($monitoring_third_parties as $third_party) {
            if ($this->GET[$third_party]) {
                $this->setThirdPartySession($third_party, $this->GET[$third_party]);
            }
        }
    }
    public function setThirdPartySession($key, $value = null) {
        $third_party = $value ? array('key' => $key, 'value' => $value) : null;
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('third_party_' . $key, $third_party);
    }
    public function getThirdPartySession($key) {
        return $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('third_party_' . $key);
    }


    public function canLoginByLinkedIn(){

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $original_sns_accounts = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);

        if(Util::isNullOrEmpty($original_sns_accounts)) {
            return false;
        }

        $original_sns_account_array = explode(',',$original_sns_accounts->content);
        if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
            return true;
        }

        return false;
    }

    public function getMailFromAddress() {

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $can_set_mail_from_address = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SET_MAIL_FROM_ADDRESS);

        if(Util::isNullOrEmpty($can_set_mail_from_address) || $can_set_mail_from_address->content == '') {
            return null;
        }

        return $can_set_mail_from_address->content;
    }

    /**
     * @param $option
     * @param bool $need_manager_access
     * @return bool
     */
    public function hasOption($option, $need_manager_access = true) {
        if ($need_manager_access && !$this->isLoginManager()) {
            return false;
        }

        if (!$this->getBrand()->hasOption($option, BrandInfoContainer::getInstance()->getBrandOptions())) {
            return false;
        }

        return true;
    }

    /**
     * @param $user_info
     * @param $app_id
     * @param null $access_token
     * @return bool
     */
    public function hasFBPublishActions($user_info, $app_id, $access_token = null) {
        if (!Util::isNullOrEmpty($access_token)) {
            $brandco_auth_service = $this->getService('BrandcoAuthService');
            $sns_access_token_result = $brandco_auth_service->getSNSAccessToken($access_token, SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_FACEBOOK]);

            if ($sns_access_token_result->result->status === Thrift_APIStatus::SUCCESS) {
                $sns_account_info = array(
                    'social_media_account_id' => SocialAccount::SOCIAL_MEDIA_FACEBOOK,
                    'social_media_access_token' => $sns_access_token_result->socialAccessToken->snsAccessToken,
                    'social_media_access_refresh_token'=>$sns_access_token_result->socialAccessToken->snsRefreshToken
                );
            }
        } else {
            $user_sns_account_manager = new UserSnsAccountManager($user_info, null, $app_id);
            // $social_media_account_idは中で特に処理に使われてない
            $sns_account_info = $user_sns_account_manager->getSnsAccountInfo(SocialAccount::SOCIAL_MEDIA_FACEBOOK, SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_FACEBOOK]);
        }

        if (!$sns_account_info['social_media_access_token']){
            return false;
        }

        try {
            $facebook_api_client = new FacebookApiClient(FacebookApiClient::BRANDCO_MODE_USER);
            $facebook_api_client->setToken($sns_account_info['social_media_access_token']);
            $permission_array = $facebook_api_client->getPermission();
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('CommentPlugin:hasFBPublishActions Exception: ' . $e->getMessage());
            return false;
        }

        foreach ($permission_array as $permission){
            if($permission->permission === 'publish_actions' && $permission->status === 'granted'){
                return true;
            }
        }
        return false;
    }
}
