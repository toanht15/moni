<?php
/**
 * Created by IntelliJ IDEA.
 * User: le_tung
 * Date: 2014/05/21
 * Time: 12:42
 */

class DatabaseSeeder extends Seeder{
    public function run()
    {
        $value = array(
            array(
                'id' => '1',
                'name' => 'dummy',
                'mail_address' => 'dummy@aainc.co.jp',
                'mail_address_hash' => '0483b25cb809f79850d1536beefaa7d3',
                'password' => '275876e34cf609db118f3d84b799a790',
                'pw_register_date' => date('Y-m-d H:i:s'),
                'pw_expire_date' => date('Y-m-d H:i:s'),
                'pw_expire_mail_send_flg' => '0',
                'login_invalid_count' => '0',
                'login_try_reset_date' => '0000-00-00 00:00:00',
                'login_lockout_reset_date' => '0000-00-00 00:00:00',
                'del_flg' => 0,
                'created_at' => '0000-00-00 00:00:00',
                'updated_at' => '0000-00-00 00:00:00'
            )
        );
        DB::table('managers')->insert( $value );
    }
} 