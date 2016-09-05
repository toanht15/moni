<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.util.Hash');
AAFW::import('jp.aainc.classes.util.TokenWithoutSimilarCharGenerator');

class BrandOuterTokenService extends aafwServiceBase {

    const TOKEN_LENGTH = BrandOuterTokens::TOKEN_LENGTH;
    const PASSWORD_LENGTH = 16;
    const SALT_LENGTH = 512;

    /** @var BrandOuterTokens $brand_outer_tokens */
    protected $brand_outer_tokens;

    public function __construct() {
        $this->brand_outer_tokens = $this->getModel('BrandOuterTokens');
        $this->settings = aafwApplicationConfig::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param Brand $brand
     * @param int   $social_app_id
     * @param int   $user_id
     *
     * @return BrandOuterToken
     */
    public function create($brand, $social_app_id, $user_id) {
        $brand_outer_token = $this->brand_outer_tokens->createEmptyObject();
        $token_generator = new TokenWithoutSimilarCharGenerator();
        $hash = new Hash();

        $brand_outer_token->password = $token_generator->generateToken(self::PASSWORD_LENGTH);
        $brand_outer_token->token = $hash->doHash(
            $brand->directory_name,
           $token_generator->generateToken(self::SALT_LENGTH)
        );
        $brand_outer_token->brand_id = $brand->id;
        $brand_outer_token->social_app_id = $social_app_id;
        $brand_outer_token->user_id = $user_id;

        // TODO: 例外処理
        $this->brand_outer_tokens->save($brand_outer_token);

        return $brand_outer_token;
    }

    /**
     * @param String $token
     *
     * @return BrandOuterToken|null
     */
    public function getBrandOuterTokenByToken($token) {
        if (strlen($token) != self::TOKEN_LENGTH) {
            return null;
        }

        return $this->brand_outer_tokens->findOne(['token' => $token]);
    }

    /**
     * @param String $token
     * @param String $password
     *
     * @return BrandOuterToken|null
     */
    public function getBrandOuterTokenByTokenAndPassword($token, $password) {
        if (strlen($token) != self::TOKEN_LENGTH) {
            return null;
        }

        return $this->brand_outer_tokens->findOne([
            'token' => $token,
            'password' => $password
        ]);
    }
}
