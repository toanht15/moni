<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class static_html_entries extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_CMS);
	public $NeedAdminLogin = true;
	public $pageLimited = 20;

	public function validate () {

        /** @var StaticHtmlEntryService $static_html_entry_service **/
        $static_html_entry_service = $this->createService('StaticHtmlEntryService');
        /** @var StaticHtmlCategoryService $static_html_category_service */
        $static_html_category_service = $this->createService('StaticHtmlCategoryService');

        $brand = $this->getBrand();
        $this->Data['totalEntriesCount'] = $static_html_entry_service->count($brand->id);

        $total_page = floor ( $this->Data['totalEntriesCount'] / $this->pageLimited ) + ( $this->Data['totalEntriesCount'] % $this->pageLimited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);

        $order = array(
            'name' => "updated_at",
            'direction' => "desc",
        );
        $static_html_entries = $static_html_entry_service->getEntriesByBrandId($brand->id,$this->p,$this->pageLimited,array(),$order);

        foreach($static_html_entries as $entry) {
            $author = $static_html_entry_service->getAuthorStaticHtmlEntry($entry);
            $entry->create_user = $author['create_user'];
            $entry->update_user = $author['update_user'];
            $category_names = array();
            $categories = $static_html_category_service->getCategoryByEntryId($entry->id);
            foreach ($categories as $entry_category) {
                $category_names[] = $static_html_category_service->getCategoryById( $entry_category->category_id)->name;
            }
            $entry->categories = $this->cutLongText( implode(', ',$category_names), 16, '...');
            if (!$entry->categories) {
                $entry->categories = 'なし';
            }
            $update_date = new DateTime($entry->updated_at);
            if ($update_date->format('Y-m-d') === date('Y-m-d')) {
                $entry->updated_at = $update_date->format('H:i');
            } else {
                $entry->updated_at = $update_date->format('Y/m/d');
            }

            $this->Data['staticHtmlEntries'][] = $entry;
        }

        $this->Data['pageLimited'] = $this->pageLimited;

		return true;
	}

	function doAction() {

        $this->Data['can_use_embed_page'] = $this->canAddEmbedPage();
        
		return 'user/brandco/admin-blog/static_html_entries.php';
	}
}