<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class InquiryBrandService extends aafwServiceBase {

    const MODEL_TYPE_INQUIRY_BRAND = 1;
    const MODEL_TYPE_INQUIRY_SECTIONS = 2;
    const MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES = 3;
    const MODEL_TYPE_INQUIRY_TEMPLATES = 4;
    const MODEL_TYPE_INQUIRY_BRAND_RECEIVERS = 5;

    private $models = array();

    public function __construct() {
        $this->models = array(
            self::MODEL_TYPE_INQUIRY_BRAND => $this->getModel('InquiryBrands'),
            self::MODEL_TYPE_INQUIRY_SECTIONS => $this->getModel('InquirySections'),
            self::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES => $this->getModel('InquiryTemplateCategories'),
            self::MODEL_TYPE_INQUIRY_TEMPLATES => $this->getModel('InquiryTemplates'),
            self::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS => $this->getModel('InquiryBrandReceivers'),
        );
    }

    /**
     * @param $model_type
     * @param $filter
     * @return mixed
     */
    public function getRecord($model_type, $filter = array()) {
        return $this->models[$model_type]->findOne(array_merge($filter, array('del_flg' => 0)));
    }

    /**
     * @param $model_type
     * @param $filter
     * @return mixed
     */
    public function getRecords($model_type, $filter = array()) {
        return $this->models[$model_type]->find(array_merge($filter, array('del_flg' => 0)));
    }

    /**
     * @param $model_type
     * @param $filter
     * @return mixed
     */
    public function countRecord($model_type, $filter = array()) {
        return $this->models[$model_type]->count(array_merge($filter, array('del_flg' => 0)));
    }

    /**
     * @param $model_type
     * @param $id
     * @return mixed
     */
    public function deleteRecord($model_type, $id) {
        $record = $this->getRecord($model_type, array('id' => $id));

        if ($record) {
            $this->models[$model_type]->deleteLogical($record);
        }
    }

    /***********************************************************
     * InquiryBrand
     ***********************************************************/
    /**
     * @param $brand_id
     * @param array $data
     * @return mixed
     * @throws aafwException
     */
    public function createInquiryBrand($brand_id, $data = array()) {
        $inquiry_brand = $this->models[self::MODEL_TYPE_INQUIRY_BRAND]->createEmptyObject();
        $inquiry_brand->brand_id = $brand_id;
        $inquiry_brand->aa_alert_flg = ($data['aa_alert_flg']) ?: 0;

        return $this->models[self::MODEL_TYPE_INQUIRY_BRAND]->save($inquiry_brand);
    }

    /**
     * @param $inquiry_brand_id
     * @param array $data
     * @return mixed
     */
    public function updateInquiryBrand($inquiry_brand_id, $data = array()) {
        $inquiry_brand = $this->getRecord(self::MODEL_TYPE_INQUIRY_BRAND, array('id' => $inquiry_brand_id));
        $inquiry_brand->aa_alert_flg = (isset($data['aa_alert_flg'])) ? $data['aa_alert_flg'] : $inquiry_brand->aa_alert_flg;

        return $this->models[self::MODEL_TYPE_INQUIRY_BRAND]->save($inquiry_brand);
    }

    /***********************************************************
     * InquirySection
     ***********************************************************/
    /**
     * @param $inquiry_brand_id
     * @param array $data
     * @return mixed
     * @throws aafwException
     */
    public function createInquirySection($inquiry_brand_id, $data = array()) {
        $inquiry_section = $this->models[self::MODEL_TYPE_INQUIRY_SECTIONS]->createEmptyObject();
        $inquiry_section->inquiry_brand_id = $inquiry_brand_id;
        $inquiry_section->level = ($data['level']) ?: 1;
        $inquiry_section->name = ($data['name']) ?: '';

        return $this->models[self::MODEL_TYPE_INQUIRY_SECTIONS]->save($inquiry_section);
    }

    /**
     * inquiry_brand_idは更新しない
     * @param $inquiry_section_id
     * @param array $data
     * @return mixed
     */
    public function updateInquirySection($inquiry_section_id, $data = array()) {
        $inquiry_section = $this->getRecord(self::MODEL_TYPE_INQUIRY_SECTIONS, array('id' => $inquiry_section_id));
        $inquiry_section->level = $data['level'] ?: $inquiry_section->level;
        $inquiry_section->name = $data['name'] ?: $inquiry_section->name;

        return $this->models[self::MODEL_TYPE_INQUIRY_SECTIONS]->save($inquiry_section);
    }

    /***********************************************************
     * InquiryTemplateCategory
     ***********************************************************/
    /**
     * @param $inquiry_brand_id
     * @param array $data
     * @return mixed
     * @throws aafwException
     */
    public function createInquiryTemplateCategory($inquiry_brand_id, $data = array()) {
        $inquiry_template_category = $this->models[self::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES]->createEmptyObject();
        $inquiry_template_category->inquiry_brand_id = $inquiry_brand_id;
        $inquiry_template_category->name = $data['name'] ?: '';
        $inquiry_template_category->order_no = $data['order_no'] ?: 0;

        return $this->models[self::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES]->save($inquiry_template_category);
    }

    /**
     * @param $inquiry_template_category_id
     * @param array $data
     * @return mixed
     */
    public function updateInquiryTemplateCategory($inquiry_template_category_id, $data = array()) {
        $inquiry_template_category = $this->getRecord(self::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES, array('id' => $inquiry_template_category_id));
        $inquiry_template_category->name = ($data['name']) ?: $inquiry_template_category->name;
        $inquiry_template_category->order_no = ($data['order_no']) ?: $inquiry_template_category->order_no;

        return $this->models[self::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES]->save($inquiry_template_category);
    }

    /***********************************************************
     * InquiryTemplate
     ***********************************************************/
    /**
     * @param $inquiry_brand_id
     * @param $inquiry_template_category_id
     * @param array $data
     * @return mixed
     */
    public function createInquiryTemplate($inquiry_brand_id, $inquiry_template_category_id, $data = array()) {
        $inquiry_template = $this->models[self::MODEL_TYPE_INQUIRY_TEMPLATES]->createEmptyObject();
        $inquiry_template->inquiry_brand_id = $inquiry_brand_id;
        $inquiry_template->inquiry_template_category_id = $inquiry_template_category_id;
        $inquiry_template->name = ($data['name']) ?: '';
        $inquiry_template->content = ($data['content']) ?: '';
        $inquiry_template->order_no = ($data['order_no']) ?: 0;
        return $this->models[self::MODEL_TYPE_INQUIRY_TEMPLATES]->save($inquiry_template);
    }

    /**
     * @param $inquiry_template_id
     * @param array $data
     * @return mixed
     */
    public function updateInquiryTemplate($inquiry_template_id, $data = array()) {
        $inquiry_template = $this->getRecord(self::MODEL_TYPE_INQUIRY_TEMPLATES, array('id' => $inquiry_template_id));
        $inquiry_template->inquiry_template_category_id = ($data['inquiry_template_category_id']) ?: $inquiry_template->inquiry_template_category_id;
        $inquiry_template->name = ($data['name']) ?: $inquiry_template->name;
        $inquiry_template->content = ($data['content']) ?: $inquiry_template->content;
        $inquiry_template->order_no = isset($data['order_no']) ? $data['order_no'] : $inquiry_template->order_no;

        return $this->models[self::MODEL_TYPE_INQUIRY_TEMPLATES]->save($inquiry_template);
    }

    /***********************************************************
     * InquiryBrandReceiver
     ***********************************************************/
    /**
     * @param $inquiry_brand_id
     * @param $data
     * @return mixed
     * @throws aafwException
     */
    public function createInquiryBrandReceiver($inquiry_brand_id, $data) {
        $inquiry_brand_receiver = $this->models[self::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS]->createEmptyObject();
        $inquiry_brand_receiver->inquiry_brand_id = $inquiry_brand_id;
        $inquiry_brand_receiver->mail_address = ($data['mail_address']) ?: '';

        return $this->models[self::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS]->save($inquiry_brand_receiver);
    }

    /***********************************************************
     * getSql
     ***********************************************************/
    /**
     * @param $str
     * @return エスケープ後文字列
     */
    public function escape($str) {
        return $this->models[self::MODEL_TYPE_INQUIRY_BRAND]->escapeForSQL($str);
    }

    /**
     * @param $inquiry_brand_id
     * @return array
     */
    public function getInquiryTemplateCategories($inquiry_brand_id) {
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT
            *
        FROM inquiry_template_categories
        WHERE
            inquiry_brand_id = {$this->escape($inquiry_brand_id)}
            AND del_flg = 0
        ORDER BY order_no ASC
EOS;

        return $db->getBySQL($sql, array());
    }

    /**
     * @param $inquiry_template_category_id
     * @return array
     */
    public function getInquiryTemplates($inquiry_template_category_id) {
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT
            *
        FROM inquiry_templates
        WHERE
            inquiry_template_category_id = {$this->escape($inquiry_template_category_id)}
            AND del_flg = 0
        ORDER BY order_no ASC
EOS;

        return $db->getBySQL($sql, array());
    }

    /**
     * @param $inquiry_brand_id
     * @return array
     */
    public function getInquiryTemplateList($inquiry_brand_id) {
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT
            inquiry_template_categories.id          category_id,
            inquiry_template_categories.name        category_name,
            inquiry_template_categories.order_no    category_order_no,
            inquiry_templates.id                    template_id,
            inquiry_templates.name                  template_name,
            inquiry_templates.content               template_content,
            inquiry_templates.order_no              template_order_no
        FROM inquiry_brands
        INNER JOIN
            inquiry_template_categories ON(
                inquiry_template_categories.inquiry_brand_id = inquiry_brands.id
                AND inquiry_template_categories.del_flg = 0
            )
        LEFT OUTER JOIN
            inquiry_templates ON(
                inquiry_templates.inquiry_template_category_id = inquiry_template_categories.id
                AND inquiry_templates.del_flg = 0
            )
        WHERE
            inquiry_brands.id = {$this->escape($inquiry_brand_id)}
            AND inquiry_brands.del_flg = 0
        ORDER BY inquiry_template_categories.order_no ASC, inquiry_templates.order_no ASC
EOS;

        $result = $db->getBySQL($sql, array());

        $inquiry_template_category_list = array();
        $inquiry_template_list = array();
        foreach ($result as $record) {
            $inquiry_template_category_list[$record['category_order_no']] = array(
                'id' => $record['category_id'],
                'name' => $record['category_name']
            );
            if ($record['template_id']) {
                $inquiry_template_list[$record['category_id']][$record['template_order_no']] = array(
                    'id' => $record['template_id'],
                    'inquiry_template_category_id' => $record['category_id'],
                    'name' => $record['template_name']
                );
            }
        }

        return array(
            'inquiry_template_category_list' => $inquiry_template_category_list,
            'inquiry_template_list' => $inquiry_template_list
        );
    }

    /**
     * @param $inquiry_brand_id
     */
    public function refreshInquiryTemplateCategoryOrderNo($inquiry_brand_id) {
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT
            inquiry_template_categories.*
        FROM inquiry_template_categories
        WHERE
            inquiry_template_categories.inquiry_brand_id = {$this->escape($inquiry_brand_id)}
            AND inquiry_template_categories.del_flg = 0
        ORDER BY inquiry_template_categories.order_no ASC
EOS;

        $inquiry_template_categories = $db->getBySQL($sql, array());

        foreach ($inquiry_template_categories as $index => $inquiry_template_category) {
            $this->updateInquiryTemplateCategory($inquiry_template_category['id'], array(
                'order_no' => $index + 1
            ));

            $this->refreshInquiryTemplateOrderNo($inquiry_template_category['id']);
        }
    }

    /**
     * @param $inquiry_template_category_id
     */
    public function refreshInquiryTemplateOrderNo($inquiry_template_category_id) {
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT
            inquiry_templates.*
        FROM inquiry_templates
        WHERE
            inquiry_templates.inquiry_template_category_id = {$this->escape($inquiry_template_category_id)}
            AND inquiry_templates.del_flg = 0
        ORDER BY inquiry_templates.order_no ASC
EOS;

        $inquiry_templates = $db->getBySQL($sql, array());

        foreach ($inquiry_templates as $index => $inquiry_template) {
            $this->updateInquiryTemplate($inquiry_template['id'], array(
                'order_no' => $index + 1
            ));
        }
    }
}
