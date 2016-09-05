<?php
/**
 * Created by PhpStorm.
 * User: katoriyusuke
 * Date: 15/08/14
 * Time: 21:19
 */

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandOptionsService extends aafwServiceBase {
    protected $brandOptions;

    public function __construct() {
        $this->brandOptions = $this->getModel("BrandOptions");
    }

    public function updateBrandOptions($brandId, $brandOptions) {
        $allOptions = $this->getAllBrandOptions();

        foreach ($allOptions as $optionId) {
            $registerdOption = $this->getBrandOptionByBrandIdAndOptionId($brandId, $optionId);

            // オプションが選択されていて、かつ登録がない場合は追加
            if (in_array($optionId, $brandOptions) && !$registerdOption) {
                $brandOption = $this->getEmptyBrandOption();
                $brandOption->brand_id  = $brandId;
                $brandOption->option_id = $optionId;
                $this->brandOptions->save($brandOption);

            // すでに登録があるが選択されていない場合は削除
            } elseif ($registerdOption && !in_array($optionId, $brandOptions)) {
                $registerdOption->del_flg = 1;
                $this->brandOptions->save($registerdOption);
            }
        }
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function getEmptyBrandOption() {
        return $this->brandOptions->createEmptyObject();
    }

    public function getBrandOptionByBrandIdAndOptionId($brandId, $optionId) {
        $filter = array(
            'brand_id'  => $brandId,
            'option_id' => $optionId,
        );

        return $this->brandOptions->findOne($filter);
    }

    public function getAllBrandOptions() {
        return array(
            BrandOptions::OPTION_CP,
            BrandOptions::OPTION_CRM,
            BrandOptions::OPTION_CMS,
            BrandOptions::OPTION_FAN_LIST,
            BrandOptions::OPTION_DASHBOARD,
            BrandOptions::OPTION_TOP,
            BrandOptions::OPTION_HEADER,
            BrandOptions::OPTION_MYPAGE,
            BrandOptions::OPTION_SEGMENT
        );
    }
}