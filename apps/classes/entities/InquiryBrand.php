<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class InquiryBrand extends aafwEntityBase {
    const MANAGER_BRAND_ID = -1;
    const MANAGER_BRAND_NAME = 'モニプラ運営事務局';
    const MANAGER_BRAND_IMAGE = 'https://s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/brand/013d407166ec4fa56eb1e1f8cbe183b9/profile/aa4cb73e30003359be32acceef975792.png';

    public static function isMoniplaBrand($brand_id) {
        return $brand_id == self::getMoniplaBrandId();
    }

    public static function getMoniplaBrandId() {
        return (config('Monipla.BrandId')) ?: null;
    }

    public static function getMoniplaBrand() {
        $monipla_brand_id = self::getMoniplaBrandId();

        if ($monipla_brand_id) {
            $aafw_service_factory = new aafwServiceFactory();
            /** @var BrandService $brand_service */
            $brand_service = $aafw_service_factory->create('BrandService');

            return $brand_service->getBrandById(self::getMoniplaBrandId());
        }

        return null;
    }
}
