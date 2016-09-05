<?php
AAFW::import('jp.aainc.classes.services.BrandOuterTokenService');

class BrandOuterTokenServiceTest extends BaseTest {

    /** @var BrandOuterTokenService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create('BrandOuterTokenService');
    }

    /**
     * @test
     */
    public function create_新規登録成功() {
        list($brand, $user, $brand_user_relation) = $this->newBrandToBrandUsersRelation();
        $brand->directory_name = 'unit_test_brand';
        $social_app = $this->entity('SocialApps', [
            'provier' => 100,
            'name' => 'unit_test',
        ]);
        $user_id = 5;
        $brand_outer_token = $this->target->create($brand, $social_app->id, $user_id);

        $this->assertSame($brand->id, $brand_outer_token->brand_id);
        $this->assertSame($social_app->id, $brand_outer_token->social_app_id);
        $this->assertSame($user_id, $brand_outer_token->user_id);
        $this->assertNotNull($brand_outer_token->token);
    }

    /**
     * @test
     */
    public function create_新規登録失敗() {
        $store = aafwEntityStoreFactory::create('Brands');
        $brand = $store->createEmptyObject();
        $brand->id = 10;
        $brand->directory_name = 'unit_test_brand';

        $user_id = 5;
        $social_app_id = 1;

        try {
            $brand_outer_token = $this->target->create($brand, $social_app_id, $user_id);
            $this->fail('登録する際に、例外が発生しませんでした。確認お願いします。');
        } catch (Exception $e) {
        }
    }

    /**
     * @test
     */
    public function getBrandOuterTokenByToken_引数がNullの場合() {
        $token = null;
        $res = $this->target->getBrandOuterTokenByToken($token);

        $this->assertNull($res);
    }

    /**
     * @test
     */
    public function getBrandOuterTokenByToken_正常() {
        list($brand, $user, $brand_user_relation) = $this->newBrandToBrandUsersRelation();
        $social_app = $this->entity('SocialApps', [
            'provier' => 101,
            'name' => 'unit_test_101',
        ]);

        $token = $this->makeRandStr();
        $password = $this->makeRandStr(16);
        $brand_outer_token = $this->entity('BrandOuterTokens', [
            'brand_id' => $brand->id,
            'social_app_id' => $social_app->id,
            'user_id' => $user->id,
            'token' => $token,
            'password' => $password,
        ]);

        $data = $this->target->getBrandOuterTokenByToken($token);

        $this->assertSame($brand->id, $data->brand_id);
        $this->assertSame($social_app->id, $data->social_app_id);
        $this->assertSame($user->id, $data->user_id);
        $this->assertSame($token, $data->token);
        $this->assertSame($password, $data->password);
    }

    /**
     * @test
     */
    public function getBrandOuterTokenByTokenAndPassword_引数がNullの場合() {
        $token = null;
        $password = null;
        $res = $this->target->getBrandOuterTokenByTokenAndPassword($token, $password);

        $this->assertNull($res);
    }

    /**
     * @test
     */
    public function getBrandOuterTokenByTokenAndPassword_正常() {
        list($brand, $user, $brand_user_relation) = $this->newBrandToBrandUsersRelation();
        $social_app = $this->entity('SocialApps', [
            'provier' => 102,
            'name' => 'unit_test_102',
        ]);

        $token = $this->makeRandStr();
        $password = $this->makeRandStr(16);
        $brand_outer_token = $this->entity('BrandOuterTokens', [
            'brand_id' => $brand->id,
            'social_app_id' => $social_app->id,
            'user_id' => $user->id,
            'token' => $token,
            'password' => $password,
        ]);

        $data = $this->target->getBrandOuterTokenByTokenAndPassword($token, $password);

        $this->assertSame($brand->id, $data->brand_id);
        $this->assertSame($social_app->id, $data->social_app_id);
        $this->assertSame($user->id, $data->user_id);
        $this->assertSame($token, $data->token);
        $this->assertSame($password, $data->password);
    }

    /**
     * ランダム文字列生成 (英数字)
     *
     * @param string $length 生成する文字数
     * @return string
     */
    private function makeRandStr($length = BrandOuterTokenService::TOKEN_LENGTH) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, 61)];
        }

        return $str;
    }
}
