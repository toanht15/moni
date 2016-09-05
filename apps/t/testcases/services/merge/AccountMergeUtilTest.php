<?php

AAFW::import('jp.aainc.classes.services.merge.AccountMergeUtil');

/**
 * Class AccountMergeUtilTest
 */
class AccountMergeUtilTest extends PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function encodeToken_トークンのエンコード結果正しいか(){
        $result = AccountMergeUtil::encodeToken(1,2,'tw');
        $this->assertEquals("eyJhY2NvdW50X21lcmdlX3N1Z2dlc3Rpb25faWQiOjEsImNwX2lkIjoyLCJjbGllbnRfaWQiOiJ0dyJ9", $result);
    }

    /**
     * @test
     */
    public function decodeToken_トークンのデコード結果正しいか(){
        $result = AccountMergeUtil::decodeToken("eyJhY2NvdW50X21lcmdlX3N1Z2dlc3Rpb25faWQiOjEsImNwX2lkIjoyLCJjbGllbnRfaWQiOiJ0dyJ9");
        $this->assertEquals(1, $result['account_merge_suggestion_id']);
        $this->assertEquals(2, $result['cp_id']);
        $this->assertEquals('tw', $result['client_id']);

    }

}
