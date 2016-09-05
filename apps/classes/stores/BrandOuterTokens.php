<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class BrandOuterTokens extends aafwEntityStoreBase {

    const TOKEN_LENGTH = 64;

    protected $_TableName = 'brand_outer_tokens';
}
