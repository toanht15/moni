<?php

AAFW::import('jp.aainc.lib.base.aafwValidatorBase');
AAFW::import('jp.aainc.aafw.classes.validator.CpActionDeadLineValidator');

class CpActionDeadLineValidatorTest extends BaseTest {
    private $endType;
    private $endDate;
    private $endHour;
    private $endMinute;

    protected function setUp() {
        $this->endType = 1;
        $this->endDate = '2030/01/01';
        $this->endHour = '10';
        $this->endMinute = '20';
    } 

    /**
     * @test
     */
    public function getValidatorDefinishionColumn_Manager() {
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $this->endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertEquals(4, count(array_keys($validator->getValidatorDefinishionColumn())));
    }

    /**
     * @test
     */
    public function getValidatorDefinishionColumn_NotManager() {
        $isLoginManager = false;
        $validator = new CpActionDeadLineValidator(
            $this->endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertEquals(4, count(array_keys($validator->getValidatorDefinishionColumn())));
    }

    /**
     * @test
     */
    public function getValidatorDefinishionRule_Manager_EndType_0() {
        $endType = 0;
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertEquals(1, count(array_keys($validator->getValidatorDefinishionRule())));
    }

    /**
     * @test
     */
    public function getValidatorDefinishionRule_Manager_EndType_1() {
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $this->endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertEquals(1, count(array_keys($validator->getValidatorDefinishionRule())));
    }

    /**
     * @test
     */
    public function getValidatorDefinishionRule_Manager_EndType_2() {
        $endType = 2;
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertEquals(4, count(array_keys($validator->getValidatorDefinishionRule())));
    }

    /**
     * @test
     */
    public function getValidatorDefinishionRule_NotManager() {
        $isLoginManager = false;
        $validator = new CpActionDeadLineValidator(
            $this->endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertEquals(1, count(array_keys($validator->getValidatorDefinishionRule())));
    }

    /**
     * @test
     */
    public function getValidationColumnAndRule_Manager_EndType_0() {
        $endType = 0;
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $params = $validator->getValidationColumnAndRule();
        $this->assertEquals(4, count(array_keys($params)));
        $this->assertNull($params['end_date']['required']);
        $this->assertNull($params['end_hh']['required']);
        $this->assertNull($params['end_mm']['required']);
    }

    /**
     * @test
     */
    public function getValidationColumnAndRule_Manager_EndType_1() {
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $this->endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $params = $validator->getValidationColumnAndRule();
        $this->assertEquals(4, count(array_keys($params)));
        $this->assertNull($params['end_date']['required']);
        $this->assertNull($params['end_hh']['required']);
        $this->assertNull($params['end_mm']['required']);
    }

    /**
     * @test
     */
    public function getValidationColumnAndRule_Manager_EndType_2() {
        $endType = 2;
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $endType,
            $endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $params = $validator->getValidationColumnAndRule();
        $this->assertEquals(4, count(array_keys($params)));
        $this->assertTrue($params['end_date']['required']);
        $this->assertTrue($params['end_hh']['required']);
        $this->assertTrue($params['end_mm']['required']);
    }

    /**
     * @test
     */
    public function getValidationColumnAndRule_NotManager() {
        $isLoginManager = false;
        $validator = new CpActionDeadLineValidator(
            $this->endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertEquals(4, count(array_keys($validator->getValidationColumnAndRule())));
    }

    /**
     * @test
     */
    public function validate_Manager_EndType_0() {
        $endType = 0;
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertTrue($validator->validate());
    }

    /**
     * @test
     */
    public function validate_Manager_EndType_1() {
        $isLoginManager = true;
        $validator = new CpActionDeadLineValidator(
            $this->endType,
            $this->endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertTrue($validator->validate());
    }

    /**
     * @test
     */
    public function validate_Manager_EndType_2_OK() {
        $endType = 2;
        $isLoginManager = true;
        $endDate = '2030/08/03';
        $validator = new CpActionDeadLineValidator(
            $endType,
            $endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertTrue($validator->validate());
    }

    /**
     * @test
     */
    public function validate_Manager_EndType_2_NG() {
        $endType = 2;
        $isLoginManager = true;
        $endDate = '2010/07/10';
        $validator = new CpActionDeadLineValidator(
            $endType,
            $endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertFalse($validator->validate());
    }

    /**
     * @test
     */
    public function validate_Manager_EndType_2_実在日でない_NG() {
        $endType = 2;
        $isLoginManager = true;
        $endDate = '2030/01/41';
        $validator = new CpActionDeadLineValidator(
            $endType,
            $endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertFalse($validator->validate());
    }

    /**
     * @test
     */
    public function validate_NotManager() {
        $isLoginManager = false;
        $endDate = '2030/08/03';
        $validator = new CpActionDeadLineValidator(
            $endType,
            $endDate,
            $this->endHour,
            $this->endMinute,
            $isLoginManager
        );

        $this->assertTrue($validator->validate());
    }
}
