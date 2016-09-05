<?php
/**
 * Created by IntelliJ IDEA.
 * User: sekine-hironori
 * Date: 2014/01/29
 * Time: 9:47
 * To change this template use File | Settings | File Templates.
 */

interface ICrawlerTask {

	function prepare();

	function crawl();

	function finish();

	function doExecute();

}