<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class StaticHtmlEntryTemplateService extends aafwServiceBase {

    private $templates;
    private $mappings;
    private $imageSliders;
    private $imageSliderImages;
    private $fullImages;
    private $floatImages;
    private $textes;
    private $templateModels = array();

    public function __construct() {
        $this->templates         = $this->getModel("StaticHtmlTemplates");
        $this->mappings          = $this->getModel("StaticHtmlEntryToTemplateMappings");
        $this->imageSliders      = $this->getModel("StaticHtmlImageSliders");
        $this->imageSliderImages = $this->getModel("StaticHtmlImageSliderImages");
        $this->fullImages        = $this->getModel("StaticHtmlFullImages");
        $this->floatImages       = $this->getModel("StaticHtmlFloatImages");
        $this->textes            = $this->getModel("StaticHtmlTextes");

        foreach(StaticHtmlTemplate::$template_types as $type => $typeDefine) {
            if($typeDefine['modelName']){
                $this->templateModels[$type] = $this->getModel($typeDefine['modelName']);
            }
        }
    }

    /**
     * @param $static_html_entry_id
     * @return string
     */
    public function getTemplateJsonByEntryId($static_html_entry_id) {
        $ret = array();
        $mappings = $this->mappings->find(array("conditions" => array("static_html_entry_id" => $static_html_entry_id), "order" => "no asc"));
        foreach($mappings as $map) {
            $template = array();
            $templateObj = $this->templates->findOne($map->template_id);
            $template['type'] = $templateObj->type;
            if($this->templateModels[$templateObj->type]){
                $template['template'] = $this->templateModels[$templateObj->type]->getRecordByTemplateId($map->template_id);
            }
            $ret[] = $template;
        }
        return json_encode($ret);
    }

    /**
     * @param $static_html_entry_id
     */
    public function deleteExistsTemplates($static_html_entry_id) {
        $mappings = $this->mappings->find(array("static_html_entry_id" => $static_html_entry_id));
        foreach($mappings as $map) {
            $template_id = $map->template_id;
            $this->mappings->delete($map);
            $this->deleteTemplate($template_id);
        }
    }

    /**
     * @param $template_id
     * @return array|bool
     */
    private function deleteTemplate($template_id) {
        $template = $this->templates->findOne($template_id);
        if($this->templateModels[$template->type]){
            $parts = $this->templateModels[$template->type]->findOne(array('template_id' => $template_id));
            $this->templateModels[$template->type]->delete($parts);
        }
        $this->templates->delete($template);
    }

    /**
     * @param $static_html_entry_id
     * @param $static_html_template_json
     * @return array|bool
     */
    public function insertTemplates($static_html_entry_id, $static_html_template_json) {
        $templateData = json_decode($static_html_template_json);
        $no = 1;
        foreach($templateData as $template) {
            $templateObj = $this->templates->createEmptyObject();
            $templateObj->type = $template->type;
            $templateObj = $this->templates->save($templateObj);
            if($this->templateModels[$template->type]) {
                $this->templateModels[$template->type]->insert($templateObj->id, $template->template);
            }
            $mapObj = $this->mappings->createEmptyObject();
            $mapObj->static_html_entry_id = $static_html_entry_id;
            $mapObj->template_id = $templateObj->id;
            $mapObj->no = $no;
            $this->mappings->save($mapObj);
            $no++;
        }
    }
}
