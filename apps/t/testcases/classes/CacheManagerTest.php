<?php

AAFW::import('jp.aainc.lib.db.aafwRedisManager');
AAFW::import('jp.aainc.classes.CacheManager');

class CacheManagerTest extends BaseTest {

    protected function setUp() {
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
        $this->setPrivateFieldValue(new CacheManager(), "connection", new CacheManager_Connection());
    }

    public function testSchemaTTL() {
        $target = new CacheManager();
        $schema_ttl = 30;
        $this->assertEquals($schema_ttl, $this->getPrivateFieldValue($target, "schema_ttl"));
    }

    public function testAddPanelCache01_whenAbsent() {
        $target = new CacheManager();

        $brand_id = 1;
        $cache_type = 2;
        $media_type = 3;
        $page = 4;
        $panel_list = "test";
        $target->addPanelCache($brand_id, $cache_type, $media_type, $page, $panel_list);

        $value = aafwRedisManager::getRedisInstance()->hGet("cache:bp:{$brand_id}", "{$cache_type}:{$media_type}:{$page}");

        $this->assertEquals('"test"', $value);
    }


    public function testAddPanelCache02_whenExist() {
        $target = new CacheManager();

        $brand_id = 1;
        $cache_type = 2;
        $media_type = 3;
        $page = 4;
        $panel_list = "test";

        // set up before add panel cache.
        aafwRedisManager::getRedisInstance()->hSet("cache:bp:{$brand_id}", "{$cache_type}:{$media_type}:{$page}", "hoge");

        $target->addPanelCache($brand_id, $cache_type, $media_type, $page, $panel_list);

        $value = aafwRedisManager::getRedisInstance()->hGet("cache:bp:{$brand_id}", "{$cache_type}:{$media_type}:{$page}");

        $this->assertEquals('"test"', $value);
    }

    public function testGetPanelCache01_whenExist() {
        $target = new CacheManager();

        $brand_id = 1;
        $cache_type = 2;
        $media_type = 3;
        $page = 4;
        $panel_list = array('name' => 'value');

        // set up before get panel cache.
        aafwRedisManager::getRedisInstance()->hSet("cache:bp:{$brand_id}", "{$cache_type}:{$media_type}:{$page}", json_encode($panel_list));

        $value = $target->getPanelCache($brand_id, $cache_type, $media_type, $page);

        $this->assertEquals($panel_list, $value);
    }

    public function testGetPanelCache02_whenAbsent() {
        $target = new CacheManager();

        $brand_id = 1;
        $cache_type = 2;
        $media_type = 3;
        $page = 4;

        $value = $target->getPanelCache($brand_id, $cache_type, $media_type, $page);

        $this->assertNull($value);
    }

    public function testDeletePanelCache01_whenExist() {
        $target = new CacheManager();

        $brand_id = 1;
        $cache_type = 2;
        $media_type = 3;
        $page = 4;

        // set up before delete panel cache.
        aafwRedisManager::getRedisInstance()->hSet("cache:bp:{$brand_id}", "{$cache_type}:{$media_type}:{$page}", "hoge");

        $target->deletePanelCache($brand_id);

        $value = aafwRedisManager::getRedisInstance()->hGet("cache:bp:{$brand_id}", "{$cache_type}:{$media_type}:{$page}");

        $this->assertFalse($value);
    }

    public function testDeletePanelCache02_whenAbsent() {
        $target = new CacheManager();

        $brand_id = 1;
        $cache_type = 2;
        $media_type = 3;
        $page = 4;

        $target->deletePanelCache($brand_id);

        $value = aafwRedisManager::getRedisInstance()->hGet("cache:bp:{$brand_id}", "{$cache_type}:{$media_type}:{$page}");

        $this->assertFalse($value);
    }

    public function testAddCache01_whenAbsentAndParamsAreNull() {
        $target = new CacheManager();

        $key = "key";
        $value = "value";
        $target->addCache($key, $value);

        $value = aafwRedisManager::getRedisInstance()->get("cache:{$key}");

        $this->assertEquals('value', $value);
    }

    public function testAddPanelCache02_whenExistAndParamsAreNotNull() {
        $target = new CacheManager();

        $key = "key";
        $params = array("hoge", "piyo");
        $value = "value";

        aafwRedisManager::getRedisInstance()->set("cache:{$key}:hoge:piyo", "hoge");

        $target->addCache($key, $value, $params);

        $value = aafwRedisManager::getRedisInstance()->get("cache:{$key}:hoge:piyo");

        $this->assertEquals('value', $value);
    }

    public function testAddPanelCache03_whenConnectionErrorOccurred() {
        $target = new CacheManager();

        $key = "key";
        $value = "value";

        $target->getRedis()->close();

        try {
            $target->addCache($key, $value);
        } finally {
            $this->setPrivateFieldValue($target, "connection", new CacheManager_Connection());
            $target->getRedis()->ping();
        }
    }

    public function testGetCache01_whenAbsentAndParamsAreNull() {
        $target = new CacheManager();
        $key = "key";

        $value = $target->getCache($key);

        $this->assertNull($value);
    }

    public function testGetCache02_whenExistAndParamsAreNull() {
        $target = new CacheManager();

        $key = "key";
        $preValue = array("name" => "value");
        aafwRedisManager::getRedisInstance()->set("cache:{$key}", json_encode($preValue));

        $value = $target->getCache($key);

        $this->assertEquals($preValue, $value);
    }

    public function testGetCache03_whenExistAndParamsAreNotNull() {
        $target = new CacheManager();

        $key = "key";
        $params = array("hoge", "piyo");
        $preValue = array("name" => "value");

        aafwRedisManager::getRedisInstance()->set("cache:{$key}:hoge:piyo", json_encode($preValue));

        $value = $target->getCache($key, $params);

        $this->assertEquals($preValue, $value);
    }

    public function testGetCache04_whenConnectionErrorOccurred() {
        $target = new CacheManager();

        $key = "key";
        $preValue = "value";
        aafwRedisManager::getRedisInstance()->set("cache:{$key}", $preValue);

        $target->getRedis()->close();

        try {
            $value = $target->getCache($key);
            $this->assertNull($value);
        } finally {
            $this->setPrivateFieldValue($target, "connection", new CacheManager_Connection());
            $target->getRedis()->ping();
        }
    }

    public function testDeleteCache01_whenExistAndParamsAreNull() {
        $target = new CacheManager();

        $key = "key";

        // set up before delete cache.
        aafwRedisManager::getRedisInstance()->set("cache:{$key}",  "test");

        $target->deleteCache($key);

        $value = aafwRedisManager::getRedisInstance()->exists("cache:key");

        $this->assertFalse($value);
    }

    public function testDeleteCache02_whenExistAndParamsAreNotNull() {
        $target = new CacheManager();

        $key = "key";
        $params = array("hoge", "piyo");

        // set up before delete cache.
        aafwRedisManager::getRedisInstance()->set("cache:{$key}:hoge:piyo",  "test");

        $target->deleteCache($key, $params);

        $value = aafwRedisManager::getRedisInstance()->exists("cache:key:hoge:piyo");

        $this->assertFalse($value);
    }

    public function testDeleteCache03_whenAbsentAndParamsAreNotNull() {
        $target = new CacheManager();

        $key = "key";

        $target->deleteCache($key);
    }

    public function testDeleteCache04_whenExistAndParamsAreNotNullAndConnectionErrorOccurred() {
        $target = new CacheManager();

        $key = "key";

        // set up before delete cache.
        aafwRedisManager::getRedisInstance()->set("cache:{$key}",  "test");

        $target->getRedis()->close();

        try {
            $target->deleteCache($key);
        } finally {
            $this->setPrivateFieldValue($target, "connection", new CacheManager_Connection());
            $target->getRedis()->ping();
        }

        $this->assertTrue(aafwRedisManager::getRedisInstance()->exists("cache:{$key}"));
    }

    public function testBeginBatch() {
        $target = new CacheManager();

        $key = "key";

        try {
            $target->beginBatch();
            $target->addCache($key, "test");
            $this->assertFalse(aafwRedisManager::getRedisInstance()->exists("cache:{$key}"));
        } finally {
            $target->resetBatch();
        }
    }

    public function testFlushBatch01_oneCommand() {
        $target = new CacheManager();

        $key = "key";

        try {
            $target->beginBatch();
            $target->addCache($key, "test");
            $target->flushBatch();
            $this->assertTrue(aafwRedisManager::getRedisInstance()->exists("cache:{$key}"));
        } finally {
            $target->resetBatch();
        }
    }

    public function testFlushBatch02_twoCommand() {
        $target = new CacheManager();

        $key1 = "key1";
        $key2 = "key2";
        $value1 = "test1";
        $value2 = "test2";

        try {
            $target->beginBatch();
            $target->addCache($key1, $value1);
            $target->addCache($key2, $value2);
            $target->flushBatch();

            $this->assertEquals(array($value1, $value2), aafwRedisManager::getRedisInstance()->mget(array("cache:{$key1}", "cache:{$key2}")));
        } finally {
            $target->resetBatch();
        }
    }

    public function testResetBatch() {
        $target = new CacheManager();

        $key = "key";

        try {
            $target->beginBatch();
            $target->addCache($key, "test");
            $target->resetBatch();
            $this->assertFalse(aafwRedisManager::getRedisInstance()->exists("cache:{$key}"));
        } finally {
            $target->resetBatch();
        }
    }

    public function testGetNotificationCount01_whenExist() {
        $target = new CacheManager();

        $brand_id = "1";
        $user_id = "2";
        $preValue = array(CacheManager::JSON_KEY_UPDATED_AT => date('Y-m-d'), CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT => 3);

        // set up before delete cache.
        aafwRedisManager::getRedisInstance()->hSet("cache:nc:{$brand_id}", $user_id, json_encode($preValue));

        $value = $target->getNotificationCount($brand_id, $user_id);

        $this->assertEquals($preValue, $value);
    }

    public function testGetNotificationCount02_whenAbsent() {
        $target = new CacheManager();

        $brand_id = "1";
        $user_id = "2";
        $value = $target->getNotificationCount($brand_id, $user_id);

        $this->assertNull($value);
    }

    public function testSetNotificationCount01_whenExist() {
        $target = new CacheManager();

        $brand_id = "1";
        $user_id1 = "2";
        $preValue1 = array(CacheManager::JSON_KEY_UPDATED_AT => date('Y-m-d'), CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT => 3);
        $preValue2 = array(CacheManager::JSON_KEY_UPDATED_AT => date('Y-m-d'), CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT => 4);

        // set up before delete cache.
        aafwRedisManager::getRedisInstance()->hMset("cache:nc:{$brand_id}", array($user_id1 => json_encode($preValue1)));

        $target->setNotificationCount($preValue2, $brand_id, $user_id1);

        $value = json_decode(aafwRedisManager::getRedisInstance()->hGet("cache:nc:{$brand_id}", $user_id1), true);

        $this->assertEquals($preValue2, $value);
    }

    public function testSetNotificationCount02_whenAbsent() {
        $target = new CacheManager();

        $brand_id = "1";
        $user_id = "2";
        $preValue = array(CacheManager::JSON_KEY_UPDATED_AT => date('Y-m-d'), CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT => 3);

        $target->setNotificationCount($preValue, $brand_id, $user_id);
        $value = json_decode(aafwRedisManager::getRedisInstance()->hGet("cache:nc:{$brand_id}", $user_id), true);

        $this->assertEquals($preValue, $value);
    }

    public function testResetNotificationCount01_whenExist() {
        $target = new CacheManager();

        $brand_id = "1";
        $user_id1 = "2";
        $user_id2 = "3";
        $preValue1 = array(CacheManager::JSON_KEY_UPDATED_AT => date('Y-m-d'), CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT => 4);
        $preValue2 = array(CacheManager::JSON_KEY_UPDATED_AT => date('Y-m-d'), CacheManager::JSON_KEY_UNREAD_MESSAGE_COUNT => 5);

        // set up before delete cache.
        aafwRedisManager::getRedisInstance()->hMset("cache:nc:{$brand_id}", array($user_id1 => json_encode($preValue1), $user_id2 => json_encode($preValue2)));

        $target->resetNotificationCount($brand_id, $user_id1);
        $values = aafwRedisManager::getRedisInstance()->hMGet("cache:nc:{$brand_id}", array($user_id1 , $user_id2));
        $this->assertEquals(array($user_id1 => false, $user_id2 => json_encode($preValue2)), $values);
    }

    public function testResetNotificationCount02_whenAbsent() {
        $target = new CacheManager();

        $brand_id = "1";
        $user_id = "2";

        $target->resetNotificationCount($brand_id, $user_id);
    }

    public function testSetMessageHistoryCache01_whenExist() {
        $target = new CacheManager();

        $cp_action_id = "1";
        $preValue = "hoge";
        $value = "value";

        // set up before delete cache.
        aafwRedisManager::getRedisInstance()->set("cache:message_delivered_history:{$cp_action_id}", $preValue);

        $target->setMessageHistoryCache($cp_action_id, $value);

        $after_value = aafwRedisManager::getRedisInstance()->get("cache:message_delivered_history:{$cp_action_id}");

        $this->assertEquals($value, $after_value);
    }

    public function testSetMessageHistoryCache02_whenAbsent() {
        $target = new CacheManager();

        $cp_action_id = "1";
        $value = "value";

        $target->setMessageHistoryCache($cp_action_id, $value);

        $after_value = aafwRedisManager::getRedisInstance()->get("cache:message_delivered_history:{$cp_action_id}");

        $this->assertEquals($value, $after_value);
    }

    public function testGetMessageHistoryCache01_whenExist() {
        $target = new CacheManager();

        $cp_action_id = "1";
        $value = array("key" => "value");

        // set up before delete cache.
        aafwRedisManager::getRedisInstance()->set("cache:message_delivered_history:{$cp_action_id}", json_encode($value));

        $after_value = $target->getMessageHistoryCache($cp_action_id);

        $this->assertEquals($value, $after_value);
    }

    public function testGetMessageHistoryCache02_whenAbsent() {
        $target = new CacheManager();

        $cp_action_id = "1";

        $after_value = $target->getMessageHistoryCache($cp_action_id);

        $this->assertNull($after_value);
    }

    public function testGetBrandInfo01_whenExistAndNotDebug() {
        $target = new CacheManager();
        $this->setPrivateFieldValue($target, "debug", 0);
        $brand_id = "1";

        // set up before get cache.
        aafwRedisManager::getRedisInstance()->set("cache:bic:{$brand_id}", serialize(array('HOGE!')));

        $after_value = $target->getBrandInfo($brand_id);

        $this->assertEquals(array("HOGE!"), $after_value);
    }

    public function testSetBrandInfo01_whenExistAndNotDebug() {
        $target = new CacheManager();
        $this->setPrivateFieldValue($target, "debug", 0);
        $brand_id = "1";

        $target->setBrandInfo($brand_id, array('HOGE!'));

        $cache = aafwRedisManager::getRedisInstance()->get("cache:bic:{$brand_id}");

        $this->assertEquals(array("HOGE!"), unserialize($cache));
    }

    public function testGetSchemaCache01_whenNotExists() {
        $target = new CacheManager();
        $this->assertFalse($target->getSchemaCache("NOT_EXISTS_TABLE"));
    }

    public function testGetSchemaCache02_whenExists() {
        $store = aafwEntityStoreFactory::create("Brands");
        $db = DBFactory::getInstance()->getDB()->Read;
        $table_info = $db->getTableInfo($store->getTableName());

        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_SCHEMA . $store->getTableName(), serialize($table_info));

        $target = new CacheManager();
        $result = $target->getSchemaCache($store->getTableName());

        $this->assertEquals(json_encode($table_info),json_encode($result));
    }

    public function testSetSchemaCache01_whenNotExists() {
        $store = aafwEntityStoreFactory::create("Brands");
        $db = DBFactory::getInstance()->getDB()->Read;
        $table_info = $db->getTableInfo($store->getTableName());

        $target = new CacheManager();
        $target->setSchemaCache($store->getTableName(), $table_info);

        $result = aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_SCHEMA . $store->getTableName());
        $unserialize = unserialize($result);
        $this->assertEquals(json_encode($table_info),json_encode($unserialize));
    }

    public function testSetSchemaCache02_whenExists() {
        $store = aafwEntityStoreFactory::create("Brands");
        $db = DBFactory::getInstance()->getDB()->Read;
        $table_info = $db->getTableInfo($store->getTableName());

        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_SCHEMA . $store->getTableName(), serialize($table_info));

        $target = new CacheManager();
        $target->setSchemaCache($store->getTableName(), $table_info);

        $result = aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_SCHEMA . $store->getTableName());
        $unserialize = unserialize($result);
        $this->assertEquals(json_encode($table_info),json_encode($unserialize));
    }

    public function testGetSchemaCaches01_whenOneSuccess() {
        $store = aafwEntityStoreFactory::create("Brands");
        $db = DBFactory::getInstance()->getDB()->Read;
        $table_info = $db->getTableInfo($store->getTableName());

        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_SCHEMA . $store->getTableName(), serialize($table_info));

        $target = new CacheManager();
        $caches = $target->getSchemaCaches(array('brands'));

        $result = aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_SCHEMA . 'brands');
        $unserialize = unserialize($result);
        $this->assertEquals(
            array('count' => count($caches), 'brands' => json_encode($caches['brands'])),
            array('count' => 1, 'brands' => json_encode($unserialize))
        );
    }

    public function testGetSchemaCaches02_whenOneSuccessAndOneFailure() {
        $store = aafwEntityStoreFactory::create("Brands");
        $db = DBFactory::getInstance()->getDB()->Read;
        $table_info = $db->getTableInfo($store->getTableName());

        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_SCHEMA . $store->getTableName(), serialize($table_info));

        $target = new CacheManager();
        $caches = $target->getSchemaCaches(array('brands', 'users'));

        $result = aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_SCHEMA . 'brands');
        $unserialize = unserialize($result);
        $this->assertEquals(
            array('count' => count($caches), 'brands' => json_encode($caches['brands']), 'users' => $caches['users']),
            array('count' => 2, 'brands' => json_encode($unserialize), 'users' => null)
        );
    }

    public function testGetSchemaCaches03_whenTwoSuccess() {
        $db = DBFactory::getInstance()->getDB()->Read;
        //
        $brands = aafwEntityStoreFactory::create("Brands");
        $brands_table_info = $db->getTableInfo($brands->getTableName());
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_SCHEMA . $brands->getTableName(), serialize($brands_table_info));
        //
        $users = aafwEntityStoreFactory::create("Users");
        $users_table_info = $db->getTableInfo($users->getTableName());
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_SCHEMA . $users->getTableName(), serialize($users_table_info));

        $target = new CacheManager();
        $caches = $target->getSchemaCaches(array('brands', 'users'));

        $expected_brands = aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_SCHEMA . 'brands');
        $expected_brands = unserialize($expected_brands);
        //
        $expected_users = aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_SCHEMA . 'users');
        $expected_users = unserialize($expected_users);

        $this->assertEquals(
            array('count' => count($caches), 'brands' => json_encode($caches['brands']), 'users' => json_encode($caches['users'])),
            array('count' => 2, 'brands' => json_encode($expected_brands), 'users' => json_encode($expected_users))
        );
    }
}
