<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.InquiryValidator');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class download_inquiry_csv extends BrandcoManagerPOSTActionBase {
    protected $ContainerName = 'download_inquiry_csv';
    protected $Form = array( 'package' => 'inquiry', 'action' => 'download_inquiry_csv');
    protected $logger;
    protected $hipchat_logger;
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    /** @var InquiryBrandService $inquiry_brand_service */
    private $inquiry_brand_service;
    private $column_title_list = array(
        'id' => 'お問い合わせID',
        'created_at' => 'お問い合わせ日付',
        'url' => 'お問い合わせURL',
        'name' => '名前',
        'brand_name' => 'お問い合わせ元',
        'no' => '会員No',
        'monipla_user_id' => 'アライドID',
        'mail_address' => 'メールアドレス',
        'cp_title' => 'キャンペーン名',
        'category' => 'お問い合わせカテゴリ',
        'inquiry_section_id_1' => 'セクション大',
        'inquiry_section_id_2' => 'セクション中',
        'inquiry_section_id_3' => 'セクション小',
        'sender' => '送信者',
        'content' => 'お問い合わせ内容',
        'user_agent' => 'UA',
        'referer' => 'リファラ',
        'status' => 'ステータス',
        'remarks' => '備考',
    );

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();

        $this->inquiry_brand_service = $this->getService('InquiryBrandService');
    }

    public function validate() {
        return true;
    }

    function doAction() {
        try {
            /** @var InquiryService $inquiry_service */
            $inquiry_service = $this->getService('InquiryService');
            $inquiry_list = $inquiry_service->getInquiryListForCSV($this->POST);

            $this->outputCsv($inquiry_list);
        } catch (aafwException $e) {
            $this->logger->error("download_inquiry_csv#doAction can't download");
            $this->logger->error($e);
            $this->hipchat_logger->error("download_inquiry_csv#doAction can't download");

            return 'redirect: ' . Util::rewriteUrl('admin-inquiry', 'show_inquiry_list', array(), array('mid' => 'photo-download-error'), '', true);
        }

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl('admin-inquiry', 'show_inquiry_list', array(), array(), '', true);
    }

    function outputCsv($inquiry_list) {
        try {
            $csv = new CSVParser();
            $csv->setCSVFileName('inquiry_' . (new DateTime())->format('YmdHis'));
            header("Content-type:" . $csv->getContentType());
            header($csv->getDisposition());

            $header = array();
            $rows = array();
            foreach ($inquiry_list as $n_row => $inquiry_message) {
                $row = array();

                foreach ($inquiry_message as $key => $val) {
                    if ($n_row === 0) {
                        $header[] = $this->getColumnTitle($key);
                    }

                    $row[] = $this->getColumnValue($key, $val);
                }

                $rows[] = $row;
            }

            $csv_data = $csv->out(array('data' => $header), 1);
            print mb_convert_encoding($csv_data, 'Shift_JIS', "UTF-8");

            foreach ($rows as $row) {
                $csv_data = $csv->out(array('data' => $row), 1);
                print mb_convert_encoding($csv_data, 'Shift_JIS', "UTF-8");
            }

            exit;
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('download_inquiry_csv#convertToCsv');
            aafwLog4phpLogger::getDefaultLogger()->error($e);
        }
    }

    /**
     * @param $key
     * @return string
     */
    public function getColumnTitle($key) {
        return $this->column_title_list[$key] ?: '未設定項目';
    }

    /**
     * @param $key
     * @param $val
     * @return null
     */
    public function getColumnValue($key, $val) {
        if ($key === 'category') {
            return Inquiry::getCategory($val);
        } else if ($key === 'sender') {
            return InquiryMessage::getSender($val);
        } else if ($key === 'status') {
            return InquiryRoom::getStatus($val);
        } else if ($key === 'url') {
            return Util::rewriteUrl('inquiry', 'show_inquiry', array($val), array(), '', true);
        } else {
            return $val;
        }
    }
}
