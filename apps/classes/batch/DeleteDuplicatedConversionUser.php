<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import ( 'jp.aainc.classes.services.ConversionService');

//ワンタイムのバッチです。
class DeleteDuplicatedConversionUser extends BrandcoBatchBase{

    public function executeProcess() {
        /** @var aafwEntityStoreBase $brand_conversion_user_store */
        $brand_conversion_user_store = aafwEntityStoreFactory::create('BrandsUsersConversions');
        try {
            $brand_conversion_user_store->begin();

            $users_conversions = $brand_conversion_user_store->find(array('order' => array('name' => 'id')));
            $last_conversion = null;
            foreach ($users_conversions as $user_conversion) {

                if (!$last_conversion || $last_conversion->user_id != $user_conversion->user_id
                    || $last_conversion->brand_id != $user_conversion->brand_id
                    || $last_conversion->conversion_id != $user_conversion->conversion_id
                ) {
                    $last_conversion = $user_conversion;
                    continue;
                }
                $last_conversion_date = DateTime::createFromFormat('Y-m-d H:i:s', $last_conversion->date_conversion);
                $duplicate_limit_date = DateTime::createFromFormat('Y-m-d H:i:s', $user_conversion->date_conversion)->modify('-'.ConversionService::CONVERSION_LIMIT_TIME);

                if ($last_conversion_date < $duplicate_limit_date) {
                    $last_conversion = $user_conversion;
                    continue;
                }

                //足りない情報を更新する
                foreach ($user_conversion->toArray() as $key => $value) {
                    if (!$user_conversion->$key && $last_conversion->$key) {
                        $user_conversion->$key = $last_conversion->$key;
                    }
                }
                $brand_conversion_user_store->save($user_conversion);
                $brand_conversion_user_store->deleteLogical($last_conversion);

                $last_conversion = $user_conversion;
            }

            $brand_conversion_user_store->commit();

        } catch (Exception $e) {
            $brand_conversion_user_store->rollback();
            $this->logger->error($e);
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }
}