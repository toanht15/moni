<?php
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class InstagramApiTest extends BaseTest {

    private $embed_media = 'https://instagram.com/p/1NR-_wDHYL/';

    /** @var Instagram $instagram */
    private $instagram;

    public function setUp() {
        $this->instagram = new Instagram;
    }

    public function testGetEmbedMedia() {
        $response = $this->instagram->getEmbedMedia($this->embed_media);
        $this->assertNotNull($response->html);
    }
}
