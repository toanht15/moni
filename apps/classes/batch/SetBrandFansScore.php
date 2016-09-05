<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class SetBrandFansScore extends BrandcoBatchBase {

    /** @var BrandPageSettingService $pageSettingsService */
    private $pageSettingsService;

    private $one_week_ago;

    private $one_month_ago;

    private $six_month_ago;

    private $one_year_ago;

    private $page_settings_cache = array();

    function executeProcess() {
        $this->one_week_ago = date("Y-m-d H:i:s",strtotime("-1 week"));
        $this->one_month_ago = date("Y-m-d H:i:s",strtotime("-1 month"));
        $this->six_month_ago = date("Y-m-d H:i:s",strtotime("-6 month"));
        $this->one_year_ago = date("Y-m-d H:i:s",strtotime("-1 year"));

        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');

        /** @var BrandsUsersRelationService $brand_users_service */
        $brand_users_service = $this->service_factory->create('BrandsUsersRelationService');

        $this->pageSettingsService = $this->service_factory->create('BrandPageSettingService');

        if($this->argv['brand_id']) {
            $brands[] = $brand_service->getBrandById($this->argv['brand_id']);
        } else{
            $brands = $brand_service->getAllBrands();
        }

        $execute_count = 0;
        $update_queries = "";
        $INTERVAL = 1000;
        $db = aafwDataBuilder::newBuilder();
        foreach ($brands as $brand) {
            if(!DEBUG && !$this->checkTarget($brand->id)) {
                continue;
            }
            $this->setDataInfo('$brand_id = ' . $brand->id);
            $brand_users = $brand_users_service->getBrandsUsersRelationsByBrandId($brand->id);

            foreach($brand_users as $brand_user) {
                $score = $this->calcScore($brand_user);
                $brands_users_relation_id = $brand_user->id;
                $update_queries .= "({$brands_users_relation_id}, {$score}, NOW())";
                $execute_count++;

                if ($execute_count % $INTERVAL === 0) {
                    $update_queries = "INSERT INTO brands_users_relations(id, score, updated_at) VALUES " . $update_queries;
                    $update_queries .= " ON DUPLICATE KEY UPDATE score = VALUES(score), updated_at = NOW()";
                    $db->executeUpdate($update_queries);
                    $update_queries = "";
                } else {
                    $update_queries .= ",";
                }
            }
        }
        if (strlen($update_queries) > 0) {
            $update_queries = substr($update_queries, 0, strlen($update_queries) - 1);
            $update_queries = "INSERT INTO brands_users_relations(id, score, updated_at) VALUES " . $update_queries;
            $update_queries = $update_queries . " ON DUPLICATE KEY UPDATE score = VALUES(score), updated_at = NOW()";
            $db->executeUpdate($update_queries);
        }
        $this->setExecuteCount($execute_count);
    }

    function checkTarget($brandId) {
        // 週イチで再計算をするように
        $surplus = $brandId % 7;
        return ( $surplus == date('w') );
    }

    function calcScore($brand_user) {
        $score = 0;
        $score += $this->privacyCoverage($brand_user) * 100;
        $score += $this->snsCoverage($brand_user) * 100;
        $score += $this->loginCount($brand_user) * 10;
        $score += $this->lastLoginScore($brand_user);

        if($brand_user->getUser()->aa_flg) {
            $score *= 0.1;
        }

        return $score;
    }

    function privacyCoverage($brand_user) {
        $numerator = 0;
        $denominator = 0;

        if (!isset($this->page_settings_cache[$brand_user->brand_id])) {
            $this->page_settings_cache[$brand_user->brand_id] = $this->pageSettingsService->getPageSettingsByBrandId($brand_user->brand_id);
        }
        $pageSettings = $this->page_settings_cache[$brand_user->brand_id];
        $user_attribute_info = null;
        if($pageSettings->privacy_required_sex) {
            $denominator++;
            $user_attribute_info = $brand_user->getUserAttributeInfo();
            if($user_attribute_info[0]){
                $numerator++;
            }
        }
        if($pageSettings->privacy_required_birthday) {
            $denominator++;
            if ($user_attribute_info === null) {
                $user_attribute_info = $brand_user->getUserAttributeInfo();
            }
            if($user_attribute_info[1]){
                $numerator++;
            }
        }
        if($pageSettings->privacy_required_address) {
            $denominator++;
            if($brand_user->getPrefecture()){
                $numerator++;
            }
        }

        return $numerator / $denominator;

    }

    function snsCoverage($brand_user) {

        $total = 0;
        $social_accounts = $brand_user->getSocialAccounts();
        if($social_accounts) {
            $total = $social_accounts->total();
        }

        return $total ? $total / 5 : 0;
    }

    function loginCount($brand_user) {

        return $brand_user->login_count;
    }

    function lastLoginScore($brand_user) {

        $last_login_date_obj = date($brand_user->last_login_date);
        if($last_login_date_obj > $this->one_week_ago) {
            return 50;
        }
        if($last_login_date_obj > $this->one_month_ago) {
            return 40;
        }
        if($last_login_date_obj > $this->six_month_ago) {
            return 30;
        }
        if($last_login_date_obj > $this->one_year_ago) {
            return 20;
        }
        return 0;
    }
}