<?php

AAFW::import('jp.aainc.classes.util.AddressChecker');

/**
 * Class AddressCheckerTest
 */
class AddressCheckerTest extends PHPUnit_Framework_TestCase {
    /** @var Hash */
    private $gen;

    public function setUp() {
        $this->addressChecker = new AddressChecker();
    }

    /**
     * @test
     * @dataProvider addressNomalTestDataProvider
     */
    public function 重複住所なしの場合に正しくチェックできること($addresses) {
        $src = [];
        foreach ($addresses as $address) {
            $src[] = [
                'user_id' => $address->user_id,
                'address1' => $address->address1,
                'address2' => $address->address2,
                'address3' => $address->address3
            ];
        }
        $dupliHash = $this->addressChecker->checkDuplicate($src);
        $this->assertEquals(0, count($dupliHash));
    }
    /**
     * @test
     * @dataProvider addressTestDataProvider
     */
    public function 重複住所をもつユーザIDが正しくチェックできること($addresses) {
        $src = [];
        foreach ($addresses as $address) {
            $src[] = [
                'user_id' => $address->user_id,
                'address1' => $address->address1,
                'address2' => $address->address2,
                'address3' => $address->address3
            ];
        }
        $dupliHash = $this->addressChecker->checkDuplicate($src);

        $key1 = '１サンノ住所１１サンノ住所２１サンノ住所３';
        $key2 = '２サンノ住所１２サンノ住所２２サンノ住所３';
        $key3 = '３サンノ住所１３サンノ住所２３サンノ住所３';

        $this->assertEquals(3, count($dupliHash));
        $this->assertTrue(array_key_exists($key1, $dupliHash));
        $this->assertTrue(array_key_exists($key2, $dupliHash));
        $this->assertTrue(array_key_exists($key3, $dupliHash));

        $this->assertEquals(2, count($dupliHash[$key1]));
        $this->assertEquals(2, count($dupliHash[$key2]));
        $this->assertEquals(2, count($dupliHash[$key3]));

        $this->assertEquals([1, 4], $dupliHash[$key1]);
        $this->assertEquals([2, 5], $dupliHash[$key2]);
        $this->assertEquals([3, 6], $dupliHash[$key3]);
    }

    /**
     * @return array
     */
    public function addressNomalTestDataProvider() {
        $addresses = [
            [
                [
                    (object)['user_id' => 1, 'address1' => '1さんの住所1', 'address2' => '1さんの住所2', 'address3' => '1さんの住所3'],
                    (object)['user_id' => 2, 'address1' => '2さんの住所1', 'address2' => '2さんの住所2', 'address3' => '2さんの住所3'],
                    (object)['user_id' => 3, 'address1' => '3さんの住所1', 'address2' => '3さんの住所2', 'address3' => '3さんの住所3'],
                    (object)['user_id' => 8, 'address1' => '7さんの住所1', 'address2' => '8さんの住所2', 'address3' => ''],
                ],
            ]
        ];

        return $addresses;
    }

    /**
     * @return array
     */
    public function addressTestDataProvider() {
        $addresses = [
            [
                [
                    (object)['user_id' => 1, 'address1' => '1さんの住所1', 'address2' => '1さんの住所2', 'address3' => '1さんの住所3'],
                    (object)['user_id' => 2, 'address1' => '2さんの住所1', 'address2' => '2さんの住所2', 'address3' => '2さんの住所3'],
                    (object)['user_id' => 3, 'address1' => '3さんの住所1', 'address2' => '3さんの住所2', 'address3' => '3さんの住所3'],
                    (object)['user_id' => 4, 'address1' => '1さんの住所1', 'address2' => '1さんの住所2', 'address3' => '1さんの住所3'],
                    (object)['user_id' => 5, 'address1' => '2さんの住所1', 'address2' => '2さんの住所2', 'address3' => '2さんの住所3'],
                    (object)['user_id' => 6, 'address1' => '3さんの住所1', 'address2' => '3さんの住所2', 'address3' => '3さんの住所3'],
                    (object)['user_id' => 7, 'address1' => '7さんの住所1', 'address2' => '7さんの住所2', 'address3' => '7さんの住所3'],
                    (object)['user_id' => 8, 'address1' => '7さんの住所1', 'address2' => '8さんの住所2', 'address3' => ''],
                ],
            ]
        ];

        return $addresses;
    }
}
