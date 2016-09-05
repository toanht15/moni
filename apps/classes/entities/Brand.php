<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

/**
 * @property mixed directory_name
 */
class Brand extends aafwEntityBase {

    const BACKGROUND_IMAGE_REPEAT_TYPE_NO     = 1;
    const BACKGROUND_IMAGE_REPEAT_TYPE_X      = 2;
    const BACKGROUND_IMAGE_REPEAT_TYPE_Y      = 3;
    const BACKGROUND_IMAGE_REPEAT_TYPE_REPEAT = 4;
    const menuSideTypeText = 0;
    const menuSideTypeImage = 1;
    /** brand search account */
    const BRAND_DEFAULT = -1;
    const BRAND_ENTERPRISE_PAGE = 0;
    const BRAND_TEST_PAGE = 1;

    /** モニプラPRの許可タイプ */
    const MONIPLA_PR_ALLOW_TYPE_NOT_SET = 0;
    const MONIPLA_PR_ALLOW_TYPE_DISALLOWED = 1;
    const MONIPLA_PR_ALLOW_TYPE_ALWAYS_ALLOWED = 2;

    // ハードコーディング対応用
    const KENKO_KENTEI_ID = 479;

    //ドメインマッピングテストブランド
    const DM_TEST_AA_DEV = 181;

    //オリンパスハードコーディング用
    const OLYMPUS_ID = 398;

    //NEC用ハードコーディング対応
    const CLUB_LENOVO = 452;
    const CLUB_LAVIE = 453;
    const LAVIE_SPECIALFAN = 527;
    const LENOVO_SPECIALFAN = 528;

    //ハードコーディング対応用
    const KANKO = 130;
    const JR_ODEKAKE_NET = 547;
    const ANGERS = 457;
    const CHOJYU = 339;

    protected $_Relations = array(
        'BrandSocialAccounts' => array(
            'id' => 'brand_id'
        ),
        'BrandsUsersRelations' => array(
            'id' => 'brand_id'
        ),
        'BrandPageSettings' => array(
            'id' => 'brand_id'
        ),
        'BrandOptions' => array(
            'id' => 'brand_id'
        ),
        'BrandContracts' => array(
            'id' => 'brand_id'
        )
    );

    public static $select_list_account = array(
        self::BRAND_ENTERPRISE_PAGE => '企業用',
        self::BRAND_TEST_PAGE => 'テスト用',
        self::BRAND_DEFAULT => '指定なし'
    );

    public static $monipla_pr_allow_type_list = array(
        self::MONIPLA_PR_ALLOW_TYPE_NOT_SET => '指定なし',
        self::MONIPLA_PR_ALLOW_TYPE_ALWAYS_ALLOWED =>'許可する',
        self::MONIPLA_PR_ALLOW_TYPE_DISALLOWED => '許可しない'
    );

	public function getProfileImage() {

		$image = $this->profile_img_url;
		if( !$image ) {
			$image = aafwApplicationConfig::getInstance()->query('Static.Url') . '/img/icon/iconNoImage1.png';
		}
		return $image;
	}

	public function getColorMain() {

		$color = $this->color_main;
		if( !$color ) {
			$color = '#CCCCCC';
		}
		return $color;
	}

	public function getColorBackground() {

		$color = $this->color_background;
		if( !$color ) {
			$color = '#f3f3f3';
		}
		return $color;
    }

    public function getColorText() {

        $color = $this->color_text;
        if( !$color ) {
            $color = '#333333';
        }
        return $color;
    }

    public function getBackgroundImageRepeatType() {

        if( $this->background_img_x && $this->background_img_y ) {
            $type = self::BACKGROUND_IMAGE_REPEAT_TYPE_REPEAT;
        } elseif( $this->background_img_x ) {
            $type = self::BACKGROUND_IMAGE_REPEAT_TYPE_X;
        } elseif( $this->background_img_y ) {
            $type = self::BACKGROUND_IMAGE_REPEAT_TYPE_Y;
        } else{
            $type = self::BACKGROUND_IMAGE_REPEAT_TYPE_NO;
        }
        return $type;
    }

    /**
    * Has the object freeArea.
    * @return bool
    */
    public function hasFreeArea() {
        $service_factory = new aafwServiceFactory ();
        $free_area_entry_service = $service_factory->create('FreeAreaEntryService');
        return $free_area_entry_service->getSelectedEntryByBrandId($this->id);
    }

    /**
     * Has the brand_option
     * @param $option_id
     * @return bool
     */
    public function hasOption($option_id, $brand_options = null) {
        if (!$brand_options) {
            $brand_options = $this->getBrandOptions();
        }
        foreach($brand_options as $option) {
            if($option_id == $option->option_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is the plan of the brand.
     * @param $plan_id
     * @return bool
     */
    public function isPlan($plan_id, $brand_contract = null) {
        if ($brand_contract === null) {
            $brand_contract = $this->getBrandContract();
        }
        return $brand_contract->plan == $plan_id;
    }

    /**
     * @param bool $secure
     * @return string
     */
    public function getUrl($secure = false) {
        $protocol = $secure ? config("Protocol.Secure") : Util::getHttpProtocol();
        $base_url = $protocol . "://" . Util::getMappedServerName($this->id) . "/" . Util::resolveDirectoryPath($this->id, $this->directory_name);
        return $base_url;
    }

    /**
     * @return 真偽値
     */
    public function getCloseStatus() {
        $brand_contract = $this->getBrandContract();

        if (!$brand_contract) {
            return null;
        }

        return $brand_contract->getCloseStatus();
    }

    /**
     * @return bool
     */
    public function isLimitedBrandPage($settings = null) {
        $service_factory = new aafwServiceFactory ();
        $brand_global_settings_service = $service_factory->create('BrandGlobalSettingService');

        if ($settings === null) {
            $brand_global_setting = $brand_global_settings_service->getBrandGlobalSetting($this->id, BrandGlobalSettingService::NEW_PAGE_LABEL);
        } else {
            $brand_global_setting = $brand_global_settings_service->getBrandGlobalSettingByName($settings, BrandGlobalSettingService::NEW_PAGE_LABEL);
        }

        return $brand_global_setting && $brand_global_setting->content == 1;
    }

    /**
     * フルテキストを表示するか
     * @return bool
     */
    public function isViewFullText() {
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        if ($brand_global_setting_service->getBrandGlobalSetting($this->id, BrandGlobalSettingService::TOP_PANEL_FULL_TEXT)) {
            return true;
        }
        return false;
    }

    /**
     * クローズしているブランドかどうか
     * @return bool
     */
    public function isClosedBrand($contract = null) {
        If (!$contract) {
            $contract = $this->getBrandContract();
        }
        return $contract->getCloseStatus() == BrandContracts::MODE_CLOSED || $contract->getCloseStatus() == BrandContracts::MODE_SITE_CLOSED || Util::isClosedBrandPreviewMode();
    }

    /**
     * favicon情報を取得
     *
     * @return mix
     */
    public function getFaviconUrl($page_settings = null) {
        if ($page_settings === null) {
            $page_settings = $this->getBrandPageSetting();
        }
        if ($page_settings) {
            return $page_settings->favicon_url;
        }

        return '';
    }

    /**
     * PRを常に禁止されているブランドかどうかを返す
     * @return bool
     */
    public function isDisallowedBrand() {
        return intval($this->monipla_pr_allow_type) === Brand::MONIPLA_PR_ALLOW_TYPE_DISALLOWED;
    }

    /**
     * PRを常に許可されているブランドかどうかを返す
     * @return bool
     */
    public function isAlwaysAllowedBrand() {
        return intval($this->monipla_pr_allow_type) === Brand::MONIPLA_PR_ALLOW_TYPE_ALWAYS_ALLOWED;
    }
}
