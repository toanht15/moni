<?php
AAFW::import('jp.aainc.classes.action.APIActionBase');
AAFW::import('jp.aainc.classes.validator.api.BrandValidator');

class get_static_html_entries extends APIActionBase {

    public $allow_methods = [
        self::HTTP_METHOD_GET
    ];

    const DAYS_AGO_MIN = 1;
    const DAYS_AGO_MAX = 14;

    public $Secure = true;

    protected $AllowContent = array('JSON');

    private $page;
    private $count;
    private $days_ago;

    /** @var $validator BrandValidator */
    private $validator;

    /** @var StaticHtmlEntryService $static_html_entry_service * */
    private $static_html_entry_service;

    /** @var StaticHtmlCategoryService $static_html_category_service */
    private $static_html_category_service;

    /**
     * @param $days_ago
     * @return null
     */
    private function getDaysAgo($days_ago) {
        if ($this->isRealInteger($days_ago)) {
            if ($days_ago >= self::DAYS_AGO_MIN && $days_ago < self::DAYS_AGO_MAX) {
                return $days_ago;
            }
        }
        return null;
    }

    public function doThisFirst() {

        $this->page = $this->getPage($this->GET["page"]);
        $this->count = $this->getCount($this->GET["count"]);
        $this->days_ago = $this->getDaysAgo($this->GET["days_ago"]);

        $this->static_html_entry_service = $this->createService('StaticHtmlEntryService');
        $this->static_html_category_service = $this->createService('StaticHtmlCategoryService');

    }

    public function validate() {

        $this->validator = new BrandValidator($this->brand_id);
        $this->validator->validate();
        if (!$this->validator->isValid()) {
            return 404;
        }

        return true;
    }

    function doAction() {

        $brand = $this->validator->getBrand();

        $order = array(
            'name' => "public_date",
            'direction' => "desc",
        );

        $entries = [];

        $static_html_entries = $this->static_html_entry_service->getPublicEntriesByBrandId($brand->id, $this->page, $this->count, $this->days_ago, $order);

        foreach ($static_html_entries as $entry) {

            $entries[] = [
                "id" => $entry->id,
                "title" => $entry->title,
                "body" => $entry->body,
                "url" => $entry->getUrlByBrand($brand),
                "public_date" => $entry->public_date,
                'meta_title' => $entry->meta_title,
                "meta_description" => $entry->meta_description,
                "meta_keyword" => $entry->meta_keyword,
                "og_image_url" => $entry->og_image_url,
                "created_at" => $entry->created_at,
                "updated_at" => $entry->updated_at,
            ];
        }

        // has_next用
        $next_static_html_entries = $this->static_html_entry_service->getPublicEntriesByBrandId($brand->id, $this->page + 1, $this->count, $this->days_ago, $order);
        $has_next = false;
        if (count($next_static_html_entries) > 0) {
            $has_next = true;
        }

        $result = [
            "entries" => $entries,
            "page" => $this->page,
            "count" => $this->count,
            "has_next" => $has_next,
        ];

        $this->assign('json_data', $result);
        return 'dummy.php';

    }
}
