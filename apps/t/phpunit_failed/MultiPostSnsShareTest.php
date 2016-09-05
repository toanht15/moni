<?php
AAFW::import('jp.aainc.classes.batch.MultiPostSnsShare');

class MultiPostSnsShareTest extends BaseTest {

    /** @var MultiPostSnsShare $multi_post_sns_share */
    private $multi_post_sns_share;

    private $properties;

    public function setUp() {
        $this->multi_post_sns_share = new MultiPostSnsShare();
        aafwApplicationConfig::getInstance()->loadYAML(AAFW_DIR . '/t/test_files/property.yml');
        $this->properties = aafwApplicationConfig::getInstance()->getValues();
    }

//    public function test_executeProcess_写真投稿キャンペーン_FBシェア_成功() {
//        // 写真作る
//        list($brand, $cp, $cp_action_group, $cp_action, $cp_user, $photo_stream, $photo_user, $photo_entry) = $this->newBrandToPhotoUser();
//
//        $share_text = sha1(mt_rand());
//
//        // シェア入れる
//        $photo_user_share = $this->entity('PhotoUserShares', array(
//            'photo_user_id' => $photo_user->id,
//            'share_text' => $share_text,
//            'social_media_type' => SocialAccount::SOCIAL_MEDIA_FACEBOOK)
//        );
//
//        $multi_post_sns_queue = $this->entity('MultiPostSnsQueues', array(
//            'social_media_type' => SocialAccount::SOCIAL_MEDIA_FACEBOOK,
//            'social_account_id' => $this->properties['Facebook']['UserId'],
//            'access_token' => $this->properties['Facebook']['UserAccessToken'],
//            'share_text' => $share_text,
//            'share_image_url' => $this->properties['ImageUrl'],
//            'share_url' => 'http://cp.monipla.com/',
//            'share_title' => 'test',
//            'callback_function_type' => MultiPostSnsQueue::CALLBACK_UPDATE_PHOTO_USER_SHARE,
//            'callback_parameter' => $photo_user->id
//        ));
//
//        // 実行
//        $this->multi_post_sns_share->executeProcess();
//
//        // Assert
//        $multi_post_sns_queue = aafwEntityStoreFactory::create('MultiPostSnsQueues')->findOne($multi_post_sns_queue->id);
//        $this->assertNull($multi_post_sns_queue);
//
//        $photo_user_share = aafwEntityStoreFactory::create('PhotoUserShares')->findOne($photo_user_share->id);
//        $this->assertEquals(MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS, $photo_user_share->execute_status);
//    }

    public function test_executeProcess_写真投稿キャンペーン_FBシェア_アクセストークン不正() {
        // 写真作る
        list($brand, $cp, $cp_action_group, $cp_action, $cp_user, $photo_stream, $photo_user, $photo_entry) = $this->newBrandToPhotoUser();

        $share_text = sha1(mt_rand());

        // シェア入れる
        $photo_user_share = $this->entity('PhotoUserShares', array(
                'photo_user_id' => $photo_user->id,
                'share_text' => $share_text,
                'social_media_type' => SocialAccount::SOCIAL_MEDIA_FACEBOOK)
        );

        $multi_post_sns_queue = $this->entity('MultiPostSnsQueues', array(
            'social_media_type' => SocialAccount::SOCIAL_MEDIA_FACEBOOK,
            'social_account_id' => $this->properties['Facebook']['UserId'],
            'access_token' => 'abc',
            'share_text' => $share_text,
            'share_image_url' => $this->properties['ImageUrl'],
            'share_url' => 'http://cp.monipla.com/',
            'share_title' => 'test',
            'callback_function_type' => MultiPostSnsQueue::CALLBACK_UPDATE_PHOTO_USER_SHARE,
            'callback_parameter' => $photo_user->id
        ));

        // 実行
        $this->multi_post_sns_share->executeProcess();

        // Assert
        $multi_post_sns_queue = aafwEntityStoreFactory::create('MultiPostSnsQueues')->findOne($multi_post_sns_queue->id);
        $this->assertEquals(1, $multi_post_sns_queue->error_flg);
        $this->assertNotNull($multi_post_sns_queue->api_result);

        $photo_user_share = aafwEntityStoreFactory::create('PhotoUserShares')->findOne($photo_user_share->id);
        $this->assertEquals(MultiPostSnsQueue::EXECUTE_STATUS_ERROR, $photo_user_share->execute_status);

        aafwEntityStoreFactory::create('MultiPostSnsQueues')->delete($multi_post_sns_queue);
    }

//    public function test_executeProcess_写真投稿キャンペーン_TWシェア_成功() {
//        // 写真作る
//        list($brand, $cp, $cp_action_group, $cp_action, $cp_user, $photo_stream, $photo_user, $photo_entry) = $this->newBrandToPhotoUser();
//
//        $share_text = sha1(mt_rand());
//
//        // シェア入れる
//        $photo_user_share = $this->entity('PhotoUserShares', array(
//            'photo_user_id' => $photo_user->id,
//            'share_text' => $share_text,
//            'social_media_type' => SocialAccount::SOCIAL_MEDIA_TWITTER)
//        );
//
//        $multi_post_sns_queue = $this->entity('MultiPostSnsQueues', array(
//            'social_media_type' => SocialAccount::SOCIAL_MEDIA_TWITTER,
//            'social_account_id' => $this->properties['Twitter']['UserId'],
//            'access_token' => $this->properties['Twitter']['UserAccessToken'],
//            'access_refresh_token' => $this->properties['Twitter']['UserRefreshToken'],
//            'share_text' => $share_text,
//            'share_image_url' => $this->properties['ImageUrl'],
//            'share_url' => 'http://cp.monipla.com/',
//            'share_title' => 'test',
//            'callback_function_type' => MultiPostSnsQueue::CALLBACK_UPDATE_PHOTO_USER_SHARE,
//            'callback_parameter' => $photo_user->id
//        ));
//
//        // 実行
//        $this->multi_post_sns_share->executeProcess();
//
//        // Assert
//        $multi_post_sns_queue = aafwEntityStoreFactory::create('MultiPostSnsQueues')->findOne($multi_post_sns_queue->id);
//        $this->assertNull($multi_post_sns_queue);
//
//        $photo_user_share = aafwEntityStoreFactory::create('PhotoUserShares')->findOne($photo_user_share->id);
//        $this->assertEquals(MultiPostSnsQueue::EXECUTE_STATUS_SUCCESS, $photo_user_share->execute_status);
//
//        // 後処理
//        $multi_post_sns_queue = aafwEntityStoreFactory::create('MultiPostSnsQueues')->findOne($multi_post_sns_queue->id);
//        if ($multi_post_sns_queue) {
//            aafwEntityStoreFactory::create('MultiPostSnsQueues')->delete($multi_post_sns_queue);
//        }
//    }

    public function test_executeProcess_写真投稿キャンペーン_TWシェア_アクセストークン不正() {
        // 写真作る
        list($brand, $cp, $cp_action_group, $cp_action, $cp_user, $photo_stream, $photo_user, $photo_entry) = $this->newBrandToPhotoUser();

        $share_text = sha1(mt_rand());

        // シェア入れる
        $photo_user_share = $this->entity('PhotoUserShares', array(
                'photo_user_id' => $photo_user->id,
                'share_text' => $share_text,
                'social_media_type' => SocialAccount::SOCIAL_MEDIA_TWITTER)
        );

        $multi_post_sns_queue = $this->entity('MultiPostSnsQueues', array(
            'social_media_type' => SocialAccount::SOCIAL_MEDIA_TWITTER,
            'social_account_id' => $this->properties['Twitter']['UserId'],
            'access_token' => 'abc',
            'access_refresh_token' => 'abc',
            'share_text' => $share_text,
            'share_image_url' => $this->properties['ImageUrl'],
            'share_url' => 'http://cp.monipla.com/',
            'share_title' => 'test',
            'callback_function_type' => MultiPostSnsQueue::CALLBACK_UPDATE_PHOTO_USER_SHARE,
            'callback_parameter' => $photo_user->id
        ));

        // 実行
        $this->multi_post_sns_share->executeProcess();

        // Assert
        $multi_post_sns_queue = aafwEntityStoreFactory::create('MultiPostSnsQueues')->findOne($multi_post_sns_queue->id);
        $this->assertEquals(1, $multi_post_sns_queue->error_flg);
        $this->assertNotNull($multi_post_sns_queue->api_result);

        $photo_user_share = aafwEntityStoreFactory::create('PhotoUserShares')->findOne($photo_user_share->id);
        $this->assertEquals(MultiPostSnsQueue::EXECUTE_STATUS_ERROR, $photo_user_share->execute_status);

        aafwEntityStoreFactory::create('MultiPostSnsQueues')->delete($multi_post_sns_queue);
    }

}