<?php
require_once dirname(__FILE__) . '/../../../../tools/deploy/GitDeploy.php';

class DeployTest extends BaseTest {

    /** @var GitDeploy $git_deploy */
    private $git_deploy;

    public function setUp() {
        $this->git_deploy = new GitDeploy();
    }

    public function test_migrate_target_product() {
        $args['m'] = 'product';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertTrue($this->git_deploy->isMigrationTarget());
    }

    public function test_migrate_target_staging() {
        $args['m'] = 'staging';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();

        $this->assertTrue($this->git_deploy->isMigrationTarget());
    }

    public function test_migrate_target_stg1() {
        $args['m'] = 'stg1';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertTrue($this->git_deploy->isMigrationTarget());
    }

    public function test_migrate_target_dev_brandco() {
        $args['m'] = 'dev-brandco';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertTrue($this->git_deploy->isMigrationTarget());
    }

    public function test_migrate_target_checking() {
        $args['m'] = 'checking';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertFalse($this->git_deploy->isMigrationTarget());
    }

    public function test_isUpdateCrontabTarget_product() {
        $args['m'] = 'product';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertTrue($this->git_deploy->isUpdateCrontabTarget());
    }

    public function test_isUpdateCrontabTarget_staging() {
        $args['m'] = 'staging';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertTrue($this->git_deploy->isUpdateCrontabTarget());
    }

    public function test_isUpdateCrontabTarget_stg1() {
        $args['m'] = 'stg1';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertFalse($this->git_deploy->isUpdateCrontabTarget());
    }

    public function test_isUpdateCrontabTarget_dev_brandco() {
        $args['m'] = 'dev_brandco';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertFalse($this->git_deploy->isUpdateCrontabTarget());
    }

    public function test_isUpdateCrontabTarget_checking() {
        $args['m'] = 'checking';
        $args['d'] = dirname(__FILE__) . '/../../../../tools/deploy/define.php';
        $this->git_deploy->setArgument($args);
        $this->git_deploy->setDefineParams();
        $this->assertFalse($this->git_deploy->isUpdateCrontabTarget());
    }
}