<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class StaticHtmlEntry extends aafwEntityBase implements IPanelEntry {

    protected $_Relations = array(
        'StaticHtmlSnsPlugins' => array(
            'id' => 'static_html_entry_id'
        ),
        'StaticHtmlEntryUsers' => array(
            'id' => 'static_html_entry_id'
        ),
        'StaticHtmlExternalPageLoginTypes' => array(
            'id' => 'static_html_entry_id'
        ),
        'StaticHtmlEmbedEntries' => array(
            'id' => 'static_html_entry_id'
        ),
    );

    const EMBED_PAGE = 1;
    const NOT_EMBED_PAGE = 0;

	public function getEntryPrefix() {
		return self::ENTRY_PREFIX_STATIC_HTML;
	}


	public function getStoreName() {
		return "StaticHtmlEntries";
	}

	public function getServicePrefix(){
		return 'StaticHtmlEntry';
	}

    public function isPublic() {
        return !$this->hidden_flg && strtotime($this->public_date) <= time();
    }

    public function getUrl() {
        return Util::getBaseUrl() . 'page/' . $this->page_url;
    }

    public function getEmbedUrl() {
        return Util::getBaseUrl() . 'embed_page/' . $this->page_url;
    }

    public function getUrlByBrand($brand) {
        return Util::createBaseUrl($brand). 'page/' . $this->page_url;
    }

    public function getBriefBody() {
        $text = html_entity_decode(strip_tags($this->body), ENT_QUOTES, 'UTF-8');

        $text = str_replace( "\xc2\xa0", " ", $text );
        $text = trim(mb_convert_kana($text, "s", 'UTF-8'));
        // 改行、タブをスペースへ
        $text = preg_replace('/[\n\r\t]/', '', $text);
        // 複数スペースを一つに
        $text = preg_replace('/\s(?=\s)/', '', $text);

        return $this->cutLongText($text, 200);
    }

    public function getImageUrl($brand) {
        if ($this->og_image_url) {
            return $this->og_image_url;
        } elseif ($brand && $brand->profile_img_url) {
            return $brand->profile_img_url;
        } else {
            $php_parser = new PHPParser();
            return $php_parser->setVersion('/img/dummy/02.jpg');
        }
    }

    public function isEmbedPage(){
        if($this->embed_flg){
            return true;
        }
        return false;
    }
}
