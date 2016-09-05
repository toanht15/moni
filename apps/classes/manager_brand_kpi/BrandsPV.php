<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');
AAFW::import('jp.aainc.classes.services.BrandService');
AAFW::import('jp.aainc.classes.manager_kpi.base.GoogleAnalyticsApiBase');

class BrandsPV extends GoogleAnalyticsApiBase implements IManagerKPI {

    function doExecute($date) {
        try {
            list($date, $brandId) = func_get_args();
            /** @var BrandService $brand_service */
            $brand_service = new BrandService();
            $brand = $brand_service->getBrandById($brandId);

            //取得する期間
            $from = date("Y-m-d", strtotime($date));
            $to = date("Y-m-d", strtotime($date));

            //取得するデータの組み合わせ
            $metrics = "ga:pageviews";  //メトリクスの設定

            //オプション設定
            $option = array(
                "sort" => "-ga:pageviews",
                // "start-index"   => 50
            );

            // gaのフィルターパスの設定
            if ($filter = $this->getFilterPath($brand->directory_name)) {
                $option['filters'] = $filter;
            }

            //データの取得
            $obj = $this->get($this->view_id, $from, $to, $metrics, $option);
            list($result) = $obj['rows'];

            return $result[0] ? $result[0] : 0;
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('brand_pv_kpi_batch get error: ' . $e);

            return 0;
        }
    }
}