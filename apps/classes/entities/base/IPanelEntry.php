<?php
/**
 * Created by IntelliJ IDEA.
 * User: sekine-hironori
 * Date: 2014/03/11
 * Time: 13:08
 * To change this template use File | Settings | File Templates.
 */

interface IPanelEntry {

	const ENTRY_PREFIX_TWITTER      = "tw";
	const ENTRY_PREFIX_FACEBOOK     = "fb";
	const ENTRY_PREFIX_LINK         = "li";
	const ENTRY_PREFIX_STATIC_HTML  = "sh";
	const ENTRY_PREFIX_YOUTUBE      = "yt";
    const ENTRY_PREFIX_RSS          = "rs";
    const ENTRY_PREFIX_INSTAGRAM    = "ig";
    const ENTRY_PREFIX_PHOTO        = 'ph';
    const ENTRY_PREFIX_PAGE         = 'pg';
    const ENTRY_PREFIX_CP_INSTAGRAM_HASHTAG = "cpigtg";

    const TARGET_TYPE_NORMAL = 0;
    const TARGET_TYPE_BLANK  = 1;

	public function getEntryPrefix() ;
}