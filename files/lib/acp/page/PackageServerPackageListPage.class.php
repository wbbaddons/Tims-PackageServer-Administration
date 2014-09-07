<?php

namespace wcf\acp\page;

use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * Represents a list of all permissions
 * 
 * @author	Joshua Rüsweg
 * @package	be.bastelstu.josh.ps
 * @subpackage	acp.page
 */
class PackageServerPackageListPage extends \wcf\page\AbstractPage {

	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.packageList';

	/**
	 * all packages with versions
	 * @var array<mixed> 
	 */
	public $items = array();

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		$handle = \wcf\util\DirectoryUtil::getInstance(PackageServerUtil::getPackageServerPath());
		$files = $handle->getFileObjects(SORT_ASC, new \wcf\system\Regex('[a-zA-Z]+\.[a-zA-Z]+\.[a-zA-Z]+/.*\.tar'));
		foreach ($files as $file) {
			$package = $file->getPathInfo()->getBasename();
			if (!isset($this->items[$package])) $this->items[$package] = array();
			$this->items[$package][] = substr($file->getBasename(), 0, -4);
		}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
		    'items' => $this->items
		));
	}
}
