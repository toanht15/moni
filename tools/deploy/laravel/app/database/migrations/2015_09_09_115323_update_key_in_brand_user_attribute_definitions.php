<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateKeyInBrandUserAttributeDefinitions extends Migration {

    private $keys = array(
        'EB7wfMDEnHxZ' =>	'1234442913',
        'Dk2p8bBOl7NG' => 	'1804950974',
        'AjXX4aAw0bOu' => 	'857658562',
        'EIjCLqDeVWjJ' =>	'1758635485',
        'CYbK1pAQyGjo' =>	'578837748',
        'ECHeEyBnsVUB' =>	'1518272166',
        'B6UH1ZCdTts5' =>	'276195118',
        'As2pBBBRYyUH' =>	'1296760507',
        'DmuyRCBjrjY9' =>	'418810512',
        'DPzdv8EBRJ9E' =>	'966893249',
        'AoMOHpCHhg88' =>	'1175597465',
        'Bb9Q8iASMAmq' =>	'1054788069',
        'Ad8KFxDUGFqT' =>	'537081279',
        'Ac81QLDRlmfR' =>	'145661070',
        'BVvtFBDlXw20' =>	'220574688',
        'D1oc3OEJCDYs' =>	'1432587507',
        'CbSi3YETZN0p' =>	'940680811',
    );

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            foreach($this->keys as $key => $value) {
                DB::statement('UPDATE brand_user_attribute_definitions SET attribute_key = \''.$value.'\' WHERE attribute_key = \''.$key.'\';');
            }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        foreach($this->keys as $key => $value) {
            DB::statement('UPDATE brand_user_attribute_definitions SET attribute_key = \''.$key.'\' WHERE attribute_key = \''.$value.'\';');
        }
	}

}
