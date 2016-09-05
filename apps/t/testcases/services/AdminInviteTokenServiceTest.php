<?php

class AdminInviteTokenServiceTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("AdminInviteTokenService");
    }

    public function testAdminInviteTokenToken(){
        $brand = $this->entity("Brands");
        $mail = "mail@mail.com";
        $this->target->inviteAdmin($brand->id,$mail);
        $result = $this->findOne('AdminInviteTokens', array('brand_id' => $brand->id));
        $this->assertEquals($brand->id, $result->brand_id);
    }

    public function testAdminInviteTokenTokenUsedFlg(){
        $brand = $this->entity("Brands");
        $mail = "mail@mail.com";
        $this->target->inviteAdmin($brand->id,$mail);
        $result = $this->findOne('AdminInviteTokens', array('brand_id' => $brand->id));
        $this->target->certificatedToken($result->brand_id,$result->token);
        $used_flg_check = $this->findOne('AdminInviteTokens', array('brand_id' => $brand->id));
        $this->assertEquals(1, $used_flg_check->used_flg);
    }

    public function testAdminInviteTokenInvitedToken(){
        $brand = $this->entity("Brands");
        $mail = "mail@mail.com";
        $this->target->inviteAdmin($brand->id,$mail);
        $result = $this->findOne('AdminInviteTokens', array('brand_id' => $brand->id));
        $token = $this->target->existInviteToken($result->token);
        $this->assertEquals($brand->id, $token->brand_id);
    }

    public function testAdminInviteTokenExistToken(){
        $brand = $this->entity("Brands");
        $mail = "mail@mail.com";
        $this->target->inviteAdmin($brand->id,$mail);
        $result = $this->findOne('AdminInviteTokens', array('brand_id' => $brand->id));
        $exist_token = $this->target->existInviteToken($result->token);
        $this->assertEquals($brand->id,$exist_token->brand_id);
    }

    public function testAdminInviteTokenValidToken(){
        $brand = $this->entity("Brands");
        $mail = "mail@mail.com";
        $this->target->inviteAdmin($brand->id,$mail);
        $result = $this->findOne('AdminInviteTokens', array('brand_id' => $brand->id));
        $token = $this->target->getValidInvitedToken($brand,$result->token);
        $this->assertEquals($result->token,$token);
    }

    public function testAdminInviteTokenCanUseInviteToken(){
        $brand = $this->entity("Brands");
        $mail = "mail@mail.com";
        $this->target->inviteAdmin($brand->id,$mail);
        $result = $this->findOne('AdminInviteTokens', array('brand_id' => $brand->id));
        $token = $this->target->canUseInviteToken($result->token);
        $this->assertEquals(true,$token);
    }


}
