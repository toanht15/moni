<?php
AAFW::import('jp.aainc.classes.services.DashboardService');
AAFW::import('jp.aainc.classes.services.ManagerBrandKpiService');

class getBrandDatePVCountTest extends BaseTest {

    public function testGetBrandPvCount01() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1));
        $brand_kpi_column = $this->findOne("ManagerBrandKpiColumns", array("import" => 'jp.aainc.classes.manager_brand_kpi.BrandsPV'));
        if(!$brand_kpi_column) {
            $brand_kpi_column = $this->entity("ManagerBrandKpiColumns", array("import" => 'jp.aainc.classes.manager_brand_kpi.BrandsPV'));
        }

        $this->entities('ManagerBrandKpiValues', array(
            array('column_id'=> $brand_kpi_column->id,
                'brand_id'=> $brand->id,
                'value' => 11000,
                'summed_date'=> date('Y-m-d', strtotime('2015-04-30'))),
            array('column_id'=> $brand_kpi_column->id,
                'brand_id'=> $brand->id,
                'value' => 12500,
                'summed_date'=> date('Y-m-d', strtotime('2015-05-01'))),
            array('column_id'=> $brand_kpi_column->id,
                'brand_id'=> $brand->id,
                'value' => 10050,
                'summed_date'=> date('Y-m-d', strtotime('2015-05-02')))
        ));

        $condition = array(
            'brand_id' => $brand->id,
            'column_id' => $brand_kpi_column->id,
            'from_date' => date('Y-m-d', strtotime('2015-05-01')),
            'to_date' => date('Y-m-d', strtotime('2015-05-02'))
        );
        $args = array($condition,'','','','');

        $result = $this->getBrandDatePVCount($args[0]);

        $expect_brand_pv_info = array(
            array(
                'summed_date' => '2015-05-01',
                'value' => 12500
            ),
            array(
                'summed_date' => '2015-05-02',
                'value' => 10050
            ),
        );
        $this->assertEquals($expect_brand_pv_info, $result);
    }
}
