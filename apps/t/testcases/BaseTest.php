<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.aafw.web.aafwController');

abstract class BaseTest extends PHPUnit_Framework_TestCase {

    private static $truncate_target_tables;

    private static $truncate_inhibit_tables =
        array('crawler_hosts', 'crawler_types', 'manager_brand_kpi_columns',
            'manager_kpi_columns', 'manuals', 'migrations', 'prefectures', 'question_types',
            'regions', 'social_apps', 'user_attribute_masters');

    const Tables_in_db_brandco = 'Tables_in_db_brandco_unittest';

    /**
     * 指定された配列のプロパティを持つ新しいエンティティを複数分、永続化します。
     *
     * @param $store エンティティの作成対象のストア
     * @param $propertyArray エンティティが持つプロパティの配列
     * @return mixed 永続化されたエンティティの配列
     */
    protected function entities($store, $propertyArray) {
        $entities = array();
        foreach ($propertyArray as $key => $value) {
            $entity = $this->entity($store, $value);
            array_push($entities, $entity);
        }

        return $entities;
    }

    /**
     * 指定されたプロパティを持つ新しいエンティティを永続化します。
     *
     * @param $store エンティティの作成対象のストア
     * @param $properties エンティティが持つプロパティ
     * @return mixed 永続化されたエンティティ。
     */
    protected function entity($store, $properties = array()) {
        $store = aafwEntityStoreFactory::create($store);
        $entity = $store->createEmptyObject();
        foreach ($properties as $key => $value) {
            $entity->$key = $value;
        }
        return $store->save($entity);
    }

    /**
     * DBに保存せずに空のエンティティを作成します。
     * @param $store
     */
    protected function emptyEntity($store, $properties = array()) {
        $store = aafwEntityStoreFactory::create($store);
        $entity = $store->createEmptyObject();
        foreach ($properties as $key => $value) {
            $entity->$key = $value;
        }
        return $entity;
    }

    /**
     * 指定された条件でエンティティを検索します。
     *
     * @param $store
     * @param arra $properties
     */
    protected function findOne($store, $conditions = array()) {
        $store = aafwEntityStoreFactory::create($store);
        return $store->findOne(array(
            "conditions" => $conditions
        ));
    }

    /**
     * 指定された条件でエンティティを検索します。
     *
     * @param $store
     * @param arra $properties
     */
    protected function findById($store, $id) {
        $store = aafwEntityStoreFactory::create($store);
        return $store->findOne(array(
            "conditions" => array('id' => $id)
        ));
    }

    protected function findOneAsJson($store, $conditions = array(), $excepted_cols = array()) {
        $obj = $this->findOne($store, $conditions);
        if ($obj === null) {
            return "";
        }
        $array = $obj->toArray();
        unset($array['id']);
        unset($array['updated_at']);
        unset($array['created_at']);
        foreach ($excepted_cols as $col) {
            unset($array[$col]);
        }
        return json_encode($array, JSON_PRETTY_PRINT);
    }

    /**
     * 指定された条件でエンティティを検索します。
     *
     * @param $store
     * @param arra $properties
     */
    protected function find($store, $conditions = array()) {
        $store = aafwEntityStoreFactory::create($store);
        return $store->find(array(
            "conditions" => $conditions
        ));
    }

    /**
     * まだDBに永続化されていない、新しい空オブジェクトを作成します。１
     * @param $store
     * @param array $properties
     * @return mixed
     */
    protected function emptyObjectOf($store, $properties = array()) {
        $store = aafwEntityStoreFactory::create($store);
        $entity = $store->createEmptyObject();
        foreach ($properties as $key => $value) {
            $entity->$key = $value;
        }
        return $entity;
    }

    /**
     * エンティティを永続化します。
     *
     * @param $entity
     */
    protected function save($store, $entity) {
        $store = aafwEntityStoreFactory::create($store);
        $store->save($entity);
    }

    /**
     * 任意のクエリを実行します。
     *
     * @param $store
     * @param $properties
     */
    protected function executeQuery($query) {
        $dataBuilder = aafwDataBuilder::newBuilder();
        return $dataBuilder->executeUpdate($query);
    }

    protected function fetchResultSet($rs) {
        $dataBuilder = aafwDataBuilder::newBuilder();
        return $dataBuilder->fetchResultSet($rs);
    }

    /**
     * 指定されたIdを持つエンティティを物理削除します。
     *
     * @param $store エンティティを取得するストア
     * @param $id エンティティのId
     */
    protected function purge($store, $id) {
        $store = aafwEntityStoreFactory::create($store);
        $entity = $store->createEmptyObject();
        $entity->id = $id;
        $store->deletePhysical($entity);
    }

    /**
     * 指定したプロパティーで検索してエンティティを物理削除します。
     *
     * @param $store エンティティの作成対象のストア
     * @param $properties エンティティが持つプロパティ
     */
    protected function deleteEntities($store, $properties = array(), $is_logical = false) {
        $store = aafwEntityStoreFactory::create($store);
        $entities = $store->find($properties);
        foreach ($entities as $entity) {
            if ($is_logical) {
                $store->delete($entity);
            } else {
                $store->deletePhysical($entity);
            }
        }
    }

    /**
     * 指定したwhereコンディションでエンティティ検索してプロパティーを更新します。
     *
     * @param $store
     * @param $where
     * @param $properties
     */
    protected function updateEntities($store, $where, $properties) {
        $store = aafwEntityStoreFactory::create($store);
        $entities = $store->find($where);
        foreach ($entities as $entity) {
            foreach ($properties as $key => $value) {
                $entity->$key = $value;
            }
            return $store->save($entity);
        }
    }

    /**
     * 指定したプロパティーで検索してエンティティ数を返ります。
     *
     * @param $store
     * @param $properties
     * @return mixed
     */
    protected function countEntities($store, $properties) {
        $store = aafwEntityStoreFactory::create($store);
        return $store->count($properties);
    }

    /**
     * 指定されたストアが持つすべてのエンティティをTruncateで削除します。
     *
     * @param $store Truncate対象のストア名
     */
    protected function truncateAll($store) {
        $store = aafwEntityStoreFactory::create($store);
        $store->truncate();
    }

    /**
     * 最大値を取得します。
     *
     * @param $store
     * @param $col_name
     * @return mixed
     */
    protected function max($store, $col_name) {
        $store = aafwEntityStoreFactory::create($store);
        return $store->getMax($col_name);
    }


    /**
     * aafwDataBuilderを利用してSQL文を実行します。
     *
     * @param SQL文の名前
     * @param 引数
     * @return getBySQLの実行結果
     */
    public function __call( $name, $args ) {
        $dataBuilder = new aafwDataBuilder();
        return $dataBuilder->$name($args[0]);
    }

    /**
     * aafwControllerのキャッシュをクリアし、新しいaafwControllerのインスタンスを取得します。
     *
     * @param $site
     */
    protected function newController($site) {
        aafwController::clearInstance();
        return aafwController::getInstance($site);
    }

    /**
     * 新しいユーザーを作成します。
     *
     * @return mixed
     */
    protected function newUser() {
        $monpla_account_db = aafwDataBuilder::newBuilder("ut_monipla_account");
        $rs = $monpla_account_db->executeUpdate("SELECT MAX(id) FROM users");
        $row = $monpla_account_db->fetchResultSet($rs);
        $monipla_max_id = $row['MAX(id)'];
        $bc_max_id = $this->max("Users", "monipla_user_id");
        $max_id = (int) max($monipla_max_id, $bc_max_id);

        return $this->entity('Users',array('monipla_user_id' => $max_id + 1));
    }

    /**
     * 新しいBrand, User, BrandUsersRelationを一括で生成します。
     */
    protected function newBrandToBrandUsersRelation() {
        $brand = $this->entity('Brands');
        $user = $this->newUser();
        $brand_users_relation = $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user->id));
        return array($brand, $user, $brand_users_relation);
    }

    /**
     * 新しいBrand, Cp, CpActionGroup, CpActionを一括で生成します。
     */
    protected function newBrandToAction($cp_type = 0, $cp_action_type = 0) {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id, 'type' => $cp_type));
        $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id));
        $cp_action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => $cp_action_type));

        return array($brand, $cp, $cp_action_group, $cp_action);
    }

    protected function newBrandToPhotoUser() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();
        $user = $this->newUser();
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $photo_stream = $this->entity('PhotoStreams', array('brand_id' => $brand->id));
        $photo_user = $this->entity('PhotoUsers', array('cp_action_id' => $cp_action->id, 'cp_user_id' => $cp_user->id));
        $photo_entry = $this->entity('PhotoEntries', array('stream_id' => $photo_stream->id, 'photo_user_id' => $photo_user->id));
        return array($brand, $cp, $cp_action_group, $cp_action, $cp_user, $photo_stream, $photo_user, $photo_entry);
    }

    /**
     * プライベート・フィールドの値を取得します。simasu.
     *
     * @param $object
     * @param $field
     * @param $value
     */
    protected function getPrivateFieldValue($object, $field) {
        $class = get_class($object);
        $reflect = new ReflectionClass($class);
        $property = $reflect->getProperty($field);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * プライベート・フィールドに強制的に値を設定します。
     *
     * @param $object
     * @param $field
     * @param $value
     */
    protected function setPrivateFieldValue($object, $field, $value) {
        $class = get_class($object);
        $reflect = new ReflectionClass($class);
        $property = $reflect->getProperty($field);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * プライベート・メソッドを実行します
     * @param $object
     * @param $method
     * @param array $args
     * @return mixed
     */
    protected function invokePrivateMethod($object, $method, $args = array()) {
        $class = get_class($object);
        $reflect = new ReflectionMethod($class, $method);
        $reflect->setAccessible(true);
        return $reflect->invokeArgs($object, $args);
    }

    protected function joinCp($cp, $cp_action, $user) {
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        $cp_user_action_status = $this->entity('CpUserActionStatuses', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id, 'status' => CpUserActionStatus::JOIN));
        $cp_user_action_message = $this->entity('CpUserActionMessages', array('cp_user_id' => $cp_user->id, 'cp_action_id' => $cp_action->id));
        return array(
            $cp_user,
            $cp_user_action_status,
            $cp_user_action_message
        );
    }

    /**
     * BrandsとBrandsに関連する全てのエンティティを良い感じに削除する。
     */
    protected function clearBrandAndRelatedEntities() {
        $builder = aafwDataBuilder::newBuilder();
        if (self::$truncate_target_tables === null) {
            $rs = $builder->executeUpdate("SHOW TABLES");
            $tables = array();
            while ($row = $builder->fetchResultSet($rs)) {
                $table = $row[self::Tables_in_db_brandco];
                if (in_array($table, self::$truncate_inhibit_tables)) {
                    continue;
                }
                $tables[] = $table;
            }
            self::$truncate_target_tables = $tables;
        }

        $queries = array();
        foreach (self::$truncate_target_tables as $table) {
            if (!$table) {
                continue;
            }
            $rs = $builder->executeUpdate("SELECT COUNT(*) FROM " . $table);
            $row = $builder->fetchResultSet($rs);
            $count = $row['COUNT(*)'];
            if ($count > 0) {
                $queries[] = 'TRUNCATE TABLE ' . $table;
            }
        }

        if (count($queries) === 0) {
            return;
        }
        try {
            $builder->executeUpdate('SET FOREIGN_KEY_CHECKS = 0');
            $sql = join(';', $queries);
            $builder->executeMulti($sql);
        } finally {
            $builder->executeUpdate('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    /**
     * キャンペーン作成
     * @param $condition
     * @return array
     */
    protected function newCampaign($condition) {
        if (!$condition || $condition[0][0] != CpAction::TYPE_ENTRY) return;

        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));

        $cp_action_groups = array();
        $cp_actions = array();
        $cp_concrete_actions = array();

        foreach ($condition as $group_order_no => $cp_action_types) {
            $group_order_no = $group_order_no + 1;
            $cp_action_group = $this->entity('CpActionGroups', array('cp_id' => $cp->id, 'order_no' => $group_order_no));
            foreach ($cp_action_types as $order_no => $cp_action_type) {
                $cp_action = $this->entity('CpActions', array('cp_action_group_id' => $cp_action_group->id, 'type' => $cp_action_type, 'order_no' => $order_no + 1));
                $cp_concrete_actions[] = $this->newCpConcreteAction($cp_action);
                $cp_actions[] = $cp_action;
            }
            $cp_action_groups[] = $cp_action_group;
        }

        return array($brand, $cp, $cp_action_groups, $cp_actions, $cp_concrete_actions);
    }

    /**
     * キャンペーンを指定された条件で削除します。
     * 紐づくcp_action_groups,cp_actions,及び関連するconcrete_actionテーブルを同時に削除します。
     *
     * @param $condition cpsの検索条件
     */
    protected function deleteCampaigns($condition) {

        $cps = $this->find('Cps', $condition);

        foreach($cps as $cp) {

            $groups = $this->find('CpActionGroups', ['cp_id' => $cp->id]);

            foreach($groups as $group) {

                $actions = $this->find('CpActions', ['cp_action_group_id' => $group->id]);

                foreach($actions as $action) {
                    $this->deleteCpConcreteAction($action);
                }
                $this->deleteEntities('CpActions', ['cp_action_group_id' => $group->id]);
            }

            $this->deleteEntities('CpActionGroups', ['cp_id' => $cp->id]);
        }

        $this->deleteEntities('Cps', $condition);
    }

    /**
     * @param CpActions $cp_action
     * @return mixed
     */
    protected function newCpConcreteAction(CpAction $cp_action) {
        $cp_concrete_actions_table_name = 'cp' . CpAction::$concrete_action_list[$cp_action->type] . 'Actions';
        return $this->entity($cp_concrete_actions_table_name, array('cp_action_id' => $cp_action->id));
    }

    /**
     * @param CpAction $cp_action
     */
    protected function deleteCpConcreteAction(CpAction $cp_action) {
        $cp_concrete_actions_table_name = 'cp' . CpAction::$concrete_action_list[$cp_action->type] . 'Actions';
        $this->deleteEntities($cp_concrete_actions_table_name, array('cp_action_id' => $cp_action->id));
    }

    /**
     * ブランド新規登録ユーザ作成
     * @param Brands $brand
     * @return array
     */
    protected function newBrandUserByBrand(Brand $brand) {
        if (!$brand) return;

        $user = $this->newUser();
        $relation = $this->entity('BrandsUsersRelations', array('brand_id' => $brand->id, 'user_id' => $user->id));
        return array('users' => $user, 'relation' => $relation);
    }

    /**
     * キャンペーンユーザ作成
     * @param Cps $cp
     * @return array
     */
    protected function newCampaignUserByCp(Cp $cp) {
        if (!$cp) return;
        $user = $this->newUser();
        $relation = $this->entity('BrandsUsersRelations', array('brand_id' => $cp->brand_id, 'user_id' => $user->id));
        $cp_user = $this->entity('CpUsers', array('cp_id' => $cp->id, 'user_id' => $user->id));
        return array('users' => $user, 'relation' => $relation, 'cp_user' => $cp_user);
    }

    /**
     * 参加状況の更新
     * @param $cp_user_id
     * @param $cp_action_id
     * @param null $status
     * @param null $read
     */
    protected function updateCpUserActionMessageAndStatus($cp_user_id, $cp_action_id, $status = null, $read = null) {
        if ($read === null) {
            $read = CpUserActionMessage::STATUS_READ;
        }
        if ($status === null) {
            $status = CpUserActionStatus::JOIN;
        }
        $this->entity('CpUserActionMessages',
            array(
                'cp_user_id' => $cp_user_id,
                'cp_action_id' => $cp_action_id,
                'read_flg' => $read
            )
        );
        $this->entity('CpUserActionStatuses',
            array(
                'cp_user_id' => $cp_user_id,
                'cp_action_id' => $cp_action_id,
                'status' => $status
            )
        );
    }

    protected function newBrandGlobalSetting(Brand $brand, $name, $content) {
        $brand_global_setting = $this->entity('BrandGlobalSettings',
            array(
                'brand_id' => $brand->id,
                'name' => $name,
                'content' => $content
            )
        );
        return $brand_global_setting;
    }

    protected function newBrandPageSetting($condition) {
        $brand_page_setting = $this->entity('BrandPageSettings',
            array(
                'brand_id' => $condition['brand']->id,
                'public_flg' => $condition['public_flg'],
                'tag_text' => $condition['tag_text'],
                'agreement' => $condition['agreement'],
                'privacy_required_name' => $condition['privacy_required_name'],
                'privacy_required_sex' => $condition['privacy_required_sex'],
                'privacy_required_birthday' => $condition['privacy_required_birthday'],
                'privacy_required_address' => $condition['privacy_required_address'],
                'privacy_required_tel' => $condition['privacy_required_tel'],
                'privacy_required_restricted' => $condition['privacy_required_restricted'],
                'top_page_og_url' => $condition['top_page_og_url'],
                'meta_title' => $condition['meta_title'],
                'meta_description' => $condition['meta_description'],
                'meta_keyword' => $condition['meta_keyword'],
                'og_image_url' => $condition['og_image_url'],
            )
        );
        return $brand_page_setting;
    }

    protected function newBrandSocialAccounts($condition) {
        $brand_social_account = $this->entity('BrandSocialAccounts',
            array(
                'brand_id' => $condition['brand']->id,
                'social_media_account_id' => $condition['social_media_account_id'],
                'social_app_id' => $condition['social_app_id'],
                'token' => '',
                'store' => ''
            )
        );
        return $brand_social_account;
    }

    protected function convertToJson($entity, $excepted_cols = array()) {
        $array = $entity->toArray();
        unset($array['id']);
        unset($array['updated_at']);
        unset($array['created_at']);
        foreach ($excepted_cols as $col) {
            unset($array[$col]);
        }

        return json_encode($array, JSON_PRETTY_PRINT);
    }

    protected function newAAIDAccount($has_mail_address = true) {
        $builder = aafwDataBuilder::newBuilder("ut_monipla_account");
        $rs = $builder->executeUpdate("SELECT MAX(id) FROM users");
        $row = $builder->fetchResultSet($rs);
        $max_id = $row['MAX(id)'] + 1;
        $mail_address = $max_id . "@aainc.co.jp";
        if (!$has_mail_address) {
            $mail_address = '';
        }
        $builder->executeUpdate("
                INSERT INTO users
                    (name, mail_address, password, confirmed_mail_address, enabled_password, optin, del_type,
                    profile, profile_image_url, locale, del_flg, date_created, date_updated)
                VALUES
                	('hogera1', '" . $mail_address . "', '', 0, 0, 1, 0, NULL, NULL, 0, 0, NOW(), NOW())");
        return $max_id;
    }

    protected function addAAIDSocialAccount($user_id, $mail_address = "") {
        $builder = aafwDataBuilder::newBuilder("ut_monipla_account");
        $rs = $builder->executeUpdate("
            INSERT INTO social_accounts
                    (social_media_id, social_media_account_id, name, mail_address, profile_image_url,
                    profile_page_url, user_id, validated, access_token, refresh_token, friend_count, del_flg, date_created, date_updated)
            VALUES
	                (1, '373231552858267', 'hogera', '" . $mail_address . "', '', 'https://facebook.com/373231552858267', ". $user_id. ", 1, '', NULL, 3, 0, NOW(), NOW())
        ");
        if (!$rs) {
            throw new aafwException("Insertion failed!: user id={$user_id}, mail address={$mail_address}");
        }
        $row = $builder->fetchResultSet($rs);
        aafwLog4phpLogger::getDefaultLogger()->info("addAAIDSocialAccount result=" . json_encode($row, JSON_PRETTY_PRINT));

    }

    protected function getAAIDAccount($aaid) {
        return \Monipla\Core\MoniplaCore::getInstance()->getUserByQuery(array(
            'class' => 'Thrift_UserQuery', 'fields' => array('id' => $aaid)
        ));
    }

    protected function getModel($store) {
        return aafwEntityStoreFactory::create($store);
    }
}
