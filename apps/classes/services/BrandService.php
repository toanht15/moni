<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.BrandOuterTokenService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandService extends aafwServiceBase {

    /** @var  Brands $brands */
    protected $brands;

    /** @var  BrandOptions $brandOptions */
    protected $brandOptions;

    const OPEN = 1;    //公開中
    const NONOPEN = 2; //非公開
    const NODATA = 3;  //データなし

    const TEST = 1;    //テスト用
    const COMPANY = 0; //企業用

	public function __construct() {
        aafwEntityStoreBase::loadCatalogs(array(
            'brands',
            'brand_options',
            'brand_user_attribute_definitions',
            'brand_user_attributes'));
        $this->brands = $this->getModel("Brands");
        $this->brandOptions = $this->getModel("BrandOptions");
        $this->brandUserAttributeDefinitions = $this->getModel("BrandUserAttributeDefinitions");
        $this->brandUserAttributes = $this->getModel("BrandUserAttributes");
		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

    public function getAllBrands() {
        return $this->brands->find(array());
    }

    public function getAllPublicBrand($test_page = 0){
        $filter = array(
            'test_page' => $test_page,
        );
        return $this->brands->find($filter);
    }

    public function getAllPublicBrandIds($test_page = 0){
        $filter = array(
            'test_page' => $test_page,
        );

        $brands = $this->brands->find($filter);

        $brand_ids = array();
        foreach ($brands as $brand) {
            $brand_ids[] = $brand->id;
        }

        return $brand_ids;
    }

    public function getBrands($page = 1, $limit = 20, $order = null) {
        $filter = array(
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
        );

        return $this->brands->find($filter);
    }

	public function getBrandByDirectoryName($directoryName) {
		$filter = array(
			'directory_name' => $directoryName,
		);
		return $this->brands->findOne($filter);
	}

	public function getBrandByEnterpriseId($enterpriseId) {
		$filter = array(
			'enterprise_id' => $enterpriseId,
		);
		return $this->brands->findOne($filter);
	}

    public function getBrandByEnterpriseIdAndToken($enterpriseId, $monipla_enterprise_token) {
        $filter = array(
            'enterprise_id' => $enterpriseId,
            'monipla_enterprise_token' => $monipla_enterprise_token
        );
        return $this->brands->findOne($filter);
    }

	public function getBrandById($brandId) {
		$filter = array(
				'id' => $brandId,
		);
		return $this->brands->findOne($filter);
	}

    public function getBrandByOuterToken($token) {
        $outer_token_service = new BrandOuterTokenService();
        $outer_token = $outer_token_service->getBrandOuterTokenByToken($token);

        $filter = array(
            'id' => $outer_token->brand_id
        );

        return $this->brands->findOne($filter);
    }

	public function updateBrand($brand) {
		$this->brands->begin();

		try {
			$this->brands->save($brand);
            BrandInfoContainer::getInstance()->clear($brand->id);
            $this->brands->commit();
		} catch (Exception $e) {
			$this->brands->rollback();
			$this->logger->error("BrandService#updateBrand Error");
			$this->logger->error($e);
		}
	}

    public function countBrands($condition = array()){

        return $this->brands->count($condition);
    }

    public function updateBrandList($brandList) {
        $this->brands->save($brandList);
        BrandInfoContainer::getInstance()->clear($brandList->id);
    }

    /**
     * @param $brandInfo
     */
    public function addBrand($brandInfo) {

        $addBrand = $this->brands->createEmptyObject();
        $addBrand->name = $brandInfo['brand_name'];
        $addBrand->mail_name = $brandInfo['mail_name'] ?: $brandInfo['brand_name'];
        $addBrand->enterprise_name = $brandInfo['enterprise_name'];
        $addBrand->app_id = $brandInfo['app_id'];
        $addBrand->directory_name = $brandInfo['directory_name'];
        $addBrand->test_page = $brandInfo['test_page'] ? '1' : '0';
        if($brandInfo['enterprise_id']) {
            $addBrand->enterprise_id = $brandInfo['enterprise_id'];
        } else{
            $addBrand->enterprise_id = 0;
        }
        if ($brandInfo['monipla_enterprise_token']) {
            $addBrand->monipla_enterprise_token = $brandInfo['monipla_enterprise_token'];
        }

        $this->brands->save($addBrand);

        $this->refreshBrandOptions($addBrand->id, $brandInfo);

        return $addBrand;
    }

    public function refreshBrandOptions($brand_id, $brandInfo) {
        $options = $this->brandOptions->find(array('brand_id' => $brand_id));
        foreach($options as $option) {
            $this->brandOptions->delete($option);
        }
        foreach($brandInfo as $postKey => $postValue) {
            if(strpos($postKey, 'option_') === 0 && $postValue == BrandOptions::ON) {
                list($prefix, $option_id) = explode('_', $postKey);
                if(isset(BrandOptions::$OPTION_LIST[$option_id])) {
                    $brandOption = $this->brandOptions->createEmptyObject();
                    $brandOption->brand_id = $brand_id;
                    $brandOption->option_id = $option_id;
                    $this->brandOptions->save($brandOption);
                }
            }
        }
        BrandInfoContainer::getInstance()->clear($brand_id);
    }

    // BrandUserAttributeDefinitions用

    /**
     * @param $brand_id
     * @return bool
     */
    public function isValidCustomAttributeDefinitions($brand_id) {
        $count = $this->countCustomAttributeDefinitions($brand_id);

        return $count != 0;
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function countCustomAttributeDefinitions($brand_id) {
        return $this->brandUserAttributeDefinitions->count(array('brand_id' => $brand_id));
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getCustomAttributeDefinitions($brand_id) {
        return $this->brandUserAttributeDefinitions->find(array('brand_id' => $brand_id));
    }

    /**
     * @param $definition_id
     * @return mixed
     */
    public function getBrandUserAttributeDefinitionById($definition_id) {
        return $this->brandUserAttributeDefinitions->findOne($definition_id);
    }

    public function getAssignableCustomAttributeValue($user_id, $def) {
        $attr = $this->brandUserAttributes->findOne(array('user_id' => $user_id, 'definition_id' => $def->id));
        if ($attr === null) {
            return '';
        }

        $value = $attr->value;
        if ($def->attribute_type === BrandUserAttributeDefinitions::ATTRIBUTE_TYPE_SET) {
            $value = $def->convertValueByValueSet($value);
            $value = $value ?: '';
        }

        return $value;
    }

    public function getAssignableCustomAttributeValueByUserIds($user_ids, $def) {
        if(!$user_ids || !$def) {
            return;
        }
        return $this->brandUserAttributes->find(array('user_id' => $user_ids, 'definition_id' => $def->id));
    }

    /**
     * @param String $token
     *
     * @return int
     */
    public function getBrandCountByDirectoryName($directory_name) {
        if (strlen($directory_name) < 0) {
            return 0;
        }
        $filter = ['directory_name' => $directory_name];
        $col = 'id';

        return $this->brands->count($filter, $col);
    }

    public function getBrandByBrandName($brand_name){

        $filter = array(
            'name' => $brand_name,
        );
        return $this->brands->findOne($filter);
    }
}
