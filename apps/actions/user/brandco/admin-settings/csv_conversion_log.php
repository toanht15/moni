<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class csv_conversion_log extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    protected $ContainerName = 'csv_conversion_tag_log';
    private $conversion_id;

    public function doThisFirst() {
        $this->conversion_id = $this->GET['conversion_id'];
    }

    public function validate() {
        if (!$this->conversion_id) return false;
        /** @var ConversionService $conversion_service */
        $conversion_service = $this->getService('ConversionService');
        $conversion = $conversion_service->getConversionById($this->conversion_id);

        if (!$conversion || $conversion->brand_id != $this->getBrand()->id) {
            return false;
        }

        return true;
    }

    public function doAction() {
        try {
            $db = new aafwDataBuilder();

            $condition = array(
                'brand_id'      => $this->getBrand()->id,
                'conversion_id' => $this->conversion_id,
                'CONVERSION_AFTER_REGISTERED' => '__ON__',             //コンバージョンのあと、ブランドに会員を登録したかどうかチェックする
                '__NOFETCH__'   => true
            );

            $rs = $db->getBrandUserConversionByConversionId($condition);

            $is_empty_data = true;
            $data_csv['header'] = array('会員番号', '会員登録日', 'タグ名', 'コンバージョン日時');

            while ($conversion_log = $db->fetch($rs)) {
                $data = array();
                $data[] = $conversion_log['relation_no'];
                $data[] = date('Y/m/d H:i', strtotime($conversion_log['registered_date']));
                $data[] = $conversion_log['conversion_name'];
                $data[] =  date('Y/m/d H:i', strtotime($conversion_log['date_conversion']));

                $data_csv['list'][] = $data;
                $is_empty_data = false;
            }

            if ($is_empty_data) {
                return 'redirect: ' . Util::rewriteUrl('admin-settings', 'conversion_setting_form', array(), array('mid' => 'conversion-download-failed'));
            }

            // Export csv
            $csv = new CSVParser();
            $csv->setCSVFileName($this->conversion_id.'_conversion_log_'.(new DateTime())->format('YmdHis'));
            header("Content-type:" . $csv->getContentType());
            header($csv->getDisposition());
            print mb_convert_encoding($csv->out($data_csv, 1), 'Shift_JIS', "UTF-8");
            exit();

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('csv_conversion_log get error.' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-settings', 'conversion_setting_form', array(), array('mid' => 'conversion-download-error'));
        }
    }
}