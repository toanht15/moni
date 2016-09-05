<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class ConversionService extends aafwServiceBase
{

    /** @var  Conversions $brands */
    protected $conversions;
    /** @var  BrandsUsersConversions $brands_users_conversion */
    public $brands_users_conversion;

    //カートタイプ
    const FUTURE_SHOP_CART = 'フューチャーショップ';
    const MAKE_SHOP_CART = 'MakeShop';
    const SAVAWAY_CART = 'SAVAWAY';
    const CONVERSION_LIMIT_TIME = '5 SECONDS';

    const CONVERSION_STATUS_DUPLICATE = 3;
    const CONVERSION_STATUS_OTHER     = 2;
    const CONVERSION_STATUS_BC_SAVED  = 1;
    const CONVERSION_STATUS_NONE      = 0;

    public $MAX_LIMIT = 10;

    public $CART_TYPES = array(
        '1' => self::FUTURE_SHOP_CART,
        '2' => self::MAKE_SHOP_CART,
        '3' => self::SAVAWAY_CART
    );

    public function __construct()
    {
        $this->conversions = $this->getModel("Conversions");
        $this->brands_users_conversion = $this->getModel('BrandsUsersConversions');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $brand_id
     * @return aafwEntityContainer|array
     */
    public function getConversionsByBrandId($brand_id)
    {
        return $this->conversions->find(array('brand_id' => $brand_id));
    }

    /**
     * @param $brand_id
     * @return bool
     */
    public function isArrivalLimitCount($brand_id)
    {
        if($this->conversions->count(array('brand_id' => $brand_id)) >= $this->MAX_LIMIT) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * @param $brand_id
     * @param $post
     * @return mixed
     */
    public function createConversion($brand_id, $post)
    {
        $conversion = $this->conversions->createEmptyObject();
        $conversion->brand_id = $brand_id;
        $conversion->name = $post['name'];
        $conversion->description = $post['description'] ? $post['description'] : '';
        return $this->conversions->save($conversion);
    }

    /**
     * @param $id
     * @return entity
     */
    public function getConversionById($id)
    {
        return $this->conversions->findOne(array('id' => $id));
    }

    /**
     * @param $id
     * @param $post
     * @return mixed
     */
    public function updateConversion($id, $post)
    {
        $conversion = $this->getConversionById($id);
        $conversion->name = $post['name'];
        $conversion->description = $post['description'] ? $post['description'] : '';
        return $this->conversions->save($conversion);
    }

    /**
     * @param $name
     * @param $brand_id
     * @return entity
     */
    public function getConversionByNameAndBrandId($name, $brand_id)
    {
        $filter = array(
            'conditions' => array(
                'name' => $name,
                'brand_id' => $brand_id
            )
        );
        return $this->conversions->findOne($filter);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createNewConversionLog($data)
    {
        /** @var aafwEntityStoreBase $conversion_log_store */
        $conversion_log_store = $this->getModel("ConversionLogs");

        $conversion = $conversion_log_store->createEmptyObject();

        $filter = array(
            'conditions' => array(
                'brand_id' => $data->brand_id,
                'conversion_id' => $data->conversion_id,
                'aa_user_id' => $data->aa_user_id,
                'saved_bc' => self::CONVERSION_STATUS_NONE,
                'date_created:>' => date('Y-m-d H:i:s', strtotime($data->date_created . ' - ' . self::CONVERSION_LIMIT_TIME))
            )
        );
        $conversion_log = $conversion_log_store->findOne($filter);

        foreach ($data as $column => $value) {
            //新しいコンバージョンの情報の取得
            $conversion->$column = $conversion_log_store->escapeForSQL($value);
            //足りない情報を更新する
            if ($conversion_log && !$conversion_log->$column && $conversion->$column) {
                $conversion_log->$column = $conversion->$column;
            }
        }

        if ($conversion_log) {
            $conversion_log_store->save($conversion_log);
            $conversion->saved_bc = self::CONVERSION_STATUS_DUPLICATE;
        }

        return $conversion_log_store->save($conversion);
    }

    /**
     * @return mixed
     */
    public function getConversionLogsOverMonth()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1M'));
        $filter = array(
            'conditions' => array(
                'date_created:<' => $date->format('Y-m-d H:i:s')
            )
        );
        return $this->getModel("ConversionLogs")->find($filter);
    }

    /**
     * @return mixed
     */
    public function getBrandUserConversionOverMonth()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1M'));
        $filter = array(
            'conditions' => array(
                'created_at:<' => $date->format('Y-m-d H:i:s')
            )
        );
        return $this->brands_users_conversion->find($filter);
    }

    /**
     * @param $container
     */
    public function deleteConversionLogs($container)
    {
        $id = null;
        try {
            foreach ($container as $record) {
                $id = $record->id;
                $this->getModel("ConversionLogs")->deletePhysical($record);
            }
        } catch (Exception $e) {
            $this->logger->error('deleteConversionLogs record_id=' . $id);
            $this->logger->error($e);
        }
    }

    /**
     * @param $container
     */
    public function deleteBrandUserConversion($container)
    {
        $id = null;
        try {
            foreach ($container as $record) {
                $id = $record->id;
                $this->brands_users_conversion->deletePhysical($record);
            }
        } catch (Exception $e) {
            $this->logger->error('deleteConversionLogs record_id=' . $id);
            $this->logger->error($e);
        }
    }

    /**
     * @return aafwEntityContainer|array
     */
    public function getConversionLogToCopyToBC()
    {

        $filter = array(
            'conditions' => array(
                'saved_bc' => self::CONVERSION_STATUS_NONE
            ),
            'pager' => array(
                'page' => 1,
                'count' => 100000,
            )
        );

        return $this->getModel("ConversionLogs")->find($filter);
    }

    /**
     * @return mixed
     */
    public function createEmptyBrandcoUserConversion()
    {
        return $this->brands_users_conversion->createEmptyObject();
    }

    /**
     * @param $record
     * @return mixed
     */
    public function updateBrandcoUserConversion($record)
    {
        return $this->brands_users_conversion->save($record);
    }

    /**
     * @return ConversionLogs
     */
    public function getTrackerConversionLogStore() {
        return $this->getModel("ConversionLogs");
    }

    /**
     * @param $conversion_log
     */
    public function savedConversionLog ($conversion_log) {
        $conversion_log->saved_bc = self::CONVERSION_STATUS_BC_SAVED;
        $this->getTrackerConversionLogStore()->save($conversion_log);
    }

    /**
     * @param $conversion_log
     */
    public function saveConversionNotBcUser($conversion_log) {
        $conversion_log->saved_bc = self::CONVERSION_STATUS_OTHER;
        $this->getTrackerConversionLogStore()->save($conversion_log);
    }

    /**
     * @param $user_id
     * @param $conversion_id
     * @param $brand_id
     * @return 件数
     */
    public function countUserConversionByUserIdAndConversionId($user_id, $conversion_id, $brand_id) {
        $filter = array(
            'conditions' => array(
                'user_id' => $user_id,
                'brand_id' => $brand_id,
                'conversion_id' => $conversion_id
            )
        );
        return $this->brands_users_conversion->count($filter);
    }

    public function findConversions($id, $brand_id) {

        $filter = array(
            'conditions' => array(
                'id'        => $id,
                'brand_id'  => $brand_id
            )
        );

        return $this->getModel("Conversions")->find($filter);
    }
}