<?php
require_once dirname(__FILE__) . '/../../../config/define.php';
AAFW::import('jp.aainc.t.testcases.BaseTest');
AAFW::import ('jp.aainc.classes.services.StaticHtmlEntryTemplateService');

class StaticHtmlEntryTemplateServiceTest extends BaseTest {

    /** @var  StaticHtmlEntryTemplateService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("StaticHtmlEntryTemplateService");
    }

    /**
     * @test
     * image_sliderのinsert⇛select⇛deleteのテスト
     */
    public function testInsertTemplates_imageslider_success() {

        try {
            $brand = $this->entity('Brands');
            $entry = $this->entity('StaticHtmlEntries', array('brand_id' => $brand->id));

            //insert
            $json = '[{"template":{"item_list":[{"image_url":"https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg","caption":"きゃぷしょん","link":"http://www.yahoo.co.jp"}],"view_type":"2","slider_pc_image_count":"1","slider_sp_image_count":"10"},"type":"1"},{"type":"99"}]';
            $this->target->insertTemplates($entry->id, $json);
            $maps = $this->find("StaticHtmlEntryToTemplateMappings", array("static_html_entry_id" => $entry->id));
            $this->assertEquals(count($maps), 1);
            //insertのテスト
            foreach($maps as $map) {
                foreach($this->find("StaticHtmlImageSliders", array("template_id" => $map->template_id)) as $item) {
                    $this->assertEquals($item->slider_pc_image_count, "1");
                    $this->assertEquals($item->slider_sp_image_count, "10");

                    foreach($this->find("StaticHtmlImageSliderImages", array("static_html_image_slider_id" => $item->id)) as $imageItem) {
                        $this->assertEquals($imageItem->image_url, "https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg");
                        $this->assertEquals($imageItem->caption, "きゃぷしょん");
                        $this->assertEquals($imageItem->link, "http://www.yahoo.co.jp");
                    }
                }
                $this->assertEquals(count($this->find("StaticHtmlFloatImages", array("template_id" => $map->template_id))), 0);
                $this->assertEquals(count($this->find("StaticHtmlFullImages", array("template_id" => $map->template_id))), 0);
                $this->assertEquals(count($this->find("StaticHtmlTextes", array("template_id" => $map->template_id))), 0);
            }

            //select
            $ret = $this->target->getTemplateJsonByEntryId($entry->id);
            $ret = json_decode($ret, true);
            //selectのテスト
            $this->assertEquals($ret[0]['type'], StaticHtmlTemplate::TEMPLATE_TYPE_IMAGE_SLIDER);
            $this->assertEquals($ret[0]['template']['slider_pc_image_count'], "1");
            $this->assertEquals($ret[0]['template']['slider_sp_image_count'], "10");
            $this->assertEquals($ret[0]['template']['item_list'][0]['image_url'], "https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg");
            $this->assertEquals($ret[0]['template']['item_list'][0]['caption'], "きゃぷしょん");
            $this->assertEquals($ret[0]['template']['item_list'][0]['link'], "http://www.yahoo.co.jp");
            $this->assertEquals($ret[1]['type'], StaticHtmlTemplate::TEMPLATE_TYPE_LOGIN_LIMIT_BOUNDARY);

            //delete
            $this->target->deleteExistsTemplates($entry->id);
            //deleteのテスト
            $this->checkDeleted($maps);
        } finally {
            $this->allDBClear($brand, $entry, $maps);
        }
    }

    /**
     * @test
     * float_imageのinsert⇛select⇛deleteのテスト
     */
    public function testInsertTemplates_floatimages_success() {

        try {
            $brand = $this->entity('Brands');
            $entry = $this->entity('StaticHtmlEntries', array('brand_id' => $brand->id));

            //insert
            $this->target->insertTemplates($entry->id, '[{"template":{"image_url":"https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg","position_type":"1","smartphone_float_off_flg":"1","link":"http://www.yahoo.co.jp","text":"てきすと","caption":"きゃぷしょん"},"type":"2"},{"type":"99"}]');
            $maps = $this->find("StaticHtmlEntryToTemplateMappings", array("static_html_entry_id" => $entry->id));
            $this->assertEquals(count($maps), 1);
            //insertのテスト
            foreach($maps as $map) {
                foreach($this->find("StaticHtmlFloatImages", array("template_id" => $map->template_id)) as $item) {
                    $this->assertEquals($item->image_url, "https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg");
                    $this->assertEquals($item->caption, "きゃぷしょん");
                    $this->assertEquals($item->text, "てきすと");
                    $this->assertEquals($item->link, "http://www.yahoo.co.jp");
                    $this->assertEquals($item->smartphone_float_off_flg, 1);
                    $this->assertEquals($item->position_type, 1);
                }
                $this->assertEquals(count($this->find("StaticHtmlImageSliders", array("template_id" => $map->template_id))), 0);
                $this->assertEquals(count($this->find("StaticHtmlFullImages", array("template_id" => $map->template_id))), 0);
                $this->assertEquals(count($this->find("StaticHtmlTextes", array("template_id" => $map->template_id))), 0);
            }

            //select
            $ret = $this->target->getTemplateJsonByEntryId($entry->id);
            $ret = json_decode($ret, true);
            //selectのテスト
            $this->assertEquals($ret[0]['type'], StaticHtmlTemplate::TEMPLATE_TYPE_FLOAT_IMAGE);
            $this->assertEquals($ret[0]['template']['image_url'], "https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg");
            $this->assertEquals($ret[0]['template']['caption'], "きゃぷしょん");
            $this->assertEquals($ret[0]['template']['text'], "てきすと");
            $this->assertEquals($ret[0]['template']['smartphone_float_off_flg'], 1);
            $this->assertEquals($ret[0]['template']['position_type'], 1);
            $this->assertEquals($ret[0]['template']['link'], "http://www.yahoo.co.jp");
            $this->assertEquals($ret[1]['type'], 99);

            //delete
            $this->target->deleteExistsTemplates($entry->id);
            //deleteのテスト
            $this->checkDeleted($maps);
        } finally {
            $this->allDBClear($brand, $entry, $maps);
        }
    }


    /**
     * @test
     * full_imageのinsert⇛select⇛deleteのテスト
     */
    public function testInsertTemplates_fullimages_success() {

        try {
            $brand = $this->entity('Brands');
            $entry = $this->entity('StaticHtmlEntries', array('brand_id' => $brand->id));

            //insert
            $this->target->insertTemplates($entry->id, '[{"template":{"image_url":"https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg","link":"http://www.yahoo.co.jp","caption":"きゃぷしょん"},"type":"3"},{"type":"99"}]');
            $maps = $this->find("StaticHtmlEntryToTemplateMappings", array("static_html_entry_id" => $entry->id));
            $this->assertEquals(count($maps), 1);
            //insertのテスト
            foreach($maps as $map) {
                foreach($this->find("StaticHtmlFullImages", array("template_id" => $map->template_id)) as $item) {
                    $this->assertEquals($item->image_url, "https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg");
                    $this->assertEquals($item->caption, "きゃぷしょん");
                    $this->assertEquals($item->link, "http://www.yahoo.co.jp");
                }
                $this->assertEquals(count($this->find("StaticHtmlImageSliders", array("template_id" => $map->template_id))), 0);
                $this->assertEquals(count($this->find("StaticHtmlFloatImages", array("template_id" => $map->template_id))), 0);
                $this->assertEquals(count($this->find("StaticHtmlTextes", array("template_id" => $map->template_id))), 0);
            }

            //select
            $ret = $this->target->getTemplateJsonByEntryId($entry->id);
            $ret = json_decode($ret, true);
            //selectのテスト
            $this->assertEquals($ret[0]['type'], StaticHtmlTemplate::TEMPLATE_TYPE_FULL_IMAGE);
            $this->assertEquals($ret[0]['template']['image_url'], "https://dev-brandco-parts.s3-ap-northeast-1.amazonaws.com/default_name/image/brand/4e732ced3463d06de0ca9a15b6153677/upload_file/a4d6ef616df2abc28cf0ce31b7b6fe101b8cc45f/11377430_563346753804726_889644607_n.jpg");
            $this->assertEquals($ret[0]['template']['caption'], "きゃぷしょん");
            $this->assertEquals($ret[0]['template']['link'], "http://www.yahoo.co.jp");
            $this->assertEquals($ret[1]['type'], 99);

            //delete
            $this->target->deleteExistsTemplates($entry->id);
            //deleteのテスト
            $this->checkDeleted($maps);
        } finally {
            $this->allDBClear($brand, $entry, $maps);
        }
    }

    /**
     * @test
     * textのinsert⇛select⇛deleteのテスト
     */
    public function testInsertTemplates_textes_success() {

        try {
            $brand = $this->entity('Brands');
            $entry = $this->entity('StaticHtmlEntries', array('brand_id' => $brand->id));

            //insert
            $this->target->insertTemplates($entry->id, '[{"template":{"text":"<p>ぶんしょう<span style=\"line-height: 20.8px;\">ぶんしょうぶんしょう</span></p>\n"},"type":"4"},{"type":"99"}]');
            $maps = $this->find("StaticHtmlEntryToTemplateMappings", array("static_html_entry_id" => $entry->id));
            $this->assertEquals(count($maps), 1);
            //insertのテスト
            foreach($maps as $map) {
                foreach($this->find("StaticHtmlTextes", array("template_id" => $map->template_id)) as $item) {
                    $this->assertEquals($item->text, "<p>ぶんしょう<span style=\"line-height: 20.8px;\">ぶんしょうぶんしょう</span></p>");
                }
                $this->assertEquals(count($this->find("StaticHtmlImageSliders", array("template_id" => $map->template_id))), 0);
                $this->assertEquals(count($this->find("StaticHtmlFloatImages", array("template_id" => $map->template_id))), 0);
                $this->assertEquals(count($this->find("StaticHtmlFullImages", array("template_id" => $map->template_id))), 0);
            }

            //select
            $ret = $this->target->getTemplateJsonByEntryId($entry->id);
            $ret = json_decode($ret, true);
            //selectのテスト
            $this->assertEquals($ret[0]['type'], StaticHtmlTemplate::TEMPLATE_TYPE_TEXT);
            $this->assertEquals($ret[0]['template']['text'], "<p>ぶんしょう<span style=\"line-height: 20.8px;\">ぶんしょうぶんしょう</span></p>");
            $this->assertEquals($ret[1]['type'], 99);

            //delete
            $this->target->deleteExistsTemplates($entry->id);
            //deleteのテスト
            $this->checkDeleted($maps);
        } finally {
            $this->allDBClear($brand, $entry, $maps);
        }
    }

    //deleteされているかの確認(全パーツ共通)
    private function checkDeleted($maps) {
        foreach($maps as $map) {
            $this->assertEquals(count($this->find("StaticHtmlImageSliders", array("template_id" => $map->template_id))), 0);
            $this->assertEquals(count($this->find("StaticHtmlFloatImages", array("template_id" => $map->template_id))), 0);
            $this->assertEquals(count($this->find("StaticHtmlFullImages", array("template_id" => $map->template_id))), 0);
            $this->assertEquals(count($this->find("StaticHtmlTextes", array("template_id" => $map->template_id))), 0);
            $this->assertEquals(count($this->find("StaticHtmlEntryToTemplateMappings", array("id" => $map->id))), 0);
            $this->assertEquals(count($this->find("StaticHtmlTemplates", array("id" => $map->template_id))), 0);
        }
    }


    //最後のテストデータ一括削除
    private function allDBClear($brand, $entry, $maps) {
        foreach($maps as $map) {
            foreach($this->find("StaticHtmlImageSliders", array("template_id" => $map->template_id)) as $item) {
                foreach($this->find("StaticHtmlImageSliderImages", array("static_html_image_slider_id" => $item->id)) as $imageItem) {
                    $this->purge('StaticHtmlImageSliderImages', $imageItem->id);
                }
                $this->purge('StaticHtmlImageSliders', $item->id);
            }
            foreach($this->find("StaticHtmlFloatImages", array("template_id" => $map->template_id)) as $item) {
                $this->purge('StaticHtmlFloatImages', $item->id);
            }
            foreach($this->find("StaticHtmlFullImages", array("template_id" => $map->template_id)) as $item) {
                $this->purge('StaticHtmlFullImages', $item->id);
            }
            foreach($this->find("StaticHtmlTextes", array("template_id" => $map->template_id)) as $item) {
                $this->purge('StaticHtmlTextes', $item->id);
            }

            $this->purge('StaticHtmlEntryToTemplateMappings', $map->id);
            $this->purge('StaticHtmlTemplates', $map->template_id);
        }

        if($entry->id) {
            $this->purge('StaticHtmlEntries', $entry->id);
        }

        if ($brand->id) {
            $this->purge('Brands', $brand->id);
        }

    }
}
