<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class Manager extends aafwEntityBase {

    const MENU_CAMPAIGNS = 0;
    const MENU_MESSAGE_HISTORY = 1;
    const MENU_KPI = 2;
    const MENU_KPI_GROUPS = 3;
    const MENU_BRAND_LIST = 4;
    const MENU_ADD_BRAND = 5;
    const MENU_INFORMATION = 6;
    const MENU_MANAGER_LIST = 7;
    const MENU_ADD_MANAGER = 8;
    const MENU_CHANGE_PASSWORD = 9;
    const MENU_USER_SEARCH = 10;
    const MENU_SQL_SELECTOR = 11;
    const MENU_FILE_LIST = 12;
    const MENU_INQUIRY = 13;
    const MENU_BRAND_MGR = 14;
    const MENU_AGENT_LIST = 15;

    const MANAGER = 0;
    const SUPER_USER = 1;
    const AGENT   = 2;

    public static $MANAGER_AUTHORITY_LIST = array(
        self::MANAGER    => 'マネージャー',
        self::SUPER_USER => 'スーパーユーザー',
        self::AGENT      => '代理店',
);

    /**
     * 各マネージャーページのTOPページをマッピングするよ
     * @var array
     */
    public static $MANAGER_MENU = array(
        self::MENU_CAMPAIGNS       => array( 'name' => 'キャンペーン一覧', 'url' => '/dashboard/campaigns',),
        self::MENU_MESSAGE_HISTORY => array( 'name' => 'メッセージ送信履歴', 'url' => '/dashboard/message_history',),
        self::MENU_KPI             => array( 'name' => 'KPI', 'url' => '/dashboard/kpi',),
        self::MENU_KPI_GROUPS      => array( 'name' => 'KPIグループ', 'url' => '/dashboard/list_kpi_groups',),
        self::MENU_BRAND_LIST      => array( 'name' => 'ブランド一覧', 'url' => '/brands/index',),
        self::MENU_ADD_BRAND       => array( 'name' => 'ブランド追加', 'url' => '/dashboard/add_brand_form',),
        self::MENU_INFORMATION     => array( 'name' => 'お知らせ編集', 'url' => '/dashboard/brand_notification_list',),
        self::MENU_MANAGER_LIST    => array( 'name' => '管理者一覧', 'url' => '/dashboard/manager_list',),
        self::MENU_ADD_MANAGER     => array( 'name' => '管理者追加', 'url' => '/dashboard/add_manager_form',),
        self::MENU_CHANGE_PASSWORD => array( 'name' => 'パスワード変更', 'url' => '/dashboard/change_password_form',),
        self::MENU_USER_SEARCH     => array( 'name' => 'ユーザー管理', 'url' => '/users/index',),
        self::MENU_SQL_SELECTOR    => array( 'name' => 'データ抽出', 'url' => '/sql_selector/index',),
        self::MENU_FILE_LIST       => array( 'name' => 'ファイルリスト', 'url' => '/dashboard/brandco_action_base_list',),
        self::MENU_INQUIRY         => array( 'name' => 'お問い合わせ', 'url' => '/inquiry/show_inquiry_list',),
        self::MENU_BRAND_MGR       => array( 'name' => '月額ブランド管理', 'url' => '/dashboard/brand_mgr',),
        self::MENU_AGENT_LIST      => array( 'name' => '代理店一覧', 'url' => '/dashboard/agent_list')
    );

    /**
     * こいつに入ってればアクセス可能
     * @var array
     */
    public static $MANAGER_ALLOWED_LIST = array(
        self::MANAGER => array(
            self::MENU_CAMPAIGNS,
            self::MENU_MESSAGE_HISTORY,
            self::MENU_KPI,
            self::MENU_KPI_GROUPS,
            self::MENU_BRAND_LIST,
            self::MENU_ADD_BRAND,
            self::MENU_CHANGE_PASSWORD,
            self::MENU_SQL_SELECTOR,
            self::MENU_FILE_LIST,
            self::MENU_INQUIRY,
            self::MENU_BRAND_MGR
        ),
        self::SUPER_USER => array(
            self::MENU_CAMPAIGNS,
            self::MENU_MESSAGE_HISTORY,
            self::MENU_KPI,
            self::MENU_KPI_GROUPS,
            self::MENU_BRAND_LIST,
            self::MENU_ADD_BRAND,
            self::MENU_INFORMATION,
            self::MENU_MANAGER_LIST,
            self::MENU_ADD_MANAGER,
            self::MENU_AGENT_LIST,
            self::MENU_CHANGE_PASSWORD,
            self::MENU_USER_SEARCH,
            self::MENU_SQL_SELECTOR,
            self::MENU_FILE_LIST,
            self::MENU_INQUIRY,
            self::MENU_BRAND_MGR
        ),
        self::AGENT   => array(
            self::MENU_BRAND_LIST,
            self::MENU_CHANGE_PASSWORD,
        ),
    );

    public function canView() {
        return (((bool)$this->full_control_flg) || Util::isPersonalMachine());
    }

    /**
     * @param $requestPage
     * @return string
     */
    public static function getManagerActionUrl($requestPage) {
        $config = aafwApplicationConfig::getInstance();
        $protocol = $config->query('Protocol.Secure');
        $domain = $config->query('Domain.brandco_manager');
        return $protocol . '://' . $domain . $requestPage['url'];
    }

    public function isAllowedPage($managerPageId){
        return in_array($managerPageId, Manager::$MANAGER_ALLOWED_LIST[$this->authority]);
    }

    /**
     * @return bool
     */
    public function isSuperUser() {
        return $this->authority == self::SUPER_USER;
    }
}
