<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import ( 'jp.aainc.classes.entities.StaticHtmlEntry' );
AAFW::import ( 'jp.aainc.classes.entities.StaticHtmlCategory' );

class StaticHtmlEntryCategory extends aafwEntityBase {

    protected $_Relations = array(

        'StaticHtmlEntry' => array(
            'static_html_entry_id' => 'id'
        ),
        'StaticHtmlCategory' => array(
            'category_id' => 'id'
        )
    );

}
