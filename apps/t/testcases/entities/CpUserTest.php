<?php

AAFW::import ('jp.aainc.aafw.classes.entities.CpAction');

class CpUserTest extends BaseTest {

  public function testGetCp_whenFound() {
      $brand = $this->entity('Brands');
      $cp = $this->entity('Cps', array('brand_id' => $brand->id));
      $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
      $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));
      $result = $cp_user->getCp();

      $this->assertEquals(array('id' => $cp->id), array('id' => $result->id));
  }

    public function testGetCp_whenFoundOnMaster() {
        $brand = $this->entity('Brands');
        $cp = $this->entity('Cps', array('brand_id' => $brand->id));
        $user = $this->entity('Users', array('monipla_user_id' => $this->max('Users', 'monipla_user_id') + 1));
        $cp_user = $this->entity('CpUsers', array('user_id' => $user->id, 'cp_id' => $cp->id));
        $result = $cp_user->getCp(array('on_master' => true));

        $this->assertEquals(array('id' => $cp->id), array('id' => $result->id));
    }
}