<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');

class api_get_draft_cps extends BrandcoPOSTActionBase{

    protected $ContainerName = 'api_get_draft_cps';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    protected $pageLimit = 5;

    public function validate()
    {
        $this->Data['brand'] = $this->getBrand();

        if ($this->isEmpty($this->POST['p']) || !$this->isNumeric($this->POST['p'])) {
            return false;
        }

        return true;
    }

    function doAction()
    {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $cp_container = $cp_flow_service->getDraftCpsByBrandIdAndArchiveFlg( $this->Data['brand']->id,$this->POST['p'], $this->pageLimit, Cp::ARCHIVE_OFF);

        $this->Data['cp_container'] = $cp_container;
        $this->Data['type'] = Cp::SKELETON_DRAFT;

        $parser = new PHPParser();
        $html = $parser->parseTemplate(
            'CpListTemplate.php',
            $this->Data
        );

        $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
} 