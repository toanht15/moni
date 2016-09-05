<?php

class FacebookApiClientTest extends BaseTest {

    /** @var FacebookApiClient $facebook_api_client */
    private $facebook_api_client;
    private $properties;

    public function setUp() {
        $this->facebook_api_client = new FacebookApiClient();
        aafwApplicationConfig::getInstance()->loadYAML(AAFW_DIR . '/t/test_files/property.yml');
        $this->properties = aafwApplicationConfig::getInstance()->getValues();
    }

    public function test_getFullImagePost() {
        $this->facebook_api_client->setToken($this->properties['Facebook']['PageAccessToken']);
        $response = $this->facebook_api_client->getResponse('GET', '/me/posts');
        $full_image_post_response =  $this->facebook_api_client->getFullImagePost('/' . $response['data'][0]->id);
        if ($full_image_post_response && $full_image_post_response['full_picture']) {
            $this->assertNotNull($full_image_post_response['full_picture']);
        }
    }

    public function test_getUserFeed() {
        $this->facebook_api_client->setToken($this->properties['Facebook']['PageAccessToken']);
        $response = $this->facebook_api_client->getUserFeed('/' . $this->properties['Facebook']['PageId'] . '/feed');
        $this->assertNotNull($response['data'][0]->id);
    }

    public function test_getPageInfo() {
        $this->facebook_api_client->setToken($this->properties['Facebook']['PageAccessToken']);
        $response = $this->facebook_api_client->getPageInfo('/' . $this->properties['Facebook']['PageId']);
        $this->assertNotNull($response['id']);
    }

    public function test_getPostDetail() {
        $this->facebook_api_client->setToken($this->properties['Facebook']['PageAccessToken']);
        $response = $this->facebook_api_client->getResponse('GET', '/me/posts');
        $response = $this->facebook_api_client->getPostDetail('/' . $response['data'][0]->id);
        $this->assertNotNull($response['id']);
    }
}
