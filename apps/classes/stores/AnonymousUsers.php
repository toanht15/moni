<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class AnonymousUsers extends aafwEntityStoreBase {

    protected $_TableName = 'anonymous_users';
    protected $_EntityName = "AnonymousUser";
}