<?php
/**
 * Created by IntelliJ IDEA.
 * User: sekine-hironori
 * Date: 2014/03/04
 * Time: 12:36
 * To change this template use File | Settings | File Templates.
 */

interface PanelService {

	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed
	 */
	public function addEntry($brand, $entry);

	/**
	 * @param $brand
	 * @param $next_entry
	 * @param $entry
	 * @return mixed
	 */
	public function moveEntry($brand, $next_entry, $entry);
	
	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed
	 */
	public function deleteEntry($brand, $entry);


	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed
	 */
	public function fixEntry($brand, $entry);

	/**
	 * @param $brand
	 * @param $entry
	 * @return mixed
	 */
	public function unFixEntry($brand, $entry);

	/**
	 * @param $brand
	 * @param $stream
	 * @param $entry_ids
	 * @return mixed
	 */
	public function addEntriesByStreamAndEntryIds($brand, $stream, $entry_ids);


	/**
	 * @param $brand
	 * @return mixed
	 */
	public function count($brand);

	/**
	 * @param $brand
	 * @param $offset
	 * @param $limit
	 * @return mixed
	 */
	public function getEntriesByOffsetAndLimit($brand, $offset, $limit);
	
	/**
	 * @param $brand
	 * @param $page
	 * @param $count
	 * @return mixed
	 */
	public function getEntriesByPage($brand, $page, $count);


	/**
	 * @param $brand
	 * @param $index
	 * @return mixed
	 */
	public function getEntryByIndex($brand, $index);


    /**
     * @param $entry_value
     * @return mixed
     */
    public function getStreamNameByEntryValue($entry_value);

    /**
     * @param $entry_value
     * @return mixed
     */
    public function getEntryByEntryValue($entry_value);
}