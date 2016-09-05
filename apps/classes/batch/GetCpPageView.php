<?php
AAFW::import('jp.aainc.classes.batch.GetCpPageViewBase');

/**
 * Googleアナリティクスから指定のキャンペーンのページビュー数を取得する
 * Class GetCpPageView
 */
class GetCpPageView extends GetCpPageViewBase {

    /**
     * TODO: ページビュー数を取得したいキャンペーンをこちらで指定してください！
     *
     * 2016年からの開始キャンペーンを取得する
     * @return aafwEntityContainer|array
     */
    public function getTargetCps(){
        /** @var Cps $cp_store */
        $cp_store = aafwEntityStoreFactory::create("Cps");

        $filters = array(
            "conditions" => array(
                "type"              => Cp::TYPE_CAMPAIGN,
                "status"            => array(Cp::STATUS_FIX,Cp::STATUS_CLOSE),
                "start_date:>="     => self::CP_START_DATE_BEGIN,
            ),
            "order"      => array(
                "name"              => "created_at",
                "direction"         => "DESC"
            )
        );

        $cps =  $cp_store->find($filters);

        $target_cps = array();
        /** @var BrandContractService $brand_contract_service */
        $brand_contract_service = $this->service_factory->create("BrandContractService");

        foreach ($cps as $cp) {
            $brand_contract = $brand_contract_service->getBrandContractByBrandId($cp->brand_id);

            if($brand_contract->for_production_flg == BrandContract::FOR_PRODUCTION_FLG_ON) {
                $target_cps[] = $cp;
            }
        }

        return $target_cps;
    }
}