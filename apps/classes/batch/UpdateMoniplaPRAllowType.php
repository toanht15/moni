<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

/**
 * Class UpdateMoniplaPRAllowType
 */
class UpdateMoniplaPRAllowType extends BrandcoBatchBase {

    protected $logger;

    public function executeProcess() {

        $brand_id = $this->argv['brand_id'];
        if (!$brand_id) {
            echo '「brand_id=xxx」の形式で引数を入力してください。' . PHP_EOL;
            return;
        }

        if (!isset($this->argv['type'])) {
            $type = 0;
        } elseif(0 === intval($this->argv['type'])) {
            $type = -1;
        } else {
            $type = intval($this->argv['type']);
        }

        if ($type !== Brand::MONIPLA_PR_ALLOW_TYPE_NOT_SET &&
                $type !== Brand::MONIPLA_PR_ALLOW_TYPE_DISALLOWED &&
                $type !== Brand::MONIPLA_PR_ALLOW_TYPE_ALWAYS_ALLOWED
        ) {
            echo '「type=0 or 1 or 2」の形式で引数を入力してください。type未指定の場合は0になります。' . PHP_EOL;
            return;
        }

        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');

        $brand = $brand_service->getBrandById($brand_id);

        if ($brand === null) {
            echo 'Brand[id=' . $brand_id . ']が見つかりませんでした。' . PHP_EOL;
            return;
        }

        $brand->monipla_pr_allow_type = $type;
        $brand_service->updateBrand($brand);

        echo 'Brand[id=' . $brand_id . ']のmonipla_pr_allow_typeを' . $type . 'に更新しました。' . PHP_EOL;
        return;
    }
}
