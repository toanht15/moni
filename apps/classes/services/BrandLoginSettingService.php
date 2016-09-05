<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandLoginSettingService extends aafwServiceBase {
    protected $brand_login_settings;

    private $brand_id;

    public function __construct($brand_id) {
        $this->brand_id = $brand_id;
        $this->brand_login_settings = $this->getModel('BrandLoginSettings');
    }

    /**
     * @return mixed
     */
    public function getBrandLoginSnses() {
        $filter = array(
            'brand_id' => $this->brand_id
        );

        return $this->brand_login_settings->find($filter);
    }

    /**
     * @return array
     */
    public function getBrandLoginSnsList() {
        $ret = array();

        $brand_login_snses = $this->getBrandLoginSnses();
        foreach ($brand_login_snses as $sns) {
            $ret[] = $sns->social_media_id;
        }

        return $ret;
    }

    /**
     * @param $sns_list
     */
    public function updateBrandLoginSettings($sns_list) {
        $this->deleteBrandLoginSettings();

        foreach ($sns_list as $sns_id) {
            $brand_login_setting = $this->brand_login_settings->createEmptyObject();

            $brand_login_setting->brand_id = $this->brand_id;
            $brand_login_setting->social_media_id = $sns_id;

            $this->brand_login_settings->save($brand_login_setting);
        }
    }

    /**
     * Physically remove brand_login_settings
     */
    public function deleteBrandLoginSettings() {
        $brand_login_snses = $this->getBrandLoginSnses();

        foreach ($brand_login_snses as $sns) {
            $this->brand_login_settings->delete($sns);
        }
    }
}
