<?php

class PhotoStreamServiceTest extends BaseTest {

    /** @var PhotoStreamService $photo_stream_service */
    private $photo_stream_service;
    private $stream;

    public function setUp() {
        $this->photo_stream_service = aafwServiceFactory::create("PhotoStreamService");
    }

    public function test_getAvailableEntriesByStreamId() {
        $photo_user_ids = array();
        $photo_user_ids[] = $this->createEntries();
        $photo_user_ids[] = $this->createEntries();

        // 除外ID
        $this->exclude_photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($photo_user_ids[0]);

        // 対象ID
        $photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($photo_user_ids[1]);

        $result = $this->photo_stream_service->getAvailableEntriesByStreamId($this->stream->id, 1, 10);

        $filter_func = function($row) {
            return $row->id != $this->exclude_photo_entry->id;
        };

        $result = $result->filter($filter_func);

        $this->assertEquals($photo_entry->id, $result[0]->id);
    }

    public function test_getAvailableEntriesByStreamId_page0() {
        $photo_user_ids = array();
        $photo_user_ids[] = $this->createEntries();
        $photo_user_ids[] = $this->createEntries();

        // 除外ID
        $this->exclude_photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($photo_user_ids[0]);

        // 対象ID
        $photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($photo_user_ids[1]);

        $result = $this->photo_stream_service->getAvailableEntriesByStreamId($this->stream->id, 0, 10);

        $filter_func = function($row) {
            return $row->id != $this->exclude_photo_entry->id;
        };
        $result = $result->filter($filter_func);

        $this->assertEquals($photo_entry->id, $result[0]->id);
    }

    public function test_getAvailableEntriesByStreamId_limit0() {
        $photo_user_ids = array();
        $photo_user_ids[] = $this->createEntries();
        $photo_user_ids[] = $this->createEntries();

        // 除外ID
        $this->exclude_photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($photo_user_ids[0]);

        // 対象ID
        $photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($photo_user_ids[1]);

        $result = $this->photo_stream_service->getAvailableEntriesByStreamId($this->stream->id, 1, 0);

        $filter_func = function($row) {
            return $row->id != $this->exclude_photo_entry->id;
        };
        $result = $result->filter($filter_func);

        $this->assertEquals($photo_entry->id, $result[0]->id);
    }

    public function test_getAvailableEntriesByStreamId_null() {
        $this->createEntries();
        $result = $this->photo_stream_service->getAvailableEntriesByStreamId(null, 1, 10);
        $this->assertEquals(array(), $result);
    }

    public function test_getAvailableEntriesByStreamId_empty() {
        $this->createEntries();
        $result = $this->photo_stream_service->getAvailableEntriesByStreamId('', 1, 10);
        $this->assertEquals(array(), $result);
    }

    public function test_getAvailableEntriesByStreamId_space() {
        $this->createEntries();
        $result = $this->photo_stream_service->getAvailableEntriesByStreamId(' ', 1, 10);
        $this->assertEquals(array(), $result);
    }

    private function createEntries() {
        list($brand, $cp, $cp_action_group, $cp_action) = $this->newBrandToAction();

        /** @var UserService $user_service */
        $user = $this->newUser();

        /** @var CpUserService $cp_user_service */
        $cp_user_service = aafwServiceFactory::create("CpUserService");
        $cp_user = $cp_user_service->createCpUser($cp->id, $user->id);

        $this->stream = $this->photo_stream_service->createEmptyStream();
        $this->stream->brand_id = $brand->id;
        $this->photo_stream_service->updateStream($this->stream);

        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = aafwServiceFactory::create("PhotoUserService");
        $params = array(
            'cp_action_id' => $cp_action->id,
            'cp_user_id' => $cp_user->id
        );
        $photo_user = $photo_user_service->createPhotoUser($params);

        $entry = $this->photo_stream_service->createEmptyEntry();
        $entry->stream_id = $this->stream->id;
        $entry->photo_user_id = $photo_user->id;
        $this->photo_stream_service->updateEntry($entry);

        return $photo_user->id;
    }
}